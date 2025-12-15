<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Scheduling extends BaseController
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

        // Get today's date for filtering active schedules
        $today = date('Y-m-d');
        $oneYearLater = date('Y-m-d', strtotime('+1 year'));

        // Get doctors who have active schedules (schedules that haven't expired yet)
        // A schedule is active if valid_to >= today (hasn't expired)
        $doctorsWithSchedules = $db->table('doctor_schedules')
            ->select('doctor_id')
            ->where('valid_to >=', $today)
            ->groupBy('doctor_id')
            ->get()
            ->getResultArray();

        $scheduledDoctorIds = array_column($doctorsWithSchedules, 'doctor_id');

        // Doctors for select - only those WITHOUT active schedules
        $doctorsQuery = $db->table('doctors')
            ->select('id, full_name, specialization, status')
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC');

        // Exclude doctors who already have active schedules
        if (!empty($scheduledDoctorIds)) {
            $doctorsQuery->whereNotIn('id', $scheduledDoctorIds);
        }

        $doctors = $doctorsQuery->get()->getResultArray();

        // Schedules for calendar (next year) - $today and $oneYearLater already defined above
        $scheduleBuilder = $db->table('doctor_schedules ds')
            ->select('ds.id, ds.doctor_id, ds.shift_name, ds.start_time, ds.end_time, ds.valid_from, ds.valid_to, ds.created_by, d.full_name')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->where('ds.valid_to >=', $today)
            ->where('ds.valid_from <=', $oneYearLater)
            ->orderBy('ds.valid_from', 'ASC');

        $schedules = $scheduleBuilder->get()->getResultArray();

        // Attach days of week
        if (! empty($schedules)) {
            $ids = array_column($schedules, 'id');
            $daysRows = $db->table('doctor_schedule_days')
                ->select('schedule_id, day_of_week')
                ->whereIn('schedule_id', $ids)
                ->get()->getResultArray();

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
            'title'     => 'Doctor Scheduling | HMS System',
            'doctors'   => $doctors,
            'schedules' => $schedules,
        ];

        return view('admin/scheduling', $data);
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

        $doctorId   = (int) $request->getPost('doctor_id');
        $shiftName  = $request->getPost('shift_name');
        $startTime  = $request->getPost('start_time');
        $endTime    = $request->getPost('end_time');
        $validFrom  = $request->getPost('valid_from');
        $validTo    = $request->getPost('valid_to');
        $days       = (array) $request->getPost('days');

        if (! $doctorId || empty($days)) {
            $this->session->setFlashdata('error', 'Please select a doctor and at least one day of availability.');
            return redirect()->back()->withInput();
        }

        // Verify doctor exists and is active
        $db = db_connect();
        $doctor = $db->table('doctors')
            ->where('id', $doctorId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Selected doctor not found or is not active.');
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
            'created_by' => 'admin', // Mark as admin-created
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

        if (! empty($dayRows)) {
            $db->table('doctor_schedule_days')->insertBatch($dayRows);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Unable to save doctor schedule. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->session->setFlashdata('success', 'Doctor schedule has been saved successfully.');
        return redirect()->to(base_url('admin/scheduling'));
    }
}
