<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class Billing extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get all bills with patient information
        $builder = $db->table('bills');
        $builder->select('bills.*, patients.first_name, patients.middle_name, patients.last_name');
        $builder->join('patients', 'patients.id = bills.patient_id', 'left');
        $builder->orderBy('bills.created_at', 'DESC');
        $bills = $builder->get()->getResultArray();
        
        // Calculate statistics
        $totalBills = count($bills);
        $paidBills = count(array_filter($bills, fn($b) => ($b['status'] ?? 'pending') === 'paid'));
        $pendingBills = $totalBills - $paidBills;
        $totalAmount = array_sum(array_column($bills, 'total_amount'));
        
        // Get patients for dropdown
        $patients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->get()
            ->getResultArray();
        
        return view('admin/billing', [
            'bills' => $bills,
            'totalBills' => $totalBills,
            'paidBills' => $paidBills,
            'pendingBills' => $pendingBills,
            'totalAmount' => $totalAmount,
            'patients' => $patients,
        ]);
    }
    
    public function store()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'bill_type' => $this->request->getPost('bill_type'),
            'total_amount' => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description'),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        $db->table('bills')->insert($data);
        
        return redirect()->to('/WebSys_HMS_G3/admin/billing')->with('success', 'Bill created successfully.');
    }
    
    public function payment()
    {
        $db = \Config\Database::connect();
        
        $billId = $this->request->getPost('bill_id');
        $paymentAmount = $this->request->getPost('payment_amount');
        
        $db->transStart();
        
        // Update bill status
        $db->table('bills')
            ->where('id', $billId)
            ->update(['status' => 'paid', 'updated_at' => date('Y-m-d H:i:s')]);
        
        // Record payment
        $db->table('payments')->insert([
            'bill_id' => $billId,
            'amount' => $paymentAmount,
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->transComplete();
        
        return redirect()->to('/WebSys_HMS_G3/admin/billing')->with('success', 'Payment recorded successfully.');
    }
}

