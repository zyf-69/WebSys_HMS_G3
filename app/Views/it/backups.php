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
    .btn-delete {
        background: #ef4444;
        color: #ffffff;
    }
    .btn-delete:hover {
        background: #dc2626;
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

<?php 
$errorFlash = session()->getFlashdata('error');
$successFlash = session()->getFlashdata('success');
?>
<?php if ($errorFlash): ?>
    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fecaca;">
        <?= esc($errorFlash) ?>
    </div>
<?php endif; ?>

<?php if ($successFlash): ?>
    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #bbf7d0;">
        <?= esc($successFlash) ?>
    </div>
<?php endif; ?>

<div class="backup-card">
    <form action="/WebSys_HMS_G3/it/backups/create" method="post" style="display: inline;">
        <?= csrf_field() ?>
        <button type="submit" class="btn-create" id="create-backup-btn">Create New Backup</button>
    </form>
    <h3>Backup History</h3>
    <?php if (empty($backups ?? [])): ?>
        <div class="empty-state">
            <div class="empty-state-icon">💾</div>
            <div>No backups found. Create your first backup to get started.</div>
        </div>
    <?php else: ?>
        <ul class="backup-list">
            <?php foreach ($backups as $backup): ?>
            <li class="backup-item">
                <div class="backup-info">
                    <div class="backup-name">Database Backup - <?= esc($backup['created_at_full']) ?></div>
                    <div class="backup-details">Full database backup • Size: <?= number_format($backup['size_mb'], 2) ?> MB</div>
                </div>
                <div style="display: flex; align-items: center;">
                    <span class="backup-status status-<?= esc($backup['status']) ?>"><?= ucfirst(esc($backup['status'])) ?></span>
                    <div class="backup-actions">
                        <a href="/WebSys_HMS_G3/it/backups/download/<?= urlencode($backup['filename']) ?>" class="btn-backup btn-download" style="text-decoration: none; display: inline-block;">Download</a>
                        <button class="btn-backup btn-restore" onclick="confirmRestore('<?= esc($backup['filename']) ?>', '<?= esc($backup['created_at_full']) ?>')">Restore</button>
                        <button class="btn-backup btn-delete" onclick="confirmDelete('<?= esc($backup['filename']) ?>')">Delete</button>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
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
                <span style="font-weight: 600; color: #6b7280;">
                    <?= number_format(($storageUsed ?? 0) / 1024 / 1024 / 1024, 2) ?> / <?= number_format(($storageTotal ?? 0) / 1024 / 1024 / 1024, 2) ?> GB
                </span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: <?= min(100, max(0, $storagePercent ?? 0)) ?>%; background: #3b82f6; transition: width 0.3s;"></div>
            </div>
        </div>
        <div style="font-size: 12px; color: #6b7280;">
            <div style="margin-bottom: 8px;">• Backup retention: 30 days</div>
            <div>• Storage location: Local server (writable/backups/)</div>
            <div>• Total backups: <?= count($backups ?? []) ?></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createBtn = document.getElementById('create-backup-btn');
    if (createBtn) {
        createBtn.closest('form').addEventListener('submit', function(e) {
            createBtn.disabled = true;
            createBtn.textContent = 'Creating Backup...';
        });
    }
});

function confirmRestore(filename, date) {
    if (confirm('WARNING: This will restore the database from backup "' + filename + '" created on ' + date + '.\n\nThis action will REPLACE all current data with the backup data.\n\nAre you sure you want to continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/WebSys_HMS_G3/it/backups/restore/' + encodeURIComponent(filename);
        form.innerHTML = '<?= csrf_field() ?>';
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete(filename) {
    if (confirm('Are you sure you want to delete backup "' + filename + '"?\n\nThis action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/WebSys_HMS_G3/it/backups/delete/' + encodeURIComponent(filename);
        form.innerHTML = '<?= csrf_field() ?>';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?= $this->endSection() ?>

