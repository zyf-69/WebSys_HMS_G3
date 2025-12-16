<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class UserAccess extends BaseController
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
        log_message('error', '=== USER ACCESS STORE METHOD CALLED ===');
        log_message('error', 'Request method: ' . $this->request->getMethod());
        log_message('error', 'Request URI: ' . $this->request->getUri()->getPath());
        log_message('error', 'POST data: ' . json_encode($this->request->getPost()));
        
        $result = $this->requireLogin();
        if ($result !== true) {
            log_message('error', 'User not logged in');
            return $result;
        }

        if (! $this->hasRole('admin')) {
            log_message('error', 'User does not have admin role');
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

        // Validate password against security settings
        $securitySettings = $this->settingsModel->getSettingsByGroup('security');
        $minLength = (int)($securitySettings['min_password_length'] ?? 8);
        $requireUppercase = ($securitySettings['require_uppercase'] ?? '1') === '1';
        $requireLowercase = ($securitySettings['require_lowercase'] ?? '1') === '1';
        $requireNumbers = ($securitySettings['require_numbers'] ?? '1') === '1';
        $requireSymbols = ($securitySettings['require_symbols'] ?? '0') === '1';

        $passwordErrors = [];

        // Check minimum length
        if (strlen($password) < $minLength) {
            $passwordErrors[] = "Password must be at least {$minLength} characters long.";
        }

        // Check uppercase requirement
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one uppercase letter.";
        }

        // Check lowercase requirement
        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one lowercase letter.";
        }

        // Check numbers requirement
        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $passwordErrors[] = "Password must contain at least one number.";
        }

        // Check symbols requirement
        if ($requireSymbols && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $passwordErrors[] = "Password must contain at least one special character.";
        }

        if (!empty($passwordErrors)) {
            $this->session->setFlashdata('error', implode(' ', $passwordErrors));
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

        log_message('error', 'User account created successfully with ID: ' . $userId);
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
        // If accessed via GET, redirect back to edit form instead of 404
        $method = strtolower($this->request->getMethod());
        if ($method === 'get') {
            return redirect()->to(base_url('admin/user-access/edit/' . $id));
        }

        $result = $this->requireLogin();
        if ($result !== true) {
            if ($method === 'patch' || $this->request->getHeaderLine('Content-Type') === 'application/json') {
                return $this->response->setJSON(['success' => false, 'error' => 'Authentication required'])->setStatusCode(401);
            }
            return $result;
        }

        if (! $this->hasRole('admin')) {
            if ($method === 'patch' || $this->request->getHeaderLine('Content-Type') === 'application/json') {
                return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(403);
            }
            return redirect()->to(base_url('dashboard'));
        }

        $request = $this->request;
        $db = db_connect();
        $isApiRequest = ($method === 'patch' || $request->getHeaderLine('Content-Type') === 'application/json');

        // Check if user exists
        $existingUser = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$existingUser) {
            if ($isApiRequest) {
                return $this->response->setJSON(['success' => false, 'error' => 'User not found'])->setStatusCode(404);
            }
            $this->session->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/user-access'));
        }

        // Get data from POST or JSON
        if ($isApiRequest) {
            $data = $request->getJSON(true) ?? [];
            $firstName = trim((string) ($data['first_name'] ?? ''));
            $middleName = trim((string) ($data['middle_name'] ?? ''));
            $lastName  = trim((string) ($data['last_name'] ?? ''));
            $address   = trim((string) ($data['address'] ?? ''));
            $username  = trim((string) ($data['username'] ?? ''));
            $email     = trim((string) ($data['email'] ?? ''));
            $password  = (string) ($data['password'] ?? '');
            $roleId    = (int) ($data['role_id'] ?? 0);
            $status    = trim((string) ($data['status'] ?? 'active')) ?: 'active';
            $licenseNumber = trim((string) ($data['license_number'] ?? ''));
            $specialization = trim((string) ($data['specialization'] ?? ''));
        } else {
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
        }

        // Role restriction: System Admin (ID = 1) cannot change their own role
        if ($id == 1 && $roleId != $existingUser['role_id']) {
            if ($isApiRequest) {
                return $this->response->setJSON(['success' => false, 'error' => 'System Admin cannot change their own role'])->setStatusCode(403);
            }
            $this->session->setFlashdata('error', 'System Admin cannot change their own role.');
            return redirect()->back()->withInput();
        }

        if ($email === '' || ! $roleId) {
            if ($isApiRequest) {
                return $this->response->setJSON(['success' => false, 'error' => 'Email and role are required'])->setStatusCode(400);
            }
            $this->session->setFlashdata('error', 'Email and role are required.');
            return redirect()->back()->withInput();
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($isApiRequest) {
                return $this->response->setJSON(['success' => false, 'error' => 'Invalid email format'])->setStatusCode(400);
            }
            $this->session->setFlashdata('error', 'Invalid email format.');
            return redirect()->back()->withInput();
        }

        // Check for existing email (excluding current user)
        $exists = $db->table('users')
            ->where('email', $email)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();
        if ($exists) {
            if ($isApiRequest) {
                return $this->response->setJSON(['success' => false, 'error' => 'A user with this email already exists'])->setStatusCode(400);
            }
            $this->session->setFlashdata('error', 'A user with this email already exists.');
            return redirect()->back()->withInput();
        }

        // Check username uniqueness (if username is provided)
        if ($username !== '') {
            $usernameExists = $db->table('users')
                ->where('username', $username)
                ->where('id !=', $id)
                ->get()
                ->getRowArray();
            if ($usernameExists) {
                if ($isApiRequest) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Username already taken'])->setStatusCode(400);
                }
                $this->session->setFlashdata('error', 'Username already taken.');
                return redirect()->back()->withInput();
            }
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

        // Update password only if provided (trim to avoid accidental spaces)
        $password = trim($password);
        if ($password !== '') {
            // Validate password against security settings
            $securitySettings = $this->settingsModel->getSettingsByGroup('security');
            $minLength = (int)($securitySettings['min_password_length'] ?? 8);
            $requireUppercase = ($securitySettings['require_uppercase'] ?? '1') === '1';
            $requireLowercase = ($securitySettings['require_lowercase'] ?? '1') === '1';
            $requireNumbers = ($securitySettings['require_numbers'] ?? '1') === '1';
            $requireSymbols = ($securitySettings['require_symbols'] ?? '0') === '1';

            $passwordErrors = [];

            // Check minimum length
            if (strlen($password) < $minLength) {
                $passwordErrors[] = "Password must be at least {$minLength} characters long.";
            }

            // Check uppercase requirement
            if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
                $passwordErrors[] = "Password must contain at least one uppercase letter.";
            }

            // Check lowercase requirement
            if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
                $passwordErrors[] = "Password must contain at least one lowercase letter.";
            }

            // Check numbers requirement
            if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = "Password must contain at least one number.";
            }

            // Check symbols requirement
            if ($requireSymbols && !preg_match('/[^A-Za-z0-9]/', $password)) {
                $passwordErrors[] = "Password must contain at least one special character.";
            }

            if (!empty($passwordErrors)) {
                if ($isApiRequest) {
                    return $this->response->setJSON(['success' => false, 'error' => implode(' ', $passwordErrors)])->setStatusCode(400);
                }
                $this->session->setFlashdata('error', implode(' ', $passwordErrors));
                return redirect()->back()->withInput();
            }

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

        // Return JSON response for API requests
        if ($isApiRequest) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User account has been updated successfully.',
                'redirect' => base_url('admin/user-access')
            ]);
        }

        $this->session->setFlashdata('success', 'User account has been updated successfully.');
        return redirect()->to(base_url('admin/user-access'));
    }
}
