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

        // Get all users with their roles
        $users = $db->table('users u')
            ->select('u.id, u.username, u.email, u.first_name, u.middle_name, u.last_name, u.address, u.status, u.created_at, r.name as role_name, r.display_name as role_display_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'User Access & Security | HMS System',
            'roles' => $roles,
            'users' => $users,
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

    public function edit($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (! $this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();
        
        // Get user with role
        $user = $db->table('users u')
            ->select('u.*, r.name as role_name, r.display_name as role_display_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.id', $id)
            ->get()
            ->getRowArray();

        if (!$user) {
            $this->session->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/user-access'));
        }

        // Get roles for dropdown
        $roles = $db->table('roles')
            ->select('id, name, display_name')
            ->where('is_active', 1)
            ->orderBy('level', 'DESC')
            ->get()->getResultArray();

        // Get doctor/nurse info if applicable
        $doctorInfo = null;
        $nurseInfo = null;
        if ($user['role_id'] == 3) { // Doctor
            $doctorInfo = $db->table('doctors')
                ->where('user_id', $id)
                ->get()
                ->getRowArray();
        } elseif ($user['role_id'] == 4 && $db->tableExists('nurses')) { // Nurse
            $nurseInfo = $db->table('nurses')
                ->where('user_id', $id)
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Edit User | HMS System',
            'user' => $user,
            'roles' => $roles,
            'doctorInfo' => $doctorInfo,
            'nurseInfo' => $nurseInfo,
        ];

        return view('admin/user_edit', $data);
    }

    public function update($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (! $this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $request = $this->request;
        $db = db_connect();

        // Check if user exists
        $existingUser = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$existingUser) {
            $this->session->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/user-access'));
        }

        $firstName = trim((string) $request->getPost('first_name'));
        $middleName = trim((string) $request->getPost('middle_name'));
        $lastName  = trim((string) $request->getPost('last_name'));
        $address   = trim((string) $request->getPost('address'));
        $username  = trim((string) $request->getPost('username'));
        $email     = trim((string) $request->getPost('email'));
        $password  = (string) $request->getPost('password');
        $roleId    = (int) $request->getPost('role_id');
        $status    = trim((string) $request->getPost('status')) ?: 'active';
        $licenseNumber = trim((string) $request->getPost('license_number'));
        $specialization = trim((string) $request->getPost('specialization'));

        if ($email === '' || ! $roleId) {
            $this->session->setFlashdata('error', 'Email and role are required.');
            return redirect()->back()->withInput();
        }

        // Check for existing email (excluding current user)
        $exists = $db->table('users')
            ->where('email', $email)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();
        if ($exists) {
            $this->session->setFlashdata('error', 'A user with this email already exists.');
            return redirect()->back()->withInput();
        }

        $userData = [
            'username'   => $username !== '' ? $username : null,
            'email'      => $email,
            'first_name' => $firstName !== '' ? $firstName : null,
            'middle_name' => $middleName !== '' ? $middleName : null,
            'last_name'  => $lastName !== '' ? $lastName : null,
            'address'    => $address !== '' ? $address : null,
            'role_id'    => $roleId,
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Update password only if provided
        if ($password !== '') {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db->table('users')->where('id', $id)->update($userData);

        // Update doctor/nurse info if applicable
        if ($roleId === 3) { // Doctor
            $fullName = trim(($firstName ?: '') . ' ' . ($middleName ?: '') . ' ' . ($lastName ?: ''));
            if (trim($fullName) === '') {
                $fullName = $username ?: $email;
            }

            $doctorData = [
                'full_name'      => $fullName,
                'specialization' => $specialization !== '' ? $specialization : null,
                'license_number' => $licenseNumber !== '' ? $licenseNumber : null,
                'updated_at'     => date('Y-m-d H:i:s'),
            ];

            // Check if doctor record exists
            $doctor = $db->table('doctors')->where('user_id', $id)->get()->getRowArray();
            if ($doctor) {
                $db->table('doctors')->where('user_id', $id)->update($doctorData);
            } else {
                $doctorData['user_id'] = $id;
                $doctorData['status'] = 'active';
                $doctorData['created_at'] = date('Y-m-d H:i:s');
                $db->table('doctors')->insert($doctorData);
            }
        }
        
        if ($roleId === 4 && $db->tableExists('nurses')) { // Nurse
            $fullName = trim(($firstName ?: '') . ' ' . ($middleName ?: '') . ' ' . ($lastName ?: ''));
            if (trim($fullName) === '') {
                $fullName = $username ?: $email;
            }

            $nurseData = [
                'full_name'      => $fullName,
                'license_number' => $licenseNumber !== '' ? $licenseNumber : null,
                'updated_at'     => date('Y-m-d H:i:s'),
            ];

            $nurse = $db->table('nurses')->where('user_id', $id)->get()->getRowArray();
            if ($nurse) {
                $db->table('nurses')->where('user_id', $id)->update($nurseData);
            } else {
                $nurseData['user_id'] = $id;
                $nurseData['status'] = 'active';
                $nurseData['created_at'] = date('Y-m-d H:i:s');
                $db->table('nurses')->insert($nurseData);
            }
        }

        $this->session->setFlashdata('success', 'User account has been updated successfully.');
        return redirect()->to(base_url('admin/user-access'));
    }
}
