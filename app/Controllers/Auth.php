<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Login | HMS System',
        ];

        return view('auth/login', $data);
    }

    public function loginPost()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            $this->session->setFlashdata('error', 'Email and password are required.');
            return redirect()->back()->withInput();
        }

        $userModel = new UserModel();
        $roleModel = new RoleModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->session->setFlashdata('error', 'Invalid credentials.');
            return redirect()->back()->withInput();
        }

        if (isset($user['status']) && $user['status'] !== 'active') {
            $this->session->setFlashdata('error', 'Your account is not active.');
            return redirect()->back()->withInput();
        }

        $role = null;
        if (!empty($user['role_id'])) {
            $role = $roleModel->find($user['role_id']);
        }

        $sessionData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'] ?? null,
            'email'      => $user['email'],
            'first_name' => $user['first_name'] ?? null,
            'last_name'  => $user['last_name'] ?? null,
            'role'       => $role['name'] ?? null,
            'role_id'    => $user['role_id'] ?? null,
            'isLoggedIn' => true,
        ];

        $this->session->set($sessionData);

        return redirect()->to(base_url('dashboard'));
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('/'));
    }

    public function dashboard()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        $role = $this->session->get('role');

        switch ($role) {
            case 'admin':
            case 'hospital_administrator':
                return redirect()->to(base_url('admin/dashboard'));
            case 'doctor':
                return redirect()->to(base_url('doctor/dashboard'));
            case 'nurse':
                return redirect()->to(base_url('nurse/dashboard'));
            case 'receptionist':
                return redirect()->to(base_url('receptionist/dashboard'));
            case 'lab_staff':
            case 'laboratory_staff':
                return redirect()->to(base_url('lab/dashboard'));
            case 'pharmacist':
                return redirect()->to(base_url('pharmacy/dashboard'));
            case 'accountant':
                return redirect()->to(base_url('accounts/dashboard'));
            case 'it_staff':
            case 'it':
                return redirect()->to(base_url('it/dashboard'));
            default:
                return redirect()->to(base_url('admin/dashboard'));
        }
    }
}
