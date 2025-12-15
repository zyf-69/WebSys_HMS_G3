<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;

class AssignedPatients extends BaseController
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

        // Get all inpatients (patients with admissions)
        $patients = $db->table('admissions a')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.blood_type,
                a.admission_datetime, a.room_type, a.room_number, a.bed_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                d.full_name as doctor_name, d.specialization')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('doctors d', 'd.id = a.doctor_id', 'left')
            ->orderBy('a.admission_datetime', 'DESC')
            ->get()
            ->getResultArray();

        // Format patient data
        foreach ($patients as &$patient) {
            $patient['full_name'] = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
            if ($patient['date_of_birth']) {
                $birthDate = new \DateTime($patient['date_of_birth']);
                $today = new \DateTime();
                $patient['age'] = $today->diff($birthDate)->y;
            } else {
                $patient['age'] = null;
            }
        }
        unset($patient);

        $data = [
            'title' => 'Assigned Patients | Nurse Panel',
            'patients' => $patients,
        ];

        return view('nurse/assigned_patients', $data);
    }
}

