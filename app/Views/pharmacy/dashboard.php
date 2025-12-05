<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Pharmacy Dashboard</div>
        <div class="page-subtitle">Prescriptions, dispensing queue, and stock alerts.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Prescriptions Today</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Low Stock Items</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
