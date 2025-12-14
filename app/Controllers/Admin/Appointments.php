<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Appointments extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        // Allow both admin and receptionist to access appointments
        if (! $this->hasRole(['admin', 'hospital_administrator', 'receptionist'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Patients for select - only outpatients (patients without admission records)
        $patients = $db->table('patients p')
            ->select('p.id, p.first_name, p.middle_name, p.last_name, p.doctor_id, d.full_name as doctor_name')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->join('doctors d', 'd.id = p.doctor_id', 'left')
            ->where('a.id IS NULL') // Only outpatients (no admission record)
            ->orderBy('p.last_name', 'ASC')
            ->orderBy('p.first_name', 'ASC')
            ->get()->getResultArray();

        // Get all active doctors from database (fresh query)
        $doctors = $db->table('doctors')
            ->select('id, full_name, specialization, license_number')
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->get()->getResultArray();
        
        // Ensure we have doctors
        if (empty($doctors)) {
            log_message('warning', 'No active doctors found in database');
        } else {
            log_message('debug', 'Doctors found: ' . count($doctors) . ' - ' . json_encode(array_column($doctors, 'full_name')));
        }
        
        // Get all doctor schedules for reference (to use when saving appointment)
        $schedules = $db->table('doctor_schedules ds')
            ->select('ds.id, ds.doctor_id, ds.shift_name, ds.start_time, ds.end_time, ds.valid_from, ds.valid_to, d.full_name')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->where('d.status', 'active')
            ->orderBy('d.full_name', 'ASC')
            ->orderBy('ds.shift_name', 'ASC')
            ->get()->getResultArray();

        // Appointments for calendar (show all appointments within next 12 months)
        $today = date('Y-m-d');
        $oneYearLater = date('Y-m-d', strtotime('+1 year'));

        $appointments = $db->table('appointments a')
            ->select('a.id, a.appointment_date, a.schedule_type, a.status, 
                p.first_name, p.middle_name, p.last_name, 
                d.full_name AS doctor_name,
                ds.start_time, ds.end_time, ds.shift_name')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'left')
            ->join('doctors d', 'd.id = ds.doctor_id', 'left')
            ->where('a.appointment_date >=', date('Y-m-d', strtotime('-1 month'))) // Show past month and future
            ->where('a.appointment_date <=', $oneYearLater)
            ->orderBy('a.appointment_date', 'ASC')
            ->orderBy('ds.start_time', 'ASC')
            ->get()->getResultArray();

        // Update status to 'pending' if appointment date hasn't passed
        $now = date('Y-m-d H:i:s');
        foreach ($appointments as &$appointment) {
            $appointmentDate = $appointment['appointment_date'] ?? '';
            $startTime = $appointment['start_time'] ?? '00:00:00';
            $appointmentDateTime = $appointmentDate . ' ' . $startTime;
            
            // If appointment date/time hasn't passed, set status to 'pending'
            if (!empty($appointmentDate) && strtotime($appointmentDateTime) > strtotime($now)) {
                if (empty($appointment['status']) || $appointment['status'] === 'scheduled') {
                    $appointment['status'] = 'pending';
                }
            } elseif (empty($appointment['status'])) {
                $appointment['status'] = 'scheduled';
            }
            
            // Ensure date is in Y-m-d format for JavaScript
            if (!empty($appointment['appointment_date'])) {
                $appointment['appointment_date'] = date('Y-m-d', strtotime($appointment['appointment_date']));
            }
        }
        unset($appointment);

        $data = [
            'title'        => 'Appointments | HMS System',
            'patients'     => $patients,
            'doctors'      => $doctors,
            'schedules'    => $schedules,
            'appointments' => $appointments,
        ];

        return view('admin/appointments', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        // Allow both admin and receptionist to create appointments
        if (! $this->hasRole(['admin', 'hospital_administrator', 'receptionist'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $request = $this->request;

        $patientId        = (int) $request->getPost('patient_id');
        $doctorId         = (int) $request->getPost('doctor_id');
        $doctorScheduleId = (int) $request->getPost('doctor_schedule_id');
        $appointmentDate  = $request->getPost('appointment_date');
        $appointmentTime  = $request->getPost('appointment_time'); // Selected appointment time
        $scheduleType     = $request->getPost('schedule_type');

        if (! $patientId || ! $doctorId || ! $appointmentDate) {
            $this->session->setFlashdata('error', 'Patient, doctor, and appointment date are required.');
            return redirect()->back()->withInput();
        }

        $db = db_connect();
        
        // If no schedule selected, create a default schedule for the appointment
        if (!$doctorScheduleId || $doctorScheduleId === 0) {
            // Calculate end time from appointment time or use default
            $startTime = $appointmentTime ?: '09:00:00';
            $endTime = $appointmentTime ? date('H:i:s', strtotime($appointmentTime . ' +1 hour')) : '10:00:00';
            
            // Create a default schedule for this doctor
            $defaultSchedule = [
                'doctor_id'  => $doctorId,
                'shift_name' => 'General Appointment',
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'valid_from' => $appointmentDate,
                'valid_to'   => date('Y-m-d', strtotime($appointmentDate . ' +1 year')),
            ];
            
            $db->table('doctor_schedules')->insert($defaultSchedule);
            $doctorScheduleId = $db->insertID();
        }
        
        // Verify that the selected schedule belongs to the selected doctor
        $schedule = $db->table('doctor_schedules')
            ->where('id', $doctorScheduleId)
            ->where('doctor_id', $doctorId)
            ->get()
            ->getRowArray();
        
        if (!$schedule) {
            $this->session->setFlashdata('error', 'Invalid doctor schedule selected. The schedule must belong to the selected doctor.');
            return redirect()->back()->withInput();
        }

        // Determine initial status: 'pending' if appointment date is in the future
        // Use selected appointment_time if provided, otherwise use schedule start_time
        $appointmentTimeToUse = $appointmentTime ?: ($schedule['start_time'] ?? '00:00:00');
        $appointmentDateTime = $appointmentDate . ' ' . $appointmentTimeToUse;
        $now = date('Y-m-d H:i:s');
        $initialStatus = (strtotime($appointmentDateTime) > strtotime($now)) ? 'pending' : 'scheduled';

        $data = [
            'patient_id'        => $patientId,
            'doctor_schedule_id'=> $doctorScheduleId,
            'appointment_date'  => $appointmentDate,
            'schedule_type'     => $scheduleType ?: null,
            'status'            => $initialStatus,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            $db->table('appointments')->insert($data);
            $appointmentId = $db->insertID();
            
            log_message('info', 'Appointment saved successfully. ID: ' . $appointmentId . ', Patient: ' . $patientId . ', Doctor: ' . $doctorId);
            $this->session->setFlashdata('success', 'Appointment has been saved successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Appointment save error: ' . $e->getMessage());
            log_message('error', 'Appointment data: ' . json_encode($data));
            $this->session->setFlashdata('error', 'Failed to save appointment: ' . $e->getMessage());
        }
        
        return redirect()->to(base_url('admin/appointments'));
    }
}
