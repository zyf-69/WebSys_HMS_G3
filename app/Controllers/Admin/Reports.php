<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class Reports extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get statistics
        $totalPatients = $db->table('patients')->countAllResults();
        
        // Get doctor role ID
        $doctorRole = $db->table('roles')
            ->where('name', 'doctor')
            ->get()
            ->getRowArray();
        
        $totalDoctors = 0;
        if ($doctorRole) {
            $totalDoctors = $db->table('users')
                ->where('role_id', $doctorRole['id'])
                ->where('status', 'active')
                ->countAllResults();
        }
        
        $totalAppointments = $db->table('appointments')->countAllResults();
        
        $totalBills = $db->table('bills')->countAllResults();
        
        $totalRevenue = $db->table('bills')
            ->selectSum('total_amount')
            ->where('status', 'paid')
            ->get()
            ->getRow();
        $totalRevenue = $totalRevenue->total_amount ?? 0;
        
        // Get recent patients
        $recentPatients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name, created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        // Get recent appointments
        $recentAppointments = $db->table('appointments');
        $recentAppointments->select('appointments.*, patients.first_name, patients.middle_name, patients.last_name, users.first_name as doctor_first, users.last_name as doctor_last');
        $recentAppointments->join('patients', 'patients.id = appointments.patient_id', 'left');
        $recentAppointments->join('doctor_schedules', 'doctor_schedules.id = appointments.doctor_schedule_id', 'left');
        $recentAppointments->join('users', 'users.id = doctor_schedules.doctor_id', 'left');
        $recentAppointments->orderBy('appointments.created_at', 'DESC');
        $recentAppointments->limit(10);
        $recentAppointments = $recentAppointments->get()->getResultArray();
        
        foreach ($recentAppointments as &$appt) {
            $appt['doctor_name'] = trim(($appt['doctor_first'] ?? '') . ' ' . ($appt['doctor_last'] ?? ''));
        }
        
        // Monthly statistics (last 12 months)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $startDate = $date . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $monthPatients = $db->table('patients')
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();
            
            $monthAppointments = $db->table('appointments')
                ->where('appointment_date >=', $startDate)
                ->where('appointment_date <=', $endDate)
                ->countAllResults();
            
            $monthRevenue = $db->table('bills')
                ->selectSum('total_amount')
                ->where('status', 'paid')
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->get()
                ->getRow();
            
            $monthlyStats[] = [
                'month' => date('M Y', strtotime($startDate)),
                'patients' => $monthPatients,
                'appointments' => $monthAppointments,
                'revenue' => $monthRevenue->total_amount ?? 0,
            ];
        }
        
        return view('admin/reports', [
            'totalPatients' => $totalPatients,
            'totalDoctors' => $totalDoctors,
            'totalAppointments' => $totalAppointments,
            'totalBills' => $totalBills,
            'totalRevenue' => $totalRevenue,
            'recentPatients' => $recentPatients,
            'recentAppointments' => $recentAppointments,
            'monthlyStats' => $monthlyStats,
        ]);
    }
    
    public function generate()
    {
        $reportType = $this->request->getPost('report_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        $db = \Config\Database::connect();
        $reportData = [];
        
        if ($reportType === 'patients') {
            $builder = $db->table('patients');
            if ($startDate) {
                $builder->where('created_at >=', $startDate . ' 00:00:00');
            }
            if ($endDate) {
                $builder->where('created_at <=', $endDate . ' 23:59:59');
            }
            $reportData = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        } elseif ($reportType === 'appointments') {
            $builder = $db->table('appointments');
            $builder->select('appointments.*, patients.first_name, patients.middle_name, patients.last_name');
            $builder->join('patients', 'patients.id = appointments.patient_id', 'left');
            if ($startDate) {
                $builder->where('appointment_date >=', $startDate);
            }
            if ($endDate) {
                $builder->where('appointment_date <=', $endDate);
            }
            $reportData = $builder->orderBy('appointment_date', 'DESC')->get()->getResultArray();
        } elseif ($reportType === 'billing') {
            $builder = $db->table('bills');
            $builder->select('bills.*, patients.first_name, patients.middle_name, patients.last_name');
            $builder->join('patients', 'patients.id = bills.patient_id', 'left');
            if ($startDate) {
                $builder->where('bills.created_at >=', $startDate . ' 00:00:00');
            }
            if ($endDate) {
                $builder->where('bills.created_at <=', $endDate . ' 23:59:59');
            }
            $reportData = $builder->orderBy('bills.created_at', 'DESC')->get()->getResultArray();
        } elseif ($reportType === 'revenue') {
            $builder = $db->table('bills');
            $builder->select('bills.*, patients.first_name, patients.middle_name, patients.last_name');
            $builder->join('patients', 'patients.id = bills.patient_id', 'left');
            $builder->where('bills.status', 'paid');
            if ($startDate) {
                $builder->where('bills.created_at >=', $startDate . ' 00:00:00');
            }
            if ($endDate) {
                $builder->where('bills.created_at <=', $endDate . ' 23:59:59');
            }
            $reportData = $builder->orderBy('bills.created_at', 'DESC')->get()->getResultArray();
        }
        
        return view('admin/reports_generate', [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportData' => $reportData,
        ]);
    }
}

