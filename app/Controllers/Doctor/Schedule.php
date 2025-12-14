<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Schedule extends BaseController
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

        // Get all schedules for this doctor (both admin-created and doctor-created)
        $today = date('Y-m-d');
        $oneYearLater = date('Y-m-d', strtotime('+1 year'));

        $schedules = $db->table('doctor_schedules ds')
            ->select('ds.id, ds.shift_name, ds.start_time, ds.end_time, ds.valid_from, ds.valid_to, ds.created_by')
            ->where('ds.doctor_id', $doctorId)
            ->where('ds.valid_to >=', $today)
            ->where('ds.valid_from <=', $oneYearLater)
            ->orderBy('ds.valid_from', 'ASC')
            ->orderBy('ds.start_time', 'ASC')
            ->get()
            ->getResultArray();

        // Attach days of week
        if (!empty($schedules)) {
            $ids = array_column($schedules, 'id');
            $daysRows = $db->table('doctor_schedule_days')
                ->select('schedule_id, day_of_week')
                ->whereIn('schedule_id', $ids)
                ->get()
                ->getResultArray();

            $daysBySchedule = [];
            foreach ($daysRows as $row) {
                $daysBySchedule[$row['schedule_id']][] = $row['day_of_week'];
            }

            foreach ($schedules as &$row) {
                $row['days'] = $daysBySchedule[$row['id']] ?? [];
            }
            unset($row);
        }

        $data = [
            'title' => 'My Schedule | Doctor Panel',
            'schedules' => $schedules,
            'doctorId' => $doctorId,
        ];

        return view('doctor/schedule', $data);
    }

    public function store()
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

        // Get doctor ID
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(base_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];
        $request = $this->request;

        $shiftName  = $request->getPost('shift_name');
        $startTime  = $request->getPost('start_time');
        $endTime    = $request->getPost('end_time');
        $validFrom  = $request->getPost('valid_from');
        $validTo    = $request->getPost('valid_to');
        $days       = (array) $request->getPost('days');

        if (empty($days)) {
            $this->session->setFlashdata('error', 'Please select at least one day of availability.');
            return redirect()->back()->withInput();
        }

        // Enforce 1-year maximum range
        if ($validFrom && $validTo) {
            $maxTo = date('Y-m-d', strtotime($validFrom . ' +1 year'));
            if ($validTo > $maxTo) {
                $validTo = $maxTo;
            }
        }

        $db->transStart();

        $scheduleData = [
            'doctor_id'  => $doctorId,
            'shift_name' => $shiftName ?: null,
            'start_time' => $startTime ?: null,
            'end_time'   => $endTime ?: null,
            'valid_from' => $validFrom ?: null,
            'valid_to'   => $validTo ?: null,
            'created_by' => 'doctor', // Mark as doctor-created
        ];

        $db->table('doctor_schedules')->insert($scheduleData);
        $scheduleId = $db->insertID();

        $dayRows = [];
        foreach ($days as $day) {
            $day = strtolower($day);
            $dayRows[] = [
                'schedule_id' => $scheduleId,
                'day_of_week' => $day,
            ];
        }

        if (!empty($dayRows)) {
            $db->table('doctor_schedule_days')->insertBatch($dayRows);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Unable to save schedule. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->session->setFlashdata('success', 'Schedule has been saved successfully.');
        return redirect()->to(base_url('doctor/schedule'));
    }
}

