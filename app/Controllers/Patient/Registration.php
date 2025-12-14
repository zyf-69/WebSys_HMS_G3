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

        // Get all active doctors for dropdown
        $doctors = $db->table('doctors')
            ->select('id, full_name, specialization, license_number')
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Patient Registration & EHR | HMS System',
            'patients' => $patients,
            'doctors' => $doctors,
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
        $now = date('Y-m-d H:i:s');
        $patientData = [
            'first_name'     => $request->getPost('first_name'),
            'middle_name'    => $request->getPost('middle_name'),
            'last_name'      => $request->getPost('last_name'),
            'date_of_birth'  => $request->getPost('date_of_birth') ?: null,
            'gender'         => $request->getPost('gender') ?: null,
            'civil_status'   => $request->getPost('civil_status') ?: null,
            'place_of_birth' => $request->getPost('place_of_birth') ?: null,
            'blood_type'     => $request->getPost('blood_type') ?: null,
            'created_at'     => $now,
            'updated_at'     => $now,
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

        // Doctor assignment - different field names for inpatient vs outpatient
        $doctorId = (int) ($visitType === 'inpatient' 
            ? $request->getPost('inpatient_doctor_id') 
            : $request->getPost('outpatient_doctor_id'));
        
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
                'doctor_id'          => $doctorId > 0 ? $doctorId : null,
                'admission_datetime' => $admissionDateTime,
                'room_type'          => $request->getPost('room_type') ?: null,
                'room_number'        => $request->getPost('room_number') ?: null,
                'bed_number'         => $request->getPost('bed_number') ?: null,
            ];
            $db->table('admissions')->insert($admissionData);
        } else {
            // For outpatient, save doctor_id directly to patients table
            if ($doctorId > 0) {
                $db->table('patients')->where('id', $patientId)->update(['doctor_id' => $doctorId]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Unable to save patient record. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->session->setFlashdata('success', 'Patient record has been saved successfully.');
        return redirect()->to(base_url('patients/register'));
    }

    public function records()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        $db = db_connect();
        
        // Get all patients with their related information
        $builder = $db->table('patients p')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.civil_status, p.place_of_birth, p.blood_type,
                p.created_at as registered_date,
                pc.phone_number, pc.mobile_number, pc.email,
                pec.contact_person as emergency_contact, pec.relationship, pec.contact_number as emergency_contact_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                a.admission_datetime, a.room_type, a.room_number, a.bed_number')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->orderBy('p.created_at', 'DESC');

        $patients = $builder->get()->getResultArray();

        // Format the data for display
        foreach ($patients as &$patient) {
            $patient['visit_type'] = !empty($patient['admission_datetime']) ? 'Inpatient' : 'Outpatient';
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
            'title' => 'Patient Records | HMS System',
            'patients' => $patients,
            'isAdmin' => $this->hasRole(['admin', 'hospital_administrator']),
        ];

        return view('patients/records', $data);
    }

    public function edit($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        // Only admin can edit patient records
        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to edit patient records.');
            return redirect()->to(base_url('patients/records'));
        }

        $db = db_connect();
        
        // Get patient with all related information
        $patient = $db->table('patients p')
            ->select('p.*,
                pc.id as contact_id, pc.province, pc.city_municipality, pc.barangay, pc.phone_number, pc.mobile_number, pc.email,
                pec.id as emergency_id, pec.contact_person as emergency_contact_person, pec.relationship as emergency_relationship, pec.contact_number as emergency_contact_number,
                pv.id as vitals_id, pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                pi.id as insurance_id, pi.provider_name as insurance_provider, pi.provider_contact_number as insurance_contact_number, pi.policy_number,
                pn.id as notes_id, pn.notes as medical_notes,
                a.id as admission_id, a.admission_datetime, a.room_type, a.room_number, a.bed_number, a.doctor_id as admission_doctor_id')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('patient_insurances pi', 'pi.patient_id = p.id', 'left')
            ->join('patient_notes pn', 'pn.patient_id = p.id', 'left')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        if (!$patient) {
            $this->session->setFlashdata('error', 'Patient record not found.');
            return redirect()->to(base_url('patients/records'));
        }

        // Format admission date and time
        if (!empty($patient['admission_datetime'])) {
            $admissionDT = new \DateTime($patient['admission_datetime']);
            $patient['admission_date'] = $admissionDT->format('Y-m-d');
            $patient['admission_time'] = $admissionDT->format('H:i');
            $patient['visit_type'] = 'inpatient';
        } else {
            $patient['admission_date'] = null;
            $patient['admission_time'] = null;
            $patient['visit_type'] = 'outpatient';
        }

        // Get all active doctors for dropdown
        $doctors = $db->table('doctors')
            ->select('id, full_name, specialization, license_number')
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Edit Patient Record | HMS System',
            'patient' => $patient,
            'doctors' => $doctors,
        ];

        return view('patients/edit', $data);
    }

    public function update($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        // Only admin can update patient records
        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to edit patient records.');
            return redirect()->to(base_url('patients/records'));
        }

        $request = $this->request;
        $visitType = $request->getPost('visit_type') ?: 'outpatient';

        $db = db_connect();
        $db->transStart();

        // Check if patient exists
        $patient = $db->table('patients')->where('id', $id)->get()->getRowArray();
        if (!$patient) {
            $this->session->setFlashdata('error', 'Patient record not found.');
            return redirect()->to(base_url('patients/records'));
        }

        // Update core patient
        $now = date('Y-m-d H:i:s');
        $patientData = [
            'first_name'     => $request->getPost('first_name'),
            'middle_name'    => $request->getPost('middle_name'),
            'last_name'      => $request->getPost('last_name'),
            'date_of_birth'  => $request->getPost('date_of_birth') ?: null,
            'gender'         => $request->getPost('gender') ?: null,
            'civil_status'   => $request->getPost('civil_status') ?: null,
            'place_of_birth' => $request->getPost('place_of_birth') ?: null,
            'blood_type'     => $request->getPost('blood_type') ?: null,
            'updated_at'     => $now,
        ];
        $db->table('patients')->where('id', $id)->update($patientData);

        // Update or insert contact information
        $contactData = [
            'province'          => $request->getPost('province') ?: null,
            'city_municipality' => $request->getPost('city_municipality') ?: null,
            'barangay'          => $request->getPost('barangay') ?: null,
            'phone_number'      => $request->getPost('phone_number') ?: null,
            'mobile_number'     => $request->getPost('mobile_number') ?: null,
            'email'             => $request->getPost('email') ?: null,
        ];
        $existingContact = $db->table('patient_contacts')->where('patient_id', $id)->get()->getRowArray();
        if ($existingContact) {
            $db->table('patient_contacts')->where('patient_id', $id)->update($contactData);
        } else {
            $contactData['patient_id'] = $id;
            $db->table('patient_contacts')->insert($contactData);
        }

        // Update or insert emergency contact
        $emergencyData = [
            'contact_person'  => $request->getPost('emergency_contact_person') ?: null,
            'relationship'    => $request->getPost('emergency_relationship') ?: null,
            'contact_number'  => $request->getPost('emergency_contact_number') ?: null,
        ];
        $existingEmergency = $db->table('patient_emergency_contacts')->where('patient_id', $id)->get()->getRowArray();
        if ($existingEmergency) {
            $db->table('patient_emergency_contacts')->where('patient_id', $id)->update($emergencyData);
        } else {
            $emergencyData['patient_id'] = $id;
            $db->table('patient_emergency_contacts')->insert($emergencyData);
        }

        // Update or insert vitals
        $height = (float) ($request->getPost('height_cm') ?: 0);
        $weight = (float) ($request->getPost('weight_kg') ?: 0);
        $bmi = null;
        if ($height > 0 && $weight > 0) {
            $meters = $height / 100.0;
            $bmi = $weight / ($meters * $meters);
        }

        $vitalsData = [
            'blood_pressure' => $request->getPost('blood_pressure') ?: null,
            'heart_rate'     => $request->getPost('heart_rate') ?: null,
            'temperature'    => $request->getPost('temperature') ?: null,
            'height_cm'      => $height ?: null,
            'weight_kg'      => $weight ?: null,
            'bmi'            => $bmi !== null ? number_format($bmi, 2, '.', '') : null,
        ];
        $existingVitals = $db->table('patient_vitals')->where('patient_id', $id)->get()->getRowArray();
        if ($existingVitals) {
            $db->table('patient_vitals')->where('patient_id', $id)->update($vitalsData);
        } else {
            $vitalsData['patient_id'] = $id;
            $db->table('patient_vitals')->insert($vitalsData);
        }

        // Update or insert insurance
        $insuranceData = [
            'provider_name'            => $request->getPost('insurance_provider') ?: null,
            'provider_contact_number'  => $request->getPost('insurance_contact_number') ?: null,
            'policy_number'            => $request->getPost('policy_number') ?: null,
        ];
        $existingInsurance = $db->table('patient_insurances')->where('patient_id', $id)->get()->getRowArray();
        if ($existingInsurance) {
            $db->table('patient_insurances')->where('patient_id', $id)->update($insuranceData);
        } else {
            $insuranceData['patient_id'] = $id;
            $db->table('patient_insurances')->insert($insuranceData);
        }

        // Update or insert medical notes
        $notes = trim((string) $request->getPost('medical_notes'));
        $existingNotes = $db->table('patient_notes')->where('patient_id', $id)->get()->getRowArray();
        if ($notes !== '') {
            if ($existingNotes) {
                $db->table('patient_notes')->where('patient_id', $id)->update(['notes' => $notes]);
            } else {
                $db->table('patient_notes')->insert(['patient_id' => $id, 'notes' => $notes]);
            }
        } elseif ($existingNotes) {
            $db->table('patient_notes')->where('patient_id', $id)->delete();
        }

        // Update or insert admission details for inpatient
        if ($visitType === 'inpatient') {
            $admissionDate = $request->getPost('admission_date');
            $admissionTime = $request->getPost('admission_time');
            $admissionDateTime = null;
            if ($admissionDate && $admissionTime) {
                $admissionDateTime = $admissionDate . ' ' . $admissionTime . ':00';
            }
            
            $doctorId = (int) $request->getPost('inpatient_doctor_id');

            $admissionData = [
                'doctor_id'          => $doctorId > 0 ? $doctorId : null,
                'admission_datetime' => $admissionDateTime,
                'room_type'          => $request->getPost('room_type') ?: null,
                'room_number'        => $request->getPost('room_number') ?: null,
                'bed_number'         => $request->getPost('bed_number') ?: null,
            ];
            $existingAdmission = $db->table('admissions')->where('patient_id', $id)->get()->getRowArray();
            if ($existingAdmission) {
                $db->table('admissions')->where('patient_id', $id)->update($admissionData);
            } else {
                $admissionData['patient_id'] = $id;
                $db->table('admissions')->insert($admissionData);
            }
        } else {
            // Remove admission if switching from inpatient to outpatient
            $db->table('admissions')->where('patient_id', $id)->delete();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Unable to update patient record. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->session->setFlashdata('success', 'Patient record has been updated successfully.');
        return redirect()->to(base_url('patients/records'));
    }
}
