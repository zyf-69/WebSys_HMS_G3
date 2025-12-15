<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Appointments extends BaseController
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

        // Get all appointments for this doctor
        // Join through doctor_schedules to get doctor_id
        $appointments = $db->table('appointments a')
            ->select('a.id, a.appointment_date, a.schedule_type, a.status,
                ds.start_time, ds.end_time, ds.shift_name,
                p.id as patient_id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('ds.doctor_id', $doctorId)
            ->orderBy('a.appointment_date', 'DESC')
            ->orderBy('ds.start_time', 'DESC')
            ->get()
            ->getResultArray();

        // Format the data and update status
        $now = date('Y-m-d H:i:s');
        foreach ($appointments as &$appointment) {
            $appointment['patient_name'] = trim(($appointment['first_name'] ?? '') . ' ' . ($appointment['middle_name'] ?? '') . ' ' . ($appointment['last_name'] ?? ''));
            if ($appointment['date_of_birth']) {
                $birthDate = new \DateTime($appointment['date_of_birth']);
                $today = new \DateTime();
                $appointment['patient_age'] = $today->diff($birthDate)->y;
            } else {
                $appointment['patient_age'] = null;
            }
            
            // Update status to 'pending' if appointment date/time hasn't passed
            $appointmentDate = $appointment['appointment_date'];
            $appointmentDateTime = $appointmentDate . ' ' . ($appointment['start_time'] ?? '00:00:00');
            if (strtotime($appointmentDateTime) > strtotime($now)) {
                if ($appointment['status'] === 'scheduled') {
                    $appointment['status'] = 'pending';
                }
            }
        }
        unset($appointment);

        // Get follow-up checkups for this doctor
        $followUps = $db->table('follow_ups fu')
            ->select('fu.*, p.id as patient_id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, a.appointment_date as original_appointment_date')
            ->join('patients p', 'p.id = fu.patient_id', 'left')
            ->join('appointments a', 'a.id = fu.original_appointment_id', 'left')
            ->where('fu.doctor_id', $doctorId)
            ->orderBy('fu.follow_up_date', 'ASC')
            ->orderBy('fu.follow_up_time', 'ASC')
            ->get()
            ->getResultArray();

        // Format follow-up data
        foreach ($followUps as &$followUp) {
            $followUp['patient_name'] = trim(($followUp['first_name'] ?? '') . ' ' . ($followUp['middle_name'] ?? '') . ' ' . ($followUp['last_name'] ?? ''));
            if ($followUp['date_of_birth']) {
                $birthDate = new \DateTime($followUp['date_of_birth']);
                $today = new \DateTime();
                $followUp['patient_age'] = $today->diff($birthDate)->y;
            } else {
                $followUp['patient_age'] = null;
            }
        }
        unset($followUp);

        $data = [
            'title' => 'My Appointments | Doctor Panel',
            'appointments' => $appointments,
            'followUps' => $followUps,
        ];

        return view('doctor/appointments', $data);
    }

    public function complete($appointmentId)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            session()->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            session()->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        $doctorId = $doctor['id'];

        // Verify the appointment belongs to this doctor
        $appointment = $db->table('appointments a')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('a.id', $appointmentId)
            ->where('ds.doctor_id', $doctorId)
            ->get()
            ->getRowArray();

        if (!$appointment) {
            session()->setFlashdata('error', 'Appointment not found or you do not have permission to modify it.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        try {
            $db->table('appointments')
                ->where('id', $appointmentId)
                ->update([
                    'status' => 'completed',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            session()->setFlashdata('success', 'Appointment marked as completed.');
        } catch (\Exception $e) {
            log_message('error', 'Failed to complete appointment: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update appointment status.');
        }

        return redirect()->to(site_url('doctor/appointments'));
    }

    public function createFollowUp()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            session()->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            session()->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        $doctorId = $doctor['id'];

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('doctor/appointments'));
        }

        $appointmentId = (int) $request->getPost('appointment_id');
        $patientId = (int) $request->getPost('patient_id');
        $followUpDate = $request->getPost('follow_up_date');
        $followUpTime = $request->getPost('follow_up_time');
        $reason = trim($request->getPost('reason') ?? '');
        $notes = trim($request->getPost('notes') ?? '');

        // Validation
        if (empty($appointmentId) || empty($patientId) || empty($followUpDate)) {
            session()->setFlashdata('error', 'Please fill in all required fields.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        // Verify the appointment belongs to this doctor and is completed
        $appointment = $db->table('appointments a')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('a.id', $appointmentId)
            ->where('a.patient_id', $patientId)
            ->where('ds.doctor_id', $doctorId)
            ->where('a.status', 'completed')
            ->get()
            ->getRowArray();

        if (!$appointment) {
            session()->setFlashdata('error', 'Appointment not found, not completed, or you do not have permission to create follow-up.');
            return redirect()->to(site_url('doctor/appointments'));
        }

        try {
            $followUpData = [
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'original_appointment_id' => $appointmentId,
                'follow_up_date' => $followUpDate,
                'follow_up_time' => $followUpTime ?: null,
                'reason' => $reason ?: null,
                'notes' => $notes ?: null,
                'status' => 'scheduled',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $db->table('follow_ups')->insert($followUpData);
            session()->setFlashdata('success', 'Follow-up checkup scheduled successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Failed to create follow-up: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to schedule follow-up. Please try again.');
        }

        return redirect()->to(site_url('doctor/appointments'));
    }
}

