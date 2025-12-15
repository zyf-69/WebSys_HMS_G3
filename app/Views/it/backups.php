<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Backups</div>
        <div class="page-subtitle">Manage database backups and restore points.</div>
    </div>
</div>

<style>
    .backup-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 20px;
    }
    .backup-card h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #111827;
    }
    .backup-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .backup-item {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .backup-item:last-child {
        border-bottom: none;
    }
    .backup-item:hover {
        background: #f9fafb;
    }
    .backup-info {
        flex: 1;
    }
    .backup-name {
        font-weight: 600;
        margin-bottom: 4px;
        color: #111827;
    }
    .backup-details {
        font-size: 12px;
        color: #6b7280;
    }
    .backup-actions {
        display: flex;
        gap: 8px;
    }
    .btn-backup {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-download {
        background: #3b82f6;
        color: #ffffff;
    }
    .btn-download:hover {
        background: #2563eb;
    }
    .btn-restore {
        background: #10b981;
        color: #ffffff;
    }
    .btn-restore:hover {
        background: #059669;
    }
    .btn-create {
        background: #3b82f6;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 16px;
    }
    .btn-create:hover {
        background: #2563eb;
    }
    .backup-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 12px;
    }
    .status-success {
        background: #dcfce7;
        color: #166534;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
</style>

<div class="backup-card">
    <button class="btn-create" onclick="alert('Backup creation functionality would be implemented here.')">Create New Backup</button>
    <h3>Backup History</h3>
    <ul class="backup-list">
        <li class="backup-item">
            <div class="backup-info">
                <div class="backup-name">Database Backup - <?= date('M d, Y H:i') ?></div>
                <div class="backup-details">Full database backup • Size: -- MB</div>
            </div>
            <div style="display: flex; align-items: center;">
                <span class="backup-status status-success">Success</span>
                <div class="backup-actions">
                    <button class="btn-backup btn-download" onclick="alert('Download functionality would be implemented here.')">Download</button>
                    <button class="btn-backup btn-restore" onclick="alert('Restore functionality would be implemented here.')">Restore</button>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="backup-card">
    <h3>Backup Schedule</h3>
    <ul class="backup-list">
        <li class="backup-item">
            <div class="backup-info">
                <div class="backup-name">Daily Backup</div>
                <div class="backup-details">Runs every day at 2:00 AM • Full database backup</div>
            </div>
            <span class="backup-status status-success">Active</span>
        </li>
        <li class="backup-item">
            <div class="backup-info">
                <div class="backup-name">Weekly Backup</div>
                <div class="backup-details">Runs every Sunday at 3:00 AM • Full database backup</div>
            </div>
            <span class="backup-status status-success">Active</span>
        </li>
        <li class="backup-item">
            <div class="backup-info">
                <div class="backup-name">Monthly Backup</div>
                <div class="backup-details">Runs on the 1st of each month at 4:00 AM • Full database backup</div>
            </div>
            <span class="backup-status status-success">Active</span>
        </li>
    </ul>
</div>

<div class="backup-card">
    <h3>Backup Storage</h3>
    <div style="padding: 16px;">
        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; color: #374151;">Storage Used</span>
                <span style="font-weight: 600; color: #6b7280;">-- / -- GB</span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: 35%; background: #3b82f6;"></div>
            </div>
        </div>
        <div style="font-size: 12px; color: #6b7280;">
            <div style="margin-bottom: 8px;">• Backup retention: 30 days</div>
            <div>• Storage location: Local server</div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

