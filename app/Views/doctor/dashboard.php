<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Doctor Dashboard</div>
        <div class="page-subtitle">Today's schedule, patient list, and pending tasks.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fecaca;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #bbf7d0;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Patients Today</div>
        </div>
        <div class="card-value"><?= esc($patientsToday ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Lab Requests</div>
        </div>
        <div class="card-value"><?= esc($pendingLabRequests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">New Messages</div>
        </div>
        <div class="card-value"><?= esc($newMessages ?? 0) ?></div>
    </div>
</div>
<?= $this->endSection() ?>
