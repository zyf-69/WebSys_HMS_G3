<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Laboratory Dashboard</div>
        <div class="page-subtitle">Test requests, processing status, and result releases.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Tests</div>
        </div>
        <div class="card-value"><?= number_format($totalTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Tests</div>
        </div>
        <div class="card-value" style="color: #dc2626;"><?= number_format($pendingTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">In Progress</div>
        </div>
        <div class="card-value" style="color: #2563eb;"><?= number_format($inProgressTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Completed Today</div>
        </div>
        <div class="card-value" style="color: #16a34a;"><?= number_format($todayCompleted ?? 0) ?></div>
    </div>
</div>
<?= $this->endSection() ?>
