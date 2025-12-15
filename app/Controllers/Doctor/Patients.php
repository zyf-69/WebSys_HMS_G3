<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Patients extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            return redirect()->to(base_url('dashboard'));
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
            return redirect()->to(base_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];

        // Get inpatients assigned to this doctor (through admissions table)
        $inpatients = $db->table('admissions a')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.civil_status, p.place_of_birth, p.blood_type,
                pc.province, pc.city_municipality, pc.barangay, pc.mobile_number, pc.email,
                pec.contact_person as emergency_contact_person, pec.relationship as emergency_relationship, pec.contact_number as emergency_contact_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                pi.provider_name as insurance_provider, pi.policy_number,
                a.admission_datetime, a.room_type, a.room_number, a.bed_number')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('patient_insurances pi', 'pi.patient_id = p.id', 'left')
            ->where('a.doctor_id', $doctorId)
            ->orderBy('a.admission_datetime', 'DESC')
            ->get()
            ->getResultArray();

        // Get outpatients assigned to this doctor (through patients.doctor_id or appointments)
        $outpatientsFromDirect = $db->table('patients p')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.civil_status, p.place_of_birth, p.blood_type,
                pc.province, pc.city_municipality, pc.barangay, pc.mobile_number, pc.email,
                pec.contact_person as emergency_contact_person, pec.relationship as emergency_relationship, pec.contact_number as emergency_contact_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                pi.provider_name as insurance_provider, pi.policy_number')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('patient_insurances pi', 'pi.patient_id = p.id', 'left')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->where('p.doctor_id', $doctorId)
            ->where('a.id IS NULL') // Only outpatients (no admission record)
            ->get()
            ->getResultArray();

        // Get outpatients from appointments
        $outpatientsFromAppointments = $db->table('appointments a')
            ->distinct()
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.civil_status, p.place_of_birth, p.blood_type,
                pc.province, pc.city_municipality, pc.barangay, pc.mobile_number, pc.email,
                pec.contact_person as emergency_contact_person, pec.relationship as emergency_relationship, pec.contact_number as emergency_contact_number,
                pv.blood_pressure, pv.heart_rate, pv.temperature, pv.height_cm, pv.weight_kg, pv.bmi,
                pi.provider_name as insurance_provider, pi.policy_number,
                a.appointment_date, a.schedule_type, a.status as appointment_status,
                ds.start_time, ds.end_time,
                a.id as appointment_id')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('patient_emergency_contacts pec', 'pec.patient_id = p.id', 'left')
            ->join('patient_vitals pv', 'pv.patient_id = p.id', 'left')
            ->join('patient_insurances pi', 'pi.patient_id = p.id', 'left')
            ->join('admissions adm', 'adm.patient_id = p.id', 'left')
            ->where('ds.doctor_id', $doctorId)
            ->where('adm.id IS NULL') // Only outpatients
            ->orderBy('a.appointment_date', 'DESC')
            ->orderBy('ds.start_time', 'DESC')
            ->get()
            ->getResultArray();

        // Merge all patients and remove duplicates
        $allPatients = [];
        $patientIds = [];
        
        // Add inpatients
        foreach ($inpatients as $patient) {
            $patient['patient_type'] = 'inpatient';
            $patient['visit_type'] = 'Inpatient';
            $allPatients[] = $patient;
            $patientIds[] = $patient['id'];
        }
        
        // Add outpatients from direct assignment
        foreach ($outpatientsFromDirect as $patient) {
            if (!in_array($patient['id'], $patientIds)) {
                $patient['patient_type'] = 'outpatient';
                $patient['visit_type'] = 'Outpatient';
                $allPatients[] = $patient;
                $patientIds[] = $patient['id'];
            }
        }
        
        // Add outpatients from appointments
        foreach ($outpatientsFromAppointments as $patient) {
            if (!in_array($patient['id'], $patientIds)) {
                $patient['patient_type'] = 'outpatient';
                $patient['visit_type'] = 'Outpatient';
                $allPatients[] = $patient;
                $patientIds[] = $patient['id'];
            }
        }

        // Format the data for display
        foreach ($allPatients as &$patient) {
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
        
        $patients = $allPatients;

        $data = [
            'title' => 'My Patients | Doctor Panel',
            'patients' => $patients,
        ];

        return view('doctor/patients', $data);
    }
}

