<?php

namespace App\Controllers\It;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(base_url('dashboard'));
        }

        // Get open tickets count (if tickets table exists, otherwise show 0)
        $openTickets = $this->getOpenTicketsCount();

        // Get last backup information
        $lastBackup = $this->getLastBackupInfo();

        $data = [
            'title' => 'IT Dashboard | HMS System',
            'openTickets' => $openTickets,
            'lastBackup' => $lastBackup,
        ];

        return view('it/dashboard', $data);
    }

    /**
     * Get count of open tickets
     */
    protected function getOpenTicketsCount(): int
    {
        $db = \Config\Database::connect();
        
        // Check if tickets table exists
        if (!$db->tableExists('tickets')) {
            return 0;
        }

        try {
            $count = $db->table('tickets')
                ->where('status', 'open')
                ->orWhere('status', 'pending')
                ->countAllResults();
            
            return $count;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching open tickets: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get last backup information
     */
    protected function getLastBackupInfo(): ?array
    {
        $backupPath = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR;
        
        if (!is_dir($backupPath)) {
            return null;
        }

        $files = glob($backupPath . 'backup_*.sql');
        
        if (empty($files)) {
            return null;
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $filename = basename($latestFile);
        $fileTime = filemtime($latestFile);
        
        return [
            'filename' => $filename,
            'date' => date('M d, Y', $fileTime),
            'time' => date('H:i', $fileTime),
            'datetime' => date('M d, Y H:i', $fileTime),
            'size_mb' => round(filesize($latestFile) / 1024 / 1024, 2),
        ];
    }
}
