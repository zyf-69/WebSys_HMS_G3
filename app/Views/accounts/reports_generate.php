<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Financial Report</div>
        <div class="page-subtitle">
            <?php
            $rd = $reportData ?? [];
            $reportType = $rd['reportType'] ?? 'daily';
            $startDate = $rd['startDate'] ?? date('Y-m-d');
            $endDate = $rd['endDate'] ?? date('Y-m-d');
            
            if ($reportType === 'daily') {
                echo date('F d, Y', strtotime($startDate));
            } elseif ($reportType === 'monthly') {
                echo date('F Y', strtotime($startDate));
            } elseif ($reportType === 'yearly') {
                echo date('Y', strtotime($startDate));
            } else {
                echo date('M d, Y', strtotime($startDate)) . ' - ' . date('M d, Y', strtotime($endDate));
            }
            ?>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/WebSys_HMS_G3/accounts/reports" style="padding: 10px 20px; background: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;">
            ← Back to Reports
        </a>
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Print Report
        </button>
    </div>
</div>

<?php
$rd = $reportData ?? [];
$totalRevenue = $rd['totalRevenue'] ?? 0;
$totalExpenses = $rd['totalExpenses'] ?? 0;
$vatAmount = $rd['vatAmount'] ?? 0;
$serviceCharge = $rd['serviceCharge'] ?? 0;
$netProfit = $rd['netProfit'] ?? 0;
?>

<div class="grid grid-5" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Revenue</div>
        </div>
        <div class="card-value" style="color: #16a34a; font-size: 20px;">₱<?= number_format($totalRevenue, 2) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Expenses</div>
        </div>
        <div class="card-value" style="color: #dc2626; font-size: 20px;">₱<?= number_format($totalExpenses, 2) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">VAT (12%)</div>
        </div>
        <div class="card-value" style="color: #dc2626; font-size: 20px;">₱<?= number_format($vatAmount, 2) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Service Charge (10%)</div>
        </div>
        <div class="card-value" style="color: #dc2626; font-size: 20px;">₱<?= number_format($serviceCharge, 2) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Net Profit</div>
        </div>
        <div class="card-value" style="color: <?= $netProfit >= 0 ? '#16a34a' : '#dc2626' ?>; font-size: 20px;">
            ₱<?= number_format($netProfit, 2) ?>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Tax & Compliance</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>VAT (Value Added Tax)</td>
                <td>12%</td>
                <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($vatAmount, 2) ?></td>
            </tr>
            <tr>
                <td>Service Charge</td>
                <td>10%</td>
                <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($serviceCharge, 2) ?></td>
            </tr>
            <tr>
                <td><strong>Total Tax & Charges</strong></td>
                <td></td>
                <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($vatAmount + $serviceCharge, 2) ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (!empty($rd['payments'])): ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Revenue Transactions</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Patient</th>
                <th>Bill Type</th>
                <th>Amount</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rd['payments'] as $payment): ?>
                <?php if (floatval($payment['amount'] ?? 0) > 0): ?>
                    <tr>
                        <td><?= !empty($payment['payment_date'] ?? $payment['display_date'] ?? null) ? date('M d, Y', strtotime($payment['payment_date'] ?? $payment['display_date'])) : 'N/A' ?></td>
                        <td><?= esc($payment['patient_name'] ?? 'N/A') ?></td>
                        <td><?= esc($payment['bill_type'] ?? 'N/A') ?></td>
                        <td style="color: #16a34a; font-weight: 600;">₱<?= number_format(floatval($payment['amount'] ?? 0), 2) ?></td>
                        <td><?= esc(ucfirst($payment['payment_method'] ?? 'cash')) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($rd['expenses'])): ?>
<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Expense Transactions</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rd['expenses'] as $expense): ?>
                <tr>
                    <td><?= $expense['expense_date'] ? date('M d, Y', strtotime($expense['expense_date'])) : 'N/A' ?></td>
                    <td><?= esc($expense['expense_type'] ?? 'N/A') ?></td>
                    <td><?= esc(ucfirst(str_replace('_', ' ', $expense['category'] ?? 'N/A'))) ?></td>
                    <td><?= esc($expense['description'] ?? '-') ?></td>
                    <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($expense['amount'] ?? 0, 2) ?></td>
                    <td><?= esc(ucfirst($expense['payment_method'] ?? 'cash')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<style>
@media print {
    .page-header button,
    .sidebar,
    .topbar {
        display: none !important;
    }
    .content {
        margin: 0 !important;
        padding: 20px !important;
    }
}
</style>

<?= $this->endSection() ?>

