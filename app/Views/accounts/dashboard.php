<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Accounts Dashboard</div>
        <div class="page-subtitle">Billing, payments, and insurance claims summary.</div>
    </div>
</div>
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card"><div class="card-header"><div class="card-title">Invoices Today</div></div><div class="card-value">--</div></div>
    <div class="card"><div class="card-header"><div class="card-title">Unpaid Bills</div></div><div class="card-value">--</div></div>
</div>
<?= $this->endSection() ?>
