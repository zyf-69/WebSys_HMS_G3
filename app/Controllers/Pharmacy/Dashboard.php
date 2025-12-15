<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('pharmacist')) {
            $this->session->setFlashdata('error', 'You do not have permission to access this page.');
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Get all medicines for inventory section
        $medicines = $db->table('medicines')
            ->select('id, medicine_name, category, stock_quantity, unit_price, unit, status, created_at, updated_at')
            ->where('status', 'active')
            ->orderBy('medicine_name', 'ASC')
            ->get()
            ->getResultArray();

        // Calculate total value for each medicine
        foreach ($medicines as &$medicine) {
            $medicine['total_value'] = (float) $medicine['stock_quantity'] * (float) $medicine['unit_price'];
            $medicine['is_out_of_stock'] = $medicine['stock_quantity'] <= 0;
            $medicine['is_low_stock'] = $medicine['stock_quantity'] > 0 && $medicine['stock_quantity'] < 10;
        }
        unset($medicine);

        // Calculate statistics for medicines
        $totalMedicines = count($medicines);
        $outOfStockCount = count(array_filter($medicines, function($m) { return $m['is_out_of_stock']; }));
        $lowStockCount = count(array_filter($medicines, function($m) { return $m['is_low_stock']; }));
        $totalInventoryValue = array_sum(array_column($medicines, 'total_value'));

        // Get all prescriptions with related data
        $prescriptions = $db->table('prescriptions p')
            ->select('p.id, p.patient_id, p.doctor_id, p.medicine_id, p.prescribed_quantity, p.dispensed_quantity, p.status, p.prescription_date, p.notes, p.created_at,
                     pt.first_name as patient_first_name, pt.middle_name as patient_middle_name, pt.last_name as patient_last_name,
                     d.full_name as doctor_name, d.specialization,
                     m.medicine_name, m.category as medicine_category, m.stock_quantity as medicine_stock, m.unit as medicine_unit, m.unit_price')
            ->join('patients pt', 'pt.id = p.patient_id', 'left')
            ->join('doctors d', 'd.id = p.doctor_id', 'left')
            ->join('medicines m', 'm.id = p.medicine_id', 'left')
            ->orderBy('p.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Process prescriptions data
        foreach ($prescriptions as &$prescription) {
            // Build patient full name
            $patientName = trim(($prescription['patient_first_name'] ?? '') . ' ' . 
                               ($prescription['patient_middle_name'] ?? '') . ' ' . 
                               ($prescription['patient_last_name'] ?? ''));
            $prescription['patient_name'] = $patientName ?: 'Unknown Patient';
            
            // Calculate remaining quantity to dispense
            $prescription['remaining_quantity'] = $prescription['prescribed_quantity'] - $prescription['dispensed_quantity'];
            
            // Check if stock is sufficient
            $prescription['is_stock_sufficient'] = $prescription['medicine_stock'] >= $prescription['remaining_quantity'];
            $prescription['stock_shortage'] = max(0, $prescription['remaining_quantity'] - $prescription['medicine_stock']);
        }
        unset($prescription);

        // Count prescriptions by status
        $pendingPrescriptions = count(array_filter($prescriptions, function($p) { return $p['status'] === 'pending'; }));
        $partiallyDispensed = count(array_filter($prescriptions, function($p) { return $p['status'] === 'partially_dispensed'; }));

        $data = [
            'title' => 'Pharmacy Dashboard | HMS System',
            'medicines' => $medicines,
            'prescriptions' => $prescriptions,
            'totalMedicines' => $totalMedicines,
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'totalInventoryValue' => $totalInventoryValue,
            'pendingPrescriptions' => $pendingPrescriptions,
            'partiallyDispensed' => $partiallyDispensed,
        ];

        return view('pharmacy/dashboard', $data);
    }

    public function dispense()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('pharmacist')) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(base_url('pharmacy/dashboard'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(base_url('pharmacy/dashboard'));
        }

        $prescriptionId = (int) $request->getPost('prescription_id');
        $dispenseQuantity = (int) $request->getPost('dispense_quantity');

        if (!$prescriptionId || $dispenseQuantity <= 0) {
            $this->session->setFlashdata('error', 'Invalid prescription ID or quantity.');
            return redirect()->to(base_url('pharmacy/dashboard'));
        }

        $db = db_connect();
        $db->transStart();

        try {
            // Get prescription with medicine details
            $prescription = $db->table('prescriptions p')
                ->select('p.*, m.stock_quantity as medicine_stock, m.medicine_name, m.unit_price, m.unit')
                ->join('medicines m', 'm.id = p.medicine_id', 'left')
                ->where('p.id', $prescriptionId)
                ->get()
                ->getRowArray();

            if (!$prescription) {
                throw new \Exception('Prescription not found.');
            }

            // Calculate remaining quantity to dispense
            $remainingQuantity = $prescription['prescribed_quantity'] - $prescription['dispensed_quantity'];

            if ($dispenseQuantity > $remainingQuantity) {
                throw new \Exception('Dispense quantity cannot exceed remaining prescribed quantity.');
            }

            // Check if stock is sufficient
            if ($prescription['medicine_stock'] < $dispenseQuantity) {
                throw new \Exception(sprintf(
                    'Insufficient stock. Available: %d, Required: %d',
                    $prescription['medicine_stock'],
                    $dispenseQuantity
                ));
            }

            // Update prescription
            $newDispensedQuantity = $prescription['dispensed_quantity'] + $dispenseQuantity;
            $newStatus = 'dispensed';
            if ($newDispensedQuantity < $prescription['prescribed_quantity']) {
                $newStatus = 'partially_dispensed';
            }

            $db->table('prescriptions')
                ->where('id', $prescriptionId)
                ->update([
                    'dispensed_quantity' => $newDispensedQuantity,
                    'status' => $newStatus,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Decrease medicine stock
            $newStock = $prescription['medicine_stock'] - $dispenseQuantity;
            $db->table('medicines')
                ->where('id', $prescription['medicine_id'])
                ->update([
                    'stock_quantity' => $newStock,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Auto-generate bill for pharmacy/medication
            $unitPrice = floatval($prescription['unit_price'] ?? 0);
            $totalAmount = $dispenseQuantity * $unitPrice;
            if ($totalAmount > 0) {
                $medicineName = $prescription['medicine_name'] ?? 'Medicine';
                $unit = $prescription['unit'] ?? 'unit';
                $description = $dispenseQuantity . ' ' . $unit . ' of ' . $medicineName;
                $this->generateBill($prescription['patient_id'], 'Pharmacy', $totalAmount, $description);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            $this->session->setFlashdata('success', sprintf(
                'Successfully dispensed %d %s of %s.',
                $dispenseQuantity,
                'units',
                $prescription['medicine_name']
            ));

        } catch (\Exception $e) {
            $db->transRollback();
            $this->session->setFlashdata('error', $e->getMessage());
        }

        return redirect()->to(base_url('pharmacy/dashboard'));
    }
}
