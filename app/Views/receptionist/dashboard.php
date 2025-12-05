<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Receptionist Dashboard</div>
        <div class="page-subtitle">Patient registration and appointment booking overview.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Check-ins Today</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Upcoming Appointments</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
