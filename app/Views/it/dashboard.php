<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">IT Dashboard</div>
        <div class="page-subtitle">System maintenance, security checks, and backups overview.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Open Tickets</div>
        </div>
        <div class="card-value"><?= number_format($openTickets ?? 0) ?></div>
        <?php if (($openTickets ?? 0) > 0): ?>
            <div class="card-trend" style="color: #ef4444;">Requires attention</div>
        <?php else: ?>
            <div class="card-trend" style="color: #10b981;">All clear</div>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Last Backup</div>
        </div>
        <?php if (!empty($lastBackup)): ?>
            <div class="card-value" style="font-size: 14px; line-height: 1.4;">
                <?= esc($lastBackup['date']) ?><br>
                <span style="font-size: 12px; color: #6b7280;"><?= esc($lastBackup['time']) ?> • <?= number_format($lastBackup['size_mb'], 2) ?> MB</span>
            </div>
            <div class="card-trend" style="color: #10b981;">Backup successful</div>
        <?php else: ?>
            <div class="card-value" style="color: #6b7280;">No backups yet</div>
            <div class="card-trend" style="color: #f59e0b;">Create first backup</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
