<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;

class PrescriptionManagement extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('receptionist')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get all prescriptions with patient, doctor, and medicine information
        $prescriptions = $db->table('prescriptions pr')
            ->select('pr.*, 
                p.id as patient_id, p.first_name, p.middle_name, p.last_name, p.patient_code,
                d.full_name as doctor_name, d.specialization,
                m.medicine_name, m.unit, m.stock_quantity')
            ->join('patients p', 'p.id = pr.patient_id', 'left')
            ->join('doctors d', 'd.id = pr.doctor_id', 'left')
            ->join('medicines m', 'm.id = pr.medicine_id', 'left')
            ->orderBy('pr.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Format patient names
        foreach ($prescriptions as &$prescription) {
            $prescription['patient_name'] = trim(($prescription['first_name'] ?? '') . ' ' . ($prescription['middle_name'] ?? '') . ' ' . ($prescription['last_name'] ?? ''));
        }
        unset($prescription);

        // Calculate statistics
        $totalPrescriptions = count($prescriptions);
        $pendingPrescriptions = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? 'pending') === 'pending'));
        $dispensedPrescriptions = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? '') === 'dispensed'));
        $partiallyDispensed = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? '') === 'partially_dispensed'));

        $data = [
            'title' => 'Prescription Management | Receptionist Panel',
            'prescriptions' => $prescriptions,
            'totalPrescriptions' => $totalPrescriptions,
            'pendingPrescriptions' => $pendingPrescriptions,
            'dispensedPrescriptions' => $dispensedPrescriptions,
            'partiallyDispensed' => $partiallyDispensed,
        ];

        return view('receptionist/prescription_management', $data);
    }
}

