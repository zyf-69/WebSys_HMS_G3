<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserAccess extends BaseController
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
        $roles = $db->table('roles')
            ->select('id, name, display_name')
            ->where('is_active', 1)
            ->orderBy('level', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'User Access & Security | HMS System',
            'roles' => $roles,
        ];

        return view('admin/user_access', $data);
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

        $firstName = trim((string) $request->getPost('first_name'));
        $middleName = trim((string) $request->getPost('middle_name'));
        $lastName  = trim((string) $request->getPost('last_name'));
        $address   = trim((string) $request->getPost('address'));
        $username  = trim((string) $request->getPost('username'));
        $email     = trim((string) $request->getPost('email'));
        $password  = (string) $request->getPost('password');
        $confirm   = (string) $request->getPost('confirm_password');
        $roleId    = (int) $request->getPost('role_id');
        $status    = trim((string) $request->getPost('status')) ?: 'active';
        $licenseNumber = trim((string) $request->getPost('license_number'));
        $specialization = trim((string) $request->getPost('specialization'));

        if ($password !== $confirm) {
            $this->session->setFlashdata('error', 'Password and confirmation do not match.');
            return redirect()->back()->withInput();
        }

        if ($email === '' || $password === '' || ! $roleId) {
            $this->session->setFlashdata('error', 'Email, password, and role are required.');
            return redirect()->back()->withInput();
        }

        $db = db_connect();

        // Check for existing email
        $exists = $db->table('users')->where('email', $email)->get()->getRowArray();
        if ($exists) {
            $this->session->setFlashdata('error', 'A user with this email already exists.');
            return redirect()->back()->withInput();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userData = [
            'username'   => $username !== '' ? $username : null,
            'email'      => $email,
            'password'   => $passwordHash,
            'first_name' => $firstName !== '' ? $firstName : null,
            'middle_name' => $middleName !== '' ? $middleName : null,
            'last_name'  => $lastName !== '' ? $lastName : null,
            'address'    => $address !== '' ? $address : null,
            'role_id'    => $roleId,
            'status'     => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('users')->insert($userData);
        $userId = $db->insertID();

        // If role is doctor, create doctor profile so it appears in scheduling doctor list
        if ($roleId === 3) {
            $fullName = trim(($firstName ?: '') . ' ' . ($middleName ?: '') . ' ' . ($lastName ?: ''));
            if (trim($fullName) === '') {
                $fullName = $username ?: $email;
            }

            $doctorData = [
                'user_id'        => $userId,
                'full_name'      => $fullName,
                'specialization' => $specialization !== '' ? $specialization : null,
                'license_number' => $licenseNumber !== '' ? $licenseNumber : null,
                'status'         => 'active',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];

            $db->table('doctors')->insert($doctorData);
        }
        
        // If role is nurse, we can create a nurse profile if nurses table exists
        // For now, we'll just store the license number if needed in the future
        if ($roleId === 4 && $licenseNumber !== '') {
            // Check if nurses table exists
            if ($db->tableExists('nurses')) {
                $fullName = trim(($firstName ?: '') . ' ' . ($middleName ?: '') . ' ' . ($lastName ?: ''));
                if (trim($fullName) === '') {
                    $fullName = $username ?: $email;
                }

                $nurseData = [
                    'user_id'        => $userId,
                    'full_name'      => $fullName,
                    'license_number' => $licenseNumber,
                    'status'         => 'active',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ];

                $db->table('nurses')->insert($nurseData);
            }
        }

        $this->session->setFlashdata('success', 'User account has been created successfully.');
        return redirect()->to(base_url('admin/user-access'));
    }
}
