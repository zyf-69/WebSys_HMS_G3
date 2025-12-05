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

        if (! $this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Patients for select
        $patients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get()->getResultArray();

        // Doctor schedules for select
        $schedules = $db->table('doctor_schedules ds')
            ->select('ds.id, ds.shift_name, ds.start_time, ds.end_time, ds.valid_from, ds.valid_to, d.full_name')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->orderBy('d.full_name', 'ASC')
            ->orderBy('ds.shift_name', 'ASC')
            ->get()->getResultArray();

        // Appointments for calendar (next year)
        $today = date('Y-m-d');
        $oneYearLater = date('Y-m-d', strtotime('+1 year'));

        $appointments = $db->table('appointments a')
            ->select('a.id, a.appointment_date, a.schedule_type, a.status, p.first_name, p.middle_name, p.last_name, d.full_name AS doctor_name')
            ->join('patients p', 'p.id = a.patient_id')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->where('a.appointment_date >=', $today)
            ->where('a.appointment_date <=', $oneYearLater)
            ->orderBy('a.appointment_date', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'        => 'Appointments | HMS System',
            'patients'     => $patients,
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

        if (! $this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $request = $this->request;

        $patientId        = (int) $request->getPost('patient_id');
        $doctorScheduleId = (int) $request->getPost('doctor_schedule_id');
        $appointmentDate  = $request->getPost('appointment_date');
        $scheduleType     = $request->getPost('schedule_type');

        if (! $patientId || ! $doctorScheduleId || ! $appointmentDate) {
            $this->session->setFlashdata('error', 'Patient, doctor schedule, and appointment date are required.');
            return redirect()->back()->withInput();
        }

        $db = db_connect();

        $data = [
            'patient_id'        => $patientId,
            'doctor_schedule_id'=> $doctorScheduleId,
            'appointment_date'  => $appointmentDate,
            'schedule_type'     => $scheduleType ?: null,
            'status'            => 'scheduled',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $db->table('appointments')->insert($data);

        $this->session->setFlashdata('success', 'Appointment has been saved successfully.');
        return redirect()->to(base_url('admin/appointments'));
    }
}
