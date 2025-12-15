<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class MedicalReports extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            return redirect()->to(site_url('dashboard'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID from doctors table
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];

        // Get all patients for this doctor (from appointments, admissions, or direct assignment)
        $patients = [];
        $patientIds = [];

        // Get patients from appointments
        $appointmentPatients = $db->table('appointments a')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('ds.doctor_id', $doctorId)
            ->distinct()
            ->get()
            ->getResultArray();

        foreach ($appointmentPatients as $patient) {
            if (!in_array($patient['id'], $patientIds)) {
                $patients[] = $patient;
                $patientIds[] = $patient['id'];
            }
        }

        // Get patients from admissions (inpatients)
        $admissionPatients = $db->table('admissions a')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->where('a.doctor_id', $doctorId)
            ->distinct()
            ->get()
            ->getResultArray();

        foreach ($admissionPatients as $patient) {
            if (!in_array($patient['id'], $patientIds)) {
                $patients[] = $patient;
                $patientIds[] = $patient['id'];
            }
        }

        // Get patients directly assigned to doctor
        $directPatients = $db->table('patients p')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender')
            ->where('p.doctor_id', $doctorId)
            ->get()
            ->getResultArray();

        foreach ($directPatients as $patient) {
            if (!in_array($patient['id'], $patientIds)) {
                $patients[] = $patient;
                $patientIds[] = $patient['id'];
            }
        }

        // Format patient names
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
            'title' => 'Medical Reports | Doctor Panel',
            'patients' => $patients,
        ];

        return view('doctor/medical_reports', $data);
    }

    public function generate($patientId)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            return redirect()->to(site_url('dashboard'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];

        // Get patient information
        $patient = $db->table('patients p')
            ->select('p.*, pc.province, pc.city_municipality, pc.barangay, pc.mobile_number, pc.email,
                pec.contact_person as emergency_contact_person, pec.relationship as emergency_relationship, pec.contact_number as emergency_contact_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                pi.provider_name as insurance_provider, pi.policy_number,
                pn.notes as medical_notes')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('patient_insurances pi', 'pi.patient_id = p.id', 'left')
            ->join('patient_notes pn', 'pn.patient_id = p.id', 'left')
            ->where('p.id', $patientId)
            ->get()
            ->getRowArray();

        if (!$patient) {
            $this->session->setFlashdata('error', 'Patient not found.');
            return redirect()->to(site_url('doctor/medical-reports'));
        }

        // Verify doctor has access to this patient
        $hasAccess = false;
        
        // Check appointments
        $appointmentCheck = $db->table('appointments a')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('a.patient_id', $patientId)
            ->where('ds.doctor_id', $doctorId)
            ->countAllResults(false);
        
        if ($appointmentCheck > 0) {
            $hasAccess = true;
        }

        // Check admissions
        if (!$hasAccess) {
            $admissionCheck = $db->table('admissions')
                ->where('patient_id', $patientId)
                ->where('doctor_id', $doctorId)
                ->countAllResults(false);
            
            if ($admissionCheck > 0) {
                $hasAccess = true;
            }
        }

        // Check direct assignment
        if (!$hasAccess && $patient['doctor_id'] == $doctorId) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'You do not have access to this patient\'s records.');
            return redirect()->to(site_url('doctor/medical-reports'));
        }

        // Get appointments for this patient with this doctor
        $appointments = $db->table('appointments a')
            ->select('a.*, ds.shift_name, ds.start_time, ds.end_time')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('a.patient_id', $patientId)
            ->where('ds.doctor_id', $doctorId)
            ->orderBy('a.appointment_date', 'DESC')
            ->orderBy('ds.start_time', 'DESC')
            ->get()
            ->getResultArray();

        // Get prescriptions for this patient
        $prescriptions = $db->table('prescriptions p')
            ->select('p.*, m.medicine_name, m.unit')
            ->join('medicines m', 'm.id = p.medicine_id', 'left')
            ->where('p.patient_id', $patientId)
            ->where('p.doctor_id', $doctorId)
            ->orderBy('p.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get lab tests for this patient
        $labTests = $db->table('lab_tests')
            ->where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate age
        if ($patient['date_of_birth']) {
            $birthDate = new \DateTime($patient['date_of_birth']);
            $today = new \DateTime();
            $patient['age'] = $today->diff($birthDate)->y;
        } else {
            $patient['age'] = null;
        }

        $data = [
            'title' => 'Medical Report | Doctor Panel',
            'patient' => $patient,
            'appointments' => $appointments,
            'prescriptions' => $prescriptions,
            'labTests' => $labTests,
            'doctor' => $doctor,
        ];

        return view('doctor/medical_report_generate', $data);
    }
}

