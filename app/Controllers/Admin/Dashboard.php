<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Dashboard extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
    }

    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();
        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        $todayEnd = $today . ' 23:59:59';
        $dayOfWeek = strtolower(date('l')); // e.g., 'monday', 'tuesday'

        // Get total patients count
        $totalPatients = $db->table('patients')->countAllResults();

        // Get patients registered today
        $patientsToday = $db->table('patients')
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        // Get on-duty doctors (doctors with schedules that include today's day of week)
        $onDutyDoctors = $db->table('doctor_schedules ds')
            ->distinct()
            ->select('ds.doctor_id, d.full_name')
            ->join('doctor_schedule_days dsd', 'dsd.schedule_id = ds.id')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->where('dsd.day_of_week', $dayOfWeek)
            ->where('ds.valid_from <=', $today)
            ->where('ds.valid_to >=', $today)
            ->where('d.status', 'active')
            ->get()
            ->getResultArray();
        $onDutyDoctorsCount = count($onDutyDoctors);

        // Get nurses assigned (users with nurse role or from nurses table)
        $nursesCount = 0;
        if ($db->tableExists('nurses')) {
            $nursesCount = $db->table('nurses')
                ->where('status', 'active')
                ->countAllResults();
        } else {
            // Fallback: count users with nurse role
            $nurseRole = $db->table('roles')
                ->where('name', 'nurse')
                ->get()
                ->getRowArray();
            if ($nurseRole) {
                $nursesCount = $db->table('users')
                    ->where('role_id', $nurseRole['id'])
                    ->where('status', 'active')
                    ->countAllResults();
            }
        }

        // Get today's appointments
        $todayAppointments = $db->table('appointments')
            ->where('appointment_date', $today)
            ->countAllResults();

        // Get recent system activity
        $recentActivity = [];
        
        // Get recent appointments
        $recentAppointments = $db->table('appointments a')
            ->select('a.created_at, p.first_name, p.last_name')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getResultArray();
        
        foreach ($recentAppointments as $apt) {
            $patientName = trim(($apt['first_name'] ?? '') . ' ' . ($apt['last_name'] ?? ''));
            $recentActivity[] = [
                'timestamp' => $apt['created_at'],
                'time' => date('H:i', strtotime($apt['created_at'])),
                'user' => $patientName ?: 'Patient',
                'action' => 'Scheduled appointment',
                'module' => 'Scheduling'
            ];
        }
        
        // Get recent patient registrations
        $recentPatients = $db->table('patients')
            ->select('first_name, last_name, created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getResultArray();
        
        foreach ($recentPatients as $patient) {
            $patientName = trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
            $recentActivity[] = [
                'timestamp' => $patient['created_at'],
                'time' => date('H:i', strtotime($patient['created_at'])),
                'user' => $patientName ?: 'Patient',
                'action' => 'Updated patient record',
                'module' => 'Patients'
            ];
        }
        
        // Get recent user activity (from users table)
        $recentUsers = $db->table('users')
            ->select('email, first_name, last_name, updated_at')
            ->orderBy('updated_at', 'DESC')
            ->limit(1)
            ->get()
            ->getResultArray();

        foreach ($recentUsers as $user) {
            $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['email'];
            $recentActivity[] = [
                'timestamp' => $user['updated_at'],
                'time' => date('H:i', strtotime($user['updated_at'])),
                'user' => $userName,
                'action' => 'Login',
                'module' => 'Authentication'
            ];
        }
        
        // Sort by timestamp (most recent first) and limit to 3
        usort($recentActivity, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        $recentActivity = array_slice($recentActivity, 0, 3);

        // Get hospital name from settings
        $hospitalName = $this->settingsModel->getSetting('hospital_name', 'St. Peter Hospital');

        $data = [
            'title' => 'Admin Dashboard | HMS System',
            'totalPatients' => $totalPatients,
            'patientsToday' => $patientsToday,
            'onDutyDoctors' => $onDutyDoctorsCount,
            'nursesAssigned' => $nursesCount,
            'todayAppointments' => $todayAppointments,
            'recentActivity' => $recentActivity,
            'hospitalName' => $hospitalName,
        ];

        return view('admin/dashboard', $data);
    }
}
