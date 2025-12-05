<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;

class Registration extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        $db = db_connect();
        // Basic patient list with inferred visit type (inpatient if admission record exists)
        $builder = $db->table('patients p')
            ->select('p.id, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, a.id AS admission_id')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->orderBy('p.id', 'DESC');

        $patients = $builder->get()->getResultArray();
        foreach ($patients as &$patient) {
            $patient['visit_type'] = !empty($patient['admission_id']) ? 'inpatient' : 'outpatient';
        }
        unset($patient);

        $data = [
            'title' => 'Patient Registration & EHR | HMS System',
            'patients' => $patients,
        ];

        return view('patients/registration', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        $request = $this->request;
        $visitType = $request->getPost('visit_type') ?: 'outpatient';

        $db = db_connect();
        $db->transStart();

        // Core patient
        $patientData = [
            'first_name'     => $request->getPost('first_name'),
            'middle_name'    => $request->getPost('middle_name'),
            'last_name'      => $request->getPost('last_name'),
            'date_of_birth'  => $request->getPost('date_of_birth') ?: null,
            'gender'         => $request->getPost('gender') ?: null,
            'civil_status'   => $request->getPost('civil_status') ?: null,
            'place_of_birth' => $request->getPost('place_of_birth') ?: null,
            'blood_type'     => $request->getPost('blood_type') ?: null,
        ];

        $db->table('patients')->insert($patientData);
        $patientId = $db->insertID();

        // Contact information
        $contactData = [
            'patient_id'        => $patientId,
            'province'          => $request->getPost('province') ?: null,
            'city_municipality' => $request->getPost('city_municipality') ?: null,
            'barangay'          => $request->getPost('barangay') ?: null,
            'street'            => $request->getPost('street') ?: null,
            'phone_number'      => $request->getPost('phone_number') ?: null,
            'mobile_number'     => $request->getPost('mobile_number') ?: null,
            'email'             => $request->getPost('email') ?: null,
        ];
        $db->table('patient_contacts')->insert($contactData);

        // Emergency contact
        $emergencyData = [
            'patient_id'      => $patientId,
            'contact_person'  => $request->getPost('emergency_contact_person') ?: null,
            'relationship'    => $request->getPost('emergency_relationship') ?: null,
            'contact_number'  => $request->getPost('emergency_contact_number') ?: null,
        ];
        $db->table('patient_emergency_contacts')->insert($emergencyData);

        // Vitals with BMI
        $height = (float) ($request->getPost('height_cm') ?: 0);
        $weight = (float) ($request->getPost('weight_kg') ?: 0);
        $bmi    = null;
        if ($height > 0 && $weight > 0) {
            $meters = $height / 100.0;
            $bmi = $weight / ($meters * $meters);
        }

        $vitalsData = [
            'patient_id'     => $patientId,
            'blood_pressure' => $request->getPost('blood_pressure') ?: null,
            'heart_rate'     => $request->getPost('heart_rate') ?: null,
            'temperature'    => $request->getPost('temperature') ?: null,
            'height_cm'      => $height ?: null,
            'weight_kg'      => $weight ?: null,
            'bmi'            => $bmi !== null ? number_format($bmi, 2, '.', '') : null,
        ];
        $db->table('patient_vitals')->insert($vitalsData);

        // Insurance
        $insuranceData = [
            'patient_id'               => $patientId,
            'provider_name'            => $request->getPost('insurance_provider') ?: null,
            'provider_contact_number'  => $request->getPost('insurance_contact_number') ?: null,
            'policy_number'            => $request->getPost('policy_number') ?: null,
        ];
        $db->table('patient_insurances')->insert($insuranceData);

        // Medical notes
        $notes = trim((string) $request->getPost('medical_notes'));
        if ($notes !== '') {
            $notesData = [
                'patient_id' => $patientId,
                'notes'      => $notes,
            ];
            $db->table('patient_notes')->insert($notesData);
        }

        // Admission details only for inpatient
        if ($visitType === 'inpatient') {
            $admissionDate = $request->getPost('admission_date');
            $admissionTime = $request->getPost('admission_time');
            $admissionDateTime = null;
            if ($admissionDate && $admissionTime) {
                $admissionDateTime = $admissionDate . ' ' . $admissionTime . ':00';
            }

            $admissionData = [
                'patient_id'         => $patientId,
                'admission_datetime' => $admissionDateTime,
                'room_type'          => $request->getPost('room_type') ?: null,
                'room_number'        => $request->getPost('room_number') ?: null,
                'bed_number'         => $request->getPost('bed_number') ?: null,
            ];
            $db->table('admissions')->insert($admissionData);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Unable to save patient record. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->session->setFlashdata('success', 'Patient record has been saved successfully.');
        return redirect()->to(base_url('patients/register'));
    }
}
