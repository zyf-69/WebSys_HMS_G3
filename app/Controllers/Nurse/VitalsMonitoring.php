<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;

class VitalsMonitoring extends BaseController
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

        // Get all inpatients
        $patients = $db->table('admissions a')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name,
                a.room_number, a.bed_number,
                pv.id as vitals_id, pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->orderBy('a.room_number', 'ASC')
            ->orderBy('a.bed_number', 'ASC')
            ->get()
            ->getResultArray();

        // Format patient data
        foreach ($patients as &$patient) {
            $patient['full_name'] = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
        }
        unset($patient);

        $data = [
            'title' => 'Vitals Monitoring | Nurse Panel',
            'patients' => $patients,
        ];

        return view('nurse/vitals_monitoring', $data);
    }

    public function update()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('nurse')) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('nurse/vitals-monitoring'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('nurse/vitals-monitoring'));
        }

        $patientId = (int) $request->getPost('patient_id');
        $db = db_connect();

        // Verify patient is an inpatient
        $admission = $db->table('admissions')
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();

        if (!$admission) {
            $this->session->setFlashdata('error', 'Patient not found or not an inpatient.');
            return redirect()->to(site_url('nurse/vitals-monitoring'));
        }

        $vitalsData = [
            'blood_pressure' => trim($request->getPost('blood_pressure') ?? ''),
            'heart_rate' => (int) ($request->getPost('heart_rate') ?: 0),
            'temperature' => (float) ($request->getPost('temperature') ?: 0),
            'height_cm' => (float) ($request->getPost('height_cm') ?: 0),
            'weight_kg' => (float) ($request->getPost('weight_kg') ?: 0),
        ];

        // Calculate BMI if height and weight are provided
        if ($vitalsData['height_cm'] > 0 && $vitalsData['weight_kg'] > 0) {
            $heightM = $vitalsData['height_cm'] / 100;
            $vitalsData['bmi'] = round($vitalsData['weight_kg'] / ($heightM * $heightM), 1);
        }

        // Check if vitals record exists
        $existingVitals = $db->table('patient_vitals')
            ->where('patient_id', $patientId)
            ->get()
            ->getRowArray();

        try {
            if ($existingVitals) {
                $db->table('patient_vitals')
                    ->where('patient_id', $patientId)
                    ->update($vitalsData);
            } else {
                $vitalsData['patient_id'] = $patientId;
                $db->table('patient_vitals')->insert($vitalsData);
            }

            $this->session->setFlashdata('success', 'Vital signs updated successfully.');
            return redirect()->to(site_url('nurse/vitals-monitoring'));
        } catch (\Exception $e) {
            log_message('error', 'Vitals update failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Failed to update vital signs. Please try again.');
            return redirect()->to(site_url('nurse/vitals-monitoring'));
        }
    }
}

