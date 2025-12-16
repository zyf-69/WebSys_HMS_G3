<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Settings extends BaseController
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

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to access this page.');
            return redirect()->to(site_url('dashboard'));
        }

        // Get all settings
        $generalSettings = $this->settingsModel->getSettingsByGroup('general');
        $securitySettings = $this->settingsModel->getSettingsByGroup('security');

        $data = [
            'title' => 'Settings | Admin Panel',
            'general' => $generalSettings,
            'security' => $securitySettings,
        ];

        return view('admin/settings', $data);
    }

    public function update()
    {
        // Log at error level to ensure it's always visible
        log_message('error', '=== Settings::update() called ===');
        log_message('error', 'Request method: ' . $this->request->getMethod());
        log_message('error', 'Request URI: ' . $this->request->getUri()->getPath());
        log_message('error', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('error', 'Raw POST: ' . file_get_contents('php://input'));
        
        $result = $this->requireLogin();
        if ($result !== true) {
            log_message('debug', 'Login required - redirecting');
            return $result;
        }

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            log_message('debug', 'Permission denied - redirecting');
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('admin/settings'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            log_message('debug', 'Not a POST request - redirecting');
            return redirect()->to(site_url('admin/settings'));
        }
        
        log_message('debug', 'Processing POST request...');

        // Get form data based on section
        $section = $request->getPost('settings_section') ?? 'general';
        
        $settings = [];
        $validation = \Config\Services::validation();
        
        if ($section === 'general') {
            // Validation rules
            $validation->setRules([
                'hospital_name' => [
                    'label' => 'Hospital Name',
                    'rules' => 'required|min_length[2]|max_length[200]',
                ],
                'hospital_address' => [
                    'label' => 'Hospital Address',
                    'rules' => 'required|min_length[5]|max_length[500]',
                ],
                'hospital_phone' => [
                    'label' => 'Phone Number',
                    'rules' => 'permit_empty|max_length[50]',
                ],
                'hospital_email' => [
                    'label' => 'Email Address',
                    'rules' => 'permit_empty|valid_email|max_length[100]',
                ],
                'timezone' => [
                    'label' => 'Timezone',
                    'rules' => 'permit_empty|max_length[50]',
                ],
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
                return redirect()->to(site_url('admin/settings'))->withInput();
            }

            $settings = [
                'hospital_name' => trim($request->getPost('hospital_name') ?? ''),
                'hospital_address' => trim($request->getPost('hospital_address') ?? ''),
                'hospital_phone' => trim($request->getPost('hospital_phone') ?? ''),
                'hospital_email' => trim($request->getPost('hospital_email') ?? ''),
                'timezone' => trim($request->getPost('timezone') ?? 'UTC'),
            ];
        } elseif ($section === 'security') {
            $validation->setRules([
                'min_password_length' => [
                    'label' => 'Minimum Password Length',
                    'rules' => 'required|integer|greater_than_equal_to[6]|less_than_equal_to[32]',
                ],
                'session_timeout' => [
                    'label' => 'Session Timeout',
                    'rules' => 'required|integer|greater_than_equal_to[5]|less_than_equal_to[480]',
                ],
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
                return redirect()->to(site_url('admin/settings'))->withInput();
            }

            $settings = [
                'min_password_length' => (int)($request->getPost('min_password_length') ?? 8),
                'require_uppercase' => $request->getPost('require_uppercase') ? '1' : '0',
                'require_lowercase' => $request->getPost('require_lowercase') ? '1' : '0',
                'require_numbers' => $request->getPost('require_numbers') ? '1' : '0',
                'require_symbols' => $request->getPost('require_symbols') ? '1' : '0',
                'session_timeout' => (int)($request->getPost('session_timeout') ?? 30),
            ];
        }

        // Save settings to database
        if (!empty($settings)) {
            try {
                // Check if settings table exists
                $db = \Config\Database::connect();
                if (!$db->tableExists('settings')) {
                    $this->session->setFlashdata('error', 'Settings table does not exist. Please run the migration: php spark migrate');
                    return redirect()->to(site_url('admin/settings'));
                }
                
                log_message('debug', 'Attempting to save settings for section: ' . $section . ' with data: ' . json_encode($settings));
                
                $saved = $this->settingsModel->saveSettings($settings, $section);
                
                log_message('debug', 'Settings save result: ' . ($saved ? 'SUCCESS' : 'FAILED'));
                
                if ($saved) {
                    log_message('info', 'Settings updated (' . $section . ') by user: ' . ($this->session->get('email') ?? 'unknown'));
                    $successMessage = ucfirst($section) . ' settings updated successfully.';
                    $this->session->setFlashdata('success', $successMessage);
                    
                    // Force session write
                    $this->session->markAsFlashdata('success');
                    
                    log_message('debug', 'Flashdata success message set: ' . $successMessage);
                    log_message('debug', 'Session flashdata after set: ' . json_encode($this->session->getFlashdata()));
                } else {
                    $errorMsg = 'Failed to save settings. Please check the database connection and try again.';
                    log_message('error', 'Settings save failed for section: ' . $section . '. Settings data: ' . json_encode($settings));
                    $this->session->setFlashdata('error', $errorMsg);
                    $this->session->markAsFlashdata('error');
                    log_message('debug', 'Flashdata error message set: ' . $errorMsg);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception saving settings: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                $this->session->setFlashdata('error', 'An error occurred while saving settings. Please check the logs for details.');
            }
        } else {
            $this->session->setFlashdata('error', 'No settings to save.');
        }

        return redirect()->to(site_url('admin/settings'));
    }
}

