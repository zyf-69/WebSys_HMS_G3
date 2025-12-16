<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_group',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public function setSetting(string $key, $value, string $group = 'general'): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        
        $data = [
            'setting_key' => $key,
            'setting_value' => is_array($value) ? json_encode($value) : (string)$value,
            'setting_group' => $group,
        ];

        try {
            if ($existing) {
                // Check if value actually changed
                if ($existing['setting_value'] === (string)$data['setting_value'] && 
                    $existing['setting_group'] === $group) {
                    // No change needed, but still return true
                    return true;
                }
                $result = $this->update($existing['id'], $data);
                // Update returns number of affected rows (0 or 1), or false on error
                return $result !== false;
            } else {
                $result = $this->insert($data);
                // Insert returns the ID or false
                return $result !== false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in setSetting: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return array
     */
    public function getSettingsByGroup(string $group = 'general'): array
    {
        $settings = $this->where('setting_group', $group)->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }

    /**
     * Get all settings as key-value pairs
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }

    /**
     * Save multiple settings at once
     *
     * @param array $settings
     * @param string $group
     * @return bool
     */
    public function saveSettings(array $settings, string $group = 'general'): bool
    {
        log_message('debug', 'saveSettings called with group: ' . $group . ', settings: ' . json_encode($settings));
        
        try {
            $allSuccess = true;
            $savedCount = 0;
            
            foreach ($settings as $key => $value) {
                $existing = $this->where('setting_key', $key)->first();
                
                $data = [
                    'setting_key' => $key,
                    'setting_value' => is_array($value) ? json_encode($value) : (string)$value,
                    'setting_group' => $group,
                ];
                
                log_message('debug', "Processing setting: {$key} = " . (string)$value);
                
                if ($existing) {
                    // Always update to ensure timestamps are updated
                    $result = $this->update($existing['id'], $data);
                    if ($result === false) {
                        $allSuccess = false;
                        log_message('error', "Failed to update setting: {$key}");
                    } else {
                        $savedCount++;
                        log_message('debug', "Updated setting: {$key}");
                    }
                } else {
                    // Insert new setting
                    $result = $this->insert($data);
                    if ($result === false) {
                        $allSuccess = false;
                        log_message('error', "Failed to insert setting: {$key}");
                    } else {
                        $savedCount++;
                        log_message('debug', "Inserted setting: {$key}");
                    }
                }
            }
            
            log_message('debug', "Settings save completed. Success: {$allSuccess}, Saved count: {$savedCount}");
            
            return $allSuccess;
        } catch (\Exception $e) {
            log_message('error', 'Error saving settings: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}

