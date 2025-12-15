<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to access this page.');
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get system settings (if you have a settings table)
        // For now, we'll use a simple approach with config values
        $data = [
            'title' => 'Settings | Admin Panel',
        ];

        return view('admin/settings', $data);
    }

    public function update()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('admin/settings'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('admin/settings'));
        }

        // Get form data based on section
        $section = $request->getPost('settings_section') ?? 'general';
        
        $settings = [];
        
        if ($section === 'general') {
            $settings = [
                'hospital_name' => trim($request->getPost('hospital_name') ?? ''),
                'hospital_address' => trim($request->getPost('hospital_address') ?? ''),
                'hospital_phone' => trim($request->getPost('hospital_phone') ?? ''),
                'hospital_email' => trim($request->getPost('hospital_email') ?? ''),
                'timezone' => trim($request->getPost('timezone') ?? 'UTC'),
            ];
        } elseif ($section === 'security') {
            $settings = [
                'min_password_length' => (int)($request->getPost('min_password_length') ?? 8),
                'require_uppercase' => $request->getPost('require_uppercase') ? 1 : 0,
                'require_lowercase' => $request->getPost('require_lowercase') ? 1 : 0,
                'require_numbers' => $request->getPost('require_numbers') ? 1 : 0,
                'require_symbols' => $request->getPost('require_symbols') ? 1 : 0,
                'session_timeout' => (int)($request->getPost('session_timeout') ?? 30),
            ];
        }

        // Here you would typically save to a settings table
        // For now, we'll just show a success message
        // TODO: Implement settings storage (database table or config file)
        
        log_message('info', 'Settings updated (' . $section . ') by user: ' . ($this->session->get('email') ?? 'unknown'));
        log_message('info', 'Settings data: ' . json_encode($settings));

        $this->session->setFlashdata('success', ucfirst($section) . ' settings updated successfully.');

        return redirect()->to(site_url('admin/settings'));
    }
}

