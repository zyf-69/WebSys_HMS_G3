<?php

namespace App\Controllers\It;

use App\Controllers\BaseController;

class Backups extends BaseController
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR;
        
        // Create backups directory if it doesn't exist
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(site_url('dashboard'));
        }

        // Get list of backups
        $backups = $this->getBackupList();
        
        // Calculate storage used
        $storageUsed = $this->calculateStorageUsed();
        $storageTotal = 10 * 1024 * 1024 * 1024; // 10 GB default
        $storagePercent = $storageTotal > 0 ? ($storageUsed / $storageTotal) * 100 : 0;

        $data = [
            'title' => 'Backups | IT Panel',
            'backups' => $backups,
            'storageUsed' => $storageUsed,
            'storageTotal' => $storageTotal,
            'storagePercent' => min(100, max(0, $storagePercent)),
        ];

        return view('it/backups', $data);
    }

    public function create()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('it/backups'));
        }

        try {
            $backupFile = $this->createBackup();
            
            if ($backupFile) {
                $this->session->setFlashdata('success', 'Database backup created successfully.');
                log_message('info', 'Backup created: ' . $backupFile . ' by user: ' . ($this->session->get('email') ?? 'unknown'));
            } else {
                $this->session->setFlashdata('error', 'Failed to create backup. Please check server permissions and try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup creation failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Error creating backup: ' . $e->getMessage());
        }

        return redirect()->to(site_url('it/backups'));
    }

    public function download($filename)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(site_url('dashboard'));
        }

        // Security: prevent directory traversal
        $filename = basename($filename);
        $filePath = $this->backupPath . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->session->setFlashdata('error', 'Backup file not found.');
            return redirect()->to(site_url('it/backups'));
        }

        // Set headers for file download
        return $this->response->download($filename, file_get_contents($filePath));
    }

    public function restore($filename)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('it/backups'));
        }

        // Security: prevent directory traversal
        $filename = basename($filename);
        $filePath = $this->backupPath . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->session->setFlashdata('error', 'Backup file not found.');
            return redirect()->to(site_url('it/backups'));
        }

        try {
            $success = $this->restoreBackup($filePath);
            
            if ($success) {
                $this->session->setFlashdata('success', 'Database restored successfully from backup.');
                log_message('info', 'Database restored from: ' . $filename . ' by user: ' . ($this->session->get('email') ?? 'unknown'));
            } else {
                $this->session->setFlashdata('error', 'Failed to restore backup. Please check the backup file and try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup restore failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Error restoring backup: ' . $e->getMessage());
        }

        return redirect()->to(site_url('it/backups'));
    }

    public function delete($filename)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('it/backups'));
        }

        // Security: prevent directory traversal
        $filename = basename($filename);
        $filePath = $this->backupPath . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->session->setFlashdata('error', 'Backup file not found.');
            return redirect()->to(site_url('it/backups'));
        }

        if (unlink($filePath)) {
            $this->session->setFlashdata('success', 'Backup deleted successfully.');
            log_message('info', 'Backup deleted: ' . $filename . ' by user: ' . ($this->session->get('email') ?? 'unknown'));
        } else {
            $this->session->setFlashdata('error', 'Failed to delete backup file.');
        }

        return redirect()->to(site_url('it/backups'));
    }

    /**
     * Create a database backup
     */
    protected function createBackup(): ?string
    {
        $db = \Config\Database::connect();
        $dbConfig = config('Database');
        $defaultGroup = $dbConfig->defaultGroup;
        $config = $dbConfig->{$defaultGroup};

        $timestamp = date('Y-m-d_H-i-s');
        $filename = 'backup_' . $timestamp . '.sql';
        $filePath = $this->backupPath . $filename;

        // Try using mysqldump first (more efficient)
        $mysqldumpPath = $this->findMysqldump();
        
        if ($mysqldumpPath && $this->canExecuteCommand()) {
            $command = sprintf(
                '"%s" --host=%s --port=%d --user=%s --password=%s %s > "%s"',
                $mysqldumpPath,
                escapeshellarg($config['hostname']),
                $config['port'] ?? 3306,
                escapeshellarg($config['username']),
                escapeshellarg($config['password']),
                escapeshellarg($config['database']),
                $filePath
            );

            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($filePath) && filesize($filePath) > 0) {
                return $filename;
            }
        }

        // Fallback: PHP-based SQL export
        return $this->createBackupPHP($filename, $filePath, $db);
    }

    /**
     * Create backup using PHP (fallback method)
     */
    protected function createBackupPHP(string $filename, string $filePath, $db): ?string
    {
        $tables = $db->listTables();
        $output = '';

        // Add header
        $output .= "-- Database Backup\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: " . $db->database . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Get table structure
            $createTable = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if (isset($createTable['Create Table'])) {
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createTable['Create Table'] . ";\n\n";
            }

            // Get table data
            $rows = $db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                // Get column names
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($columns as $col) {
                        $value = $row[$col] ?? null;
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            // Properly escape the value
                            $escaped = str_replace(['\\', "'", "\n", "\r"], ['\\\\', "\\'", "\\n", "\\r"], $value);
                            $rowValues[] = "'" . $escaped . "'";
                        }
                    }
                    $values[] = '(' . implode(',', $rowValues) . ')';
                }
                $output .= implode(",\n", $values) . ";\n\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        if (file_put_contents($filePath, $output) !== false) {
            return $filename;
        }

        return null;
    }

    /**
     * Restore database from backup file
     */
    protected function restoreBackup(string $filePath): bool
    {
        $db = \Config\Database::connect();
        $dbConfig = config('Database');
        $defaultGroup = $dbConfig->defaultGroup;
        $config = $dbConfig->{$defaultGroup};

        // Try using mysql command first (more reliable)
        $mysqlPath = $this->findMysql();
        
        if ($mysqlPath && $this->canExecuteCommand()) {
            $command = sprintf(
                '"%s" --host=%s --port=%d --user=%s --password=%s %s < "%s"',
                $mysqlPath,
                escapeshellarg($config['hostname']),
                $config['port'] ?? 3306,
                escapeshellarg($config['username']),
                escapeshellarg($config['password']),
                escapeshellarg($config['database']),
                $filePath
            );

            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0) {
                return true;
            }
        }

        // Fallback: PHP-based restore
        return $this->restoreBackupPHP($filePath, $db);
    }

    /**
     * Restore backup using PHP (fallback method)
     */
    protected function restoreBackupPHP(string $filePath, $db): bool
    {
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            return false;
        }

        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split into statements (simple approach - split by semicolon)
        // Note: This is a simplified approach. For production, consider using a proper SQL parser
        $allStatements = explode(';', $sql);
        $statements = [];
        foreach ($allStatements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt) && strlen($stmt) > 5) {
                // Skip SET statements and comments
                if (!preg_match('#^(SET|/\*|--)#i', $stmt)) {
                    $statements[] = $stmt;
                }
            }
        }

        $db->transStart();
        try {
            foreach ($statements as $statement) {
                if (!empty($statement) && strlen($statement) > 5) {
                    $db->query($statement);
                }
            }
            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Restore error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find mysql executable
     */
    protected function findMysql(): ?string
    {
        $paths = [
            'mysql',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\wamp\\bin\\mysql\\mysql5.7.9\\bin\\mysql.exe',
        ];

        foreach ($paths as $path) {
            if ($this->commandExists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get list of backup files
     */
    protected function getBackupList(): array
    {
        $backups = [];
        
        if (!is_dir($this->backupPath)) {
            return $backups;
        }

        $files = glob($this->backupPath . 'backup_*.sql');
        
        foreach ($files as $file) {
            $filename = basename($file);
            $fileInfo = [
                'filename' => $filename,
                'size' => filesize($file),
                'size_mb' => round(filesize($file) / 1024 / 1024, 2),
                'created_at' => date('Y-m-d H:i', filemtime($file)),
                'created_at_full' => date('M d, Y H:i', filemtime($file)),
                'status' => 'success',
            ];
            $backups[] = $fileInfo;
        }

        // Sort by creation time (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Calculate total storage used by backups
     */
    protected function calculateStorageUsed(): int
    {
        $total = 0;
        
        if (!is_dir($this->backupPath)) {
            return $total;
        }

        $files = glob($this->backupPath . 'backup_*.sql');
        foreach ($files as $file) {
            $total += filesize($file);
        }

        return $total;
    }

    /**
     * Find mysqldump executable
     */
    protected function findMysqldump(): ?string
    {
        $paths = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql5.7.9\\bin\\mysqldump.exe',
        ];

        foreach ($paths as $path) {
            if ($this->commandExists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Check if command exists and is executable
     */
    protected function commandExists(string $command): bool
    {
        $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';
        $process = proc_open(
            "$whereIsCommand $command",
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );
        
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            proc_close($process);
            return !empty($stdout);
        }
        
        return false;
    }

    /**
     * Check if we can execute shell commands
     */
    protected function canExecuteCommand(): bool
    {
        return function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')));
    }
}

