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

        $data = [
            'title' => 'My Appointments | Doctor Panel',
            'appointments' => $appointments,
        ];

        return view('doctor/appointments', $data);
    }
}

