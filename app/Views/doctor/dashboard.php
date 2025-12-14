<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Doctor Dashboard</div>
        <div class="page-subtitle">Todays schedule, patient list, and pending tasks.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Patients Today</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Pending Lab Requests</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">New Messages</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
