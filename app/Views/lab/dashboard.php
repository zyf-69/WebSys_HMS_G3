<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Laboratory Dashboard</div>
        <div class="page-subtitle">Test requests, processing status, and result releases.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Pending Tests</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Completed Today</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
