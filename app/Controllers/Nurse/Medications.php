<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;

class Medications extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('nurse')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get medications for inpatients (show all statuses except cancelled)
        $medications = $db->table('prescriptions pr')
            ->select('pr.*, p.id as patient_id, p.patient_code, p.first_name, p.middle_name, p.last_name,
                a.room_number, a.bed_number,
                m.medicine_name, m.unit, m.stock_quantity,
                d.full_name as doctor_name')
            ->join('patients p', 'p.id = pr.patient_id', 'inner')
            ->join('admissions a', 'a.patient_id = p.id', 'inner')
            ->join('medicines m', 'm.id = pr.medicine_id', 'left')
            ->join('doctors d', 'd.id = pr.doctor_id', 'left')
            ->where('pr.status !=', 'cancelled')
            ->orderBy('pr.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Format patient names
        foreach ($medications as &$medication) {
            $medication['patient_name'] = trim(($medication['first_name'] ?? '') . ' ' . ($medication['middle_name'] ?? '') . ' ' . ($medication['last_name'] ?? ''));
        }
        unset($medication);

        // Calculate statistics
        $totalPending = count(array_filter($medications, fn($m) => ($m['status'] ?? 'pending') === 'pending' || ($m['status'] ?? 'pending') === 'partially_dispensed'));

        $data = [
            'title' => 'Medications | Nurse Panel',
            'medications' => $medications,
            'totalPending' => $totalPending,
        ];

        return view('nurse/medications', $data);
    }

    public function update($prescriptionId)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('nurse')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get the action (dispensed or not_dispensed)
        $action = $this->request->getPost('action');

        if (!in_array($action, ['dispensed', 'not_dispensed'])) {
            session()->setFlashdata('error', 'Invalid action.');
            return redirect()->to(site_url('nurse/medications'));
        }

        // Get prescription details with medicine info
        $prescription = $db->table('prescriptions')
            ->select('prescriptions.*, medicines.stock_quantity, medicines.id as medicine_id, medicines.medicine_name, medicines.unit_price, medicines.unit')
            ->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left')
            ->where('prescriptions.id', $prescriptionId)
            ->get()
            ->getRowArray();

        if (!$prescription) {
            session()->setFlashdata('error', 'Prescription not found.');
            return redirect()->to(site_url('nurse/medications'));
        }

        // Verify this is for an inpatient (nurse can only manage inpatients)
        $isInpatient = $db->table('admissions')
            ->where('patient_id', $prescription['patient_id'])
            ->countAllResults() > 0;

        if (!$isInpatient) {
            session()->setFlashdata('error', 'This prescription is not for an inpatient.');
            return redirect()->to(site_url('nurse/medications'));
        }

        $db->transStart();

        try {
            if ($action === 'dispensed') {
                // Mark as dispensed
                $prescribedQty = (int)$prescription['prescribed_quantity'];
                $dispensedQty = (int)$prescription['dispensed_quantity'];
                $remainingQty = $prescribedQty - $dispensedQty;

                if ($remainingQty <= 0) {
                    session()->setFlashdata('error', 'This prescription is already fully dispensed.');
                    $db->transRollback();
                    return redirect()->to(site_url('nurse/medications'));
                }

                // Check stock availability
                $stockQty = (int)($prescription['stock_quantity'] ?? 0);
                if ($stockQty < $remainingQty) {
                    session()->setFlashdata('error', 'Insufficient stock. Available: ' . $stockQty . ', Required: ' . $remainingQty);
                    $db->transRollback();
                    return redirect()->to(site_url('nurse/medications'));
                }

                // Update prescription
                $newDispensedQty = $prescribedQty;
                $newStatus = 'dispensed';

                $db->table('prescriptions')
                    ->where('id', $prescriptionId)
                    ->update([
                        'dispensed_quantity' => $newDispensedQty,
                        'status' => $newStatus,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Deduct from medicine stock
                $db->table('medicines')
                    ->where('id', $prescription['medicine_id'])
                    ->set('stock_quantity', 'stock_quantity - ' . $remainingQty, false)
                    ->update();

                // Auto-generate bill for pharmacy/medication
                $unitPrice = floatval($prescription['unit_price'] ?? 0);
                $totalAmount = $remainingQty * $unitPrice;
                if ($totalAmount > 0) {
                    $medicineName = $prescription['medicine_name'] ?? 'Medicine';
                    $unit = $prescription['unit'] ?? 'unit';
                    $description = $remainingQty . ' ' . $unit . ' of ' . $medicineName;
                    $this->generateBill($prescription['patient_id'], 'Pharmacy', $totalAmount, $description);
                }

                session()->setFlashdata('success', 'Prescription marked as dispensed successfully.');
            } else {
                // Mark as not dispensed (reset to pending)
                $db->table('prescriptions')
                    ->where('id', $prescriptionId)
                    ->update([
                        'dispensed_quantity' => 0,
                        'status' => 'pending',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Restore medicine stock if it was previously dispensed
                $previouslyDispensed = (int)$prescription['dispensed_quantity'];
                if ($previouslyDispensed > 0) {
                    $db->table('medicines')
                        ->where('id', $prescription['medicine_id'])
                        ->set('stock_quantity', 'stock_quantity + ' . $previouslyDispensed, false)
                        ->update();
                }

                session()->setFlashdata('success', 'Prescription marked as not dispensed.');
            }

            if ($db->transComplete() === false) {
                session()->setFlashdata('error', 'Failed to update prescription. Please try again.');
                return redirect()->to(site_url('nurse/medications'));
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating prescription: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while updating the prescription.');
            return redirect()->to(site_url('nurse/medications'));
        }

        return redirect()->to(site_url('nurse/medications'));
    }
}

