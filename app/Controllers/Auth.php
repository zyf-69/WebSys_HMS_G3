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
        // Log all login page requests for debugging
        log_message('error', '=== LOGIN PAGE ACCESSED (GET) ===');
        log_message('error', 'Request method: ' . $this->request->getMethod());
        log_message('error', 'Request URI: ' . $this->request->getUri()->getPath());
        log_message('error', 'Is logged in: ' . ($this->session->get('isLoggedIn') ? 'YES' : 'NO'));
        
        // If already logged in, redirect to role-specific dashboard
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            $redirectUrl = $this->getRoleDashboardUrl($role);
            log_message('error', 'Already logged in, redirecting to: ' . $redirectUrl);
            return redirect()->to(base_url($redirectUrl));
        }

        // Only show login page for GET requests
        // POST requests should go to loginPost() via routing
        if (!$this->request->is('get')) {
            log_message('error', 'Non-GET request to login() method - this should not happen!');
            // Redirect POST requests back - they should be handled by loginPost()
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Login | HMS System',
        ];

        return view('auth/login', $data);
    }

    public function loginPost()
    {
        // Log the request for debugging - use error level to ensure it's always logged
        log_message('error', '=== LOGIN POST REQUEST RECEIVED ===');
        log_message('error', 'Request method: ' . $this->request->getMethod());
        log_message('error', 'Request URI: ' . $this->request->getUri()->getPath());
        log_message('error', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('error', 'Raw input: ' . $this->request->getBody());

        // Validate request method
        if (!$this->request->is('post')) {
            log_message('error', 'Login failed: Invalid request method');
            $this->session->setFlashdata('error', 'Invalid request method.');
            return redirect()->back()->withInput();
        }

        // Get form data
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        // Validate input
        if (empty($email) || empty($password)) {
            log_message('warning', 'Login failed: Empty email or password');
            $this->session->setFlashdata('error', 'Email and password are required.');
            return redirect()->back()->withInput();
        }

        // Initialize models
        $userModel = new UserModel();
        $roleModel = new RoleModel();

        // Find user by email
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            log_message('warning', 'Login failed: User not found - ' . $email);
            $this->session->setFlashdata('error', 'Invalid email or password.');
            return redirect()->back()->withInput();
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            log_message('warning', 'Login failed: Invalid password for - ' . $email);
            $this->session->setFlashdata('error', 'Invalid email or password.');
            return redirect()->back()->withInput();
        }

        // Check account status
        if (isset($user['status']) && $user['status'] !== 'active') {
            log_message('warning', 'Login failed: Inactive account - ' . $email);
            $this->session->setFlashdata('error', 'Your account is not active. Please contact the administrator.');
            return redirect()->back()->withInput();
        }

        // Get user role
        $role = null;
        $roleName = null;
        if (!empty($user['role_id'])) {
            $role = $roleModel->find($user['role_id']);
            if ($role && isset($role['name'])) {
                $roleName = $role['name'];
            }
        }

        // Prepare session data
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

        // Set session data
        $this->session->set($sessionData);
        
        // Regenerate session ID for security (destroy old session)
        $this->session->regenerate(true);

        // Get redirect URL
        $redirectUrl = $this->getRoleDashboardUrl($roleName);
        
        // Log successful login
        log_message('info', 'User logged in successfully: ' . $email . ' (Role: ' . ($roleName ?? 'unknown') . ') - Redirecting to: ' . $redirectUrl);
        log_message('debug', 'Session data set: ' . json_encode($sessionData));
        log_message('debug', 'Redirect URL: ' . $redirectUrl);
        log_message('debug', 'Base URL: ' . base_url());

        // Redirect to dashboard using relative path
        // redirect()->to() will automatically use baseURL from config
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
     * Returns relative path for redirect
     */
    private function getRoleDashboardUrl(?string $role): string
    {
        // Return relative paths - redirect() will handle the full URL
        switch ($role) {
            case 'admin':
            case 'hospital_administrator':
                return 'admin/dashboard';
            case 'doctor':
                return 'doctor/dashboard';
            case 'nurse':
                return 'nurse/dashboard';
            case 'receptionist':
                return 'receptionist/dashboard';
            case 'lab_staff':
            case 'laboratory_staff':
                return 'lab/dashboard';
            case 'pharmacist':
                return 'pharmacy/dashboard';
            case 'accountant':
                return 'accounts/dashboard';
            case 'it_staff':
            case 'it':
                return 'it/dashboard';
            default:
                return 'admin/dashboard';
        }
    }
}
