<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Nurse Dashboard</div>
        <div class="page-subtitle">Ward assignments, vitals monitoring, and shift overview.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Assigned Patients</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Pending Medications</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
