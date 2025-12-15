<?php

namespace App\Controllers\It;

use App\Controllers\BaseController;

class UserManagement extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get all users with their roles
        $users = $db->table('users u')
            ->select('u.id, u.username, u.email, u.first_name, u.middle_name, u.last_name, u.address, u.status, u.created_at, u.updated_at,
                r.name as role_name, r.display_name as role_display_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate statistics
        $totalUsers = count($users);
        $activeUsers = count(array_filter($users, fn($u) => ($u['status'] ?? 'active') === 'active'));
        $inactiveUsers = $totalUsers - $activeUsers;
        $recentUsers = count(array_filter($users, function($u) {
            $createdDate = $u['created_at'] ?? null;
            return $createdDate && date('Y-m-d', strtotime($createdDate)) === date('Y-m-d');
        }));

        // Get users by role
        $usersByRole = [];
        foreach ($users as $user) {
            $roleName = $user['role_display_name'] ?? ucfirst($user['role_name'] ?? 'No Role');
            if (!isset($usersByRole[$roleName])) {
                $usersByRole[$roleName] = 0;
            }
            $usersByRole[$roleName]++;
        }

        $data = [
            'title' => 'User Management | IT Panel',
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'recentUsers' => $recentUsers,
            'usersByRole' => $usersByRole,
        ];

        return view('it/user_management', $data);
    }

    public function edit($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(site_url('dashboard'));
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
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(site_url('it/user-management'));
        }

        // Get doctor/nurse info if applicable
        $doctorInfo = null;
        $nurseInfo = null;
        $roleId = $user['role_id'] ?? null;
        
        if ($roleId == 3) { // Doctor role_id is 3
            $doctorInfo = $db->table('doctors')
                ->where('user_id', $id)
                ->get()
                ->getRowArray();
        } elseif ($roleId == 4 && $db->tableExists('nurses')) { // Nurse role_id is 4
            $nurseInfo = $db->table('nurses')
                ->where('user_id', $id)
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Edit User | IT Panel',
            'user' => $user,
            'doctorInfo' => $doctorInfo,
            'nurseInfo' => $nurseInfo,
        ];

        return view('it/user_edit', $data);
    }

    public function update($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            session()->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('it/user-management'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('it/user-management/edit/' . $id));
        }

        $db = db_connect();

        // Check if user exists
        $existingUser = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$existingUser) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(site_url('it/user-management'));
        }

        // Get form data (excluding role_id - IT cannot change roles)
        $firstName = trim($request->getPost('first_name') ?? '');
        $middleName = trim($request->getPost('middle_name') ?? '');
        $lastName = trim($request->getPost('last_name') ?? '');
        $address = trim($request->getPost('address') ?? '');
        $username = trim($request->getPost('username') ?? '');
        $email = trim($request->getPost('email') ?? '');
        // Note: role_id is NOT updated by IT staff
        $status = trim($request->getPost('status') ?? 'active');

        // Validation
        if (empty($email)) {
            session()->setFlashdata('error', 'Email is required.');
            return redirect()->to(site_url('it/user-management/edit/' . $id));
        }

        // Check if email is already used by another user
        $emailExists = $db->table('users')
            ->where('email', $email)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();
        if ($emailExists) {
            session()->setFlashdata('error', 'A user with this email already exists.');
            return redirect()->to(site_url('it/user-management/edit/' . $id));
        }

        // Update user data (excluding role_id - IT cannot change roles)
        $updateData = [
            'first_name' => $firstName ?: null,
            'middle_name' => $middleName ?: null,
            'last_name' => $lastName ?: null,
            'address' => $address ?: null,
            'username' => $username ?: null,
            'email' => $email,
            // role_id is NOT included - IT staff cannot change user roles
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $db->table('users')
                ->where('id', $id)
                ->update($updateData);

            session()->setFlashdata('success', 'User updated successfully.');
            return redirect()->to(site_url('it/user-management'));
        } catch (\Exception $e) {
            log_message('error', 'User update failed: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update user. Please try again.');
            return redirect()->to(site_url('it/user-management/edit/' . $id));
        }
    }
}

