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
        // Log the request - use error level so it's always logged
        log_message('error', '=== SCHEDULE STORE METHOD CALLED ===');
        log_message('error', 'Request method: ' . $this->request->getMethod());
        log_message('error', 'Request URI: ' . $this->request->getUri()->getPath());
        log_message('error', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('error', 'Raw input: ' . $this->request->getBody());

        $result = $this->requireLogin();
        if ($result !== true) {
            log_message('error', 'User not logged in');
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            log_message('error', 'User does not have doctor role');
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
            log_message('error', 'Doctor profile not found for user_id: ' . $userId);
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(base_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];
        log_message('error', 'Doctor ID: ' . $doctorId);
        
        $request = $this->request;

        $shiftName  = $request->getPost('shift_name');
        $startTime  = $request->getPost('start_time');
        $endTime    = $request->getPost('end_time');
        $validFrom  = $request->getPost('valid_from');
        $validTo    = $request->getPost('valid_to');
        $days       = (array) $request->getPost('days');

        log_message('error', 'Form data - shift_name: ' . ($shiftName ?? 'null') . ', start_time: ' . ($startTime ?? 'null') . ', end_time: ' . ($endTime ?? 'null'));
        log_message('error', 'Days received (raw): ' . json_encode($days));
        log_message('error', 'Days count: ' . count($days));

        // Filter out empty values from days array first
        $days = array_filter($days, function($day) {
            return !empty($day) && !empty(trim((string)$day));
        });
        
        // Re-index the array after filtering
        $days = array_values($days);
        
        log_message('error', 'Days after filtering: ' . json_encode($days));
        log_message('error', 'Days count after filtering: ' . count($days));

        if (empty($days)) {
            log_message('error', 'No valid days selected after filtering');
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

        try {
            $scheduleData = [
                'doctor_id'  => $doctorId,
                'shift_name' => !empty($shiftName) ? $shiftName : null,
                'start_time' => !empty($startTime) ? $startTime : null,
                'end_time'   => !empty($endTime) ? $endTime : null,
                'valid_from' => !empty($validFrom) ? $validFrom : null,
                'valid_to'   => !empty($validTo) ? $validTo : null,
            ];

            // Add created_by only if column exists
            try {
                $fields = $db->getFieldNames('doctor_schedules');
                if (in_array('created_by', $fields)) {
                    $scheduleData['created_by'] = 'doctor';
                }
            } catch (\Exception $e) {
                log_message('error', 'Error checking fields: ' . $e->getMessage());
            }

            log_message('error', 'Inserting schedule data: ' . json_encode($scheduleData));

            if (!$db->table('doctor_schedules')->insert($scheduleData)) {
                $error = $db->error();
                log_message('error', 'Database insert error: ' . json_encode($error));
                throw new \Exception('Failed to create schedule record: ' . ($error['message'] ?? 'Unknown error'));
            }

            $scheduleId = $db->insertID();
            log_message('error', 'Schedule ID created: ' . $scheduleId);

            if (!$scheduleId) {
                throw new \Exception('Failed to get schedule ID after insert');
            }

            $dayRows = [];
            foreach ($days as $day) {
                $day = strtolower(trim($day));
                if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
                    $dayRows[] = [
                        'schedule_id' => $scheduleId,
                        'day_of_week' => $day,
                    ];
                }
            }

            log_message('error', 'Day rows to insert: ' . json_encode($dayRows));

            if (!empty($dayRows)) {
                if (!$db->table('doctor_schedule_days')->insertBatch($dayRows)) {
                    $error = $db->error();
                    log_message('error', 'Database batch insert error: ' . json_encode($error));
                    throw new \Exception('Failed to save schedule days: ' . ($error['message'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('No valid days to save');
            }

            // Complete the transaction - this will commit if successful, rollback if failed
            if ($db->transComplete() === false) {
                $error = $db->error();
                log_message('error', 'Transaction failed: ' . json_encode($error));
                throw new \Exception('Transaction failed: ' . ($error['message'] ?? 'Unknown error'));
            }

            log_message('error', 'Schedule saved successfully');
            
            $this->session->setFlashdata('success', 'Schedule has been saved successfully.');
            return redirect()->to(base_url('doctor/schedule'));
        } catch (\Exception $e) {
            // Only rollback if transaction is still active
            if ($db->transStatus() !== false) {
                $db->transRollback();
            }
            log_message('error', 'Schedule save error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->session->setFlashdata('error', 'Unable to save schedule: ' . $e->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $e) {
            // Only rollback if transaction is still active
            if ($db->transStatus() !== false) {
                $db->transRollback();
            }
            log_message('error', 'Schedule save fatal error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->session->setFlashdata('error', 'Unable to save schedule. Please try again.');
            return redirect()->back()->withInput();
        }
    }
}

