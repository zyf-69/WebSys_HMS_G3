<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">IT Dashboard</div>
        <div class="page-subtitle">System maintenance, security checks, and backups overview.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Open Tickets</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Last Backup</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
