<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Auth extends BaseController
{
    public function index()
    {
        // Root route - redirect to dashboard if logged in, otherwise to login
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            $redirectUrl = $this->getRoleDashboardUrl($role);
            return redirect()->to($redirectUrl);
        }

        return redirect()->to(site_url('login'));
    }

    public function login()
    {
        // If already logged in, redirect to role-specific dashboard
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            $redirectUrl = $this->getRoleDashboardUrl($role);
            return redirect()->to($redirectUrl);
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
        $roleName = null;
        if (!empty($user['role_id'])) {
            $role = $roleModel->find($user['role_id']);
            if ($role && isset($role['name'])) {
                $roleName = $role['name'];
            }
        }

        $sessionData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'] ?? null,
            'email'      => $user['email'],
            'first_name' => $user['first_name'] ?? null,
            'last_name'  => $user['last_name'] ?? null,
            'role'       => $roleName,
            'role_id'    => $user['role_id'] ?? null,
            'isLoggedIn' => true,
        ];

        $this->session->set($sessionData);

        // Redirect directly to role-specific dashboard
        $redirectUrl = $this->getRoleDashboardUrl($roleName);
        return redirect()->to($redirectUrl);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(site_url('/'));
    }

    public function dashboard()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        $role = $this->session->get('role');
        $redirectUrl = $this->getRoleDashboardUrl($role);
        return redirect()->to($redirectUrl);
    }

    /**
     * Get the dashboard URL based on user role
     * Returns full absolute URL using site_url() to prevent relative path issues
     */
    private function getRoleDashboardUrl(?string $role): string
    {
        // Use site_url() to generate full absolute URL to prevent relative redirect issues
        switch ($role) {
            case 'admin':
            case 'hospital_administrator':
                return site_url('admin/dashboard');
            case 'doctor':
                return site_url('doctor/dashboard');
            case 'nurse':
                return site_url('nurse/dashboard');
            case 'receptionist':
                return site_url('receptionist/dashboard');
            case 'lab_staff':
            case 'laboratory_staff':
                return site_url('lab/dashboard');
            case 'pharmacist':
                return site_url('pharmacy/dashboard');
            case 'accountant':
                return site_url('accounts/dashboard');
            case 'it_staff':
            case 'it':
                return site_url('it/dashboard');
            default:
                return site_url('admin/dashboard');
        }
    }
}
