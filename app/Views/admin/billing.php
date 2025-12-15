<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Billing & Payments</div>
        <div class="page-subtitle">Manage patient bills, invoices, and payment records.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="card" style="background:#ffebe9;border-color:#fecaca;color:#b91c1c;padding:10px 14px;margin-bottom:14px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="card" style="background:#ecfdf3;border-color:#bbf7d0;color:#166534;padding:10px 14px;margin-bottom:14px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Bills</div>
        </div>
        <div class="card-value"><?= number_format($totalBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Paid Bills</div>
        </div>
        <div class="card-value" style="color:#16a34a;"><?= number_format($paidBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Bills</div>
        </div>
        <div class="card-value" style="color:#dc2626;"><?= number_format($pendingBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Amount</div>
        </div>
        <div class="card-value" style="color:#16a34a;">₱<?= number_format($totalAmount ?? 0, 2) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">All Bills</div>
    </div>
    <?php if (!empty($bills)): ?>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td>#<?= esc($bill['id']) ?></td>
                        <td><?= esc(trim(($bill['last_name'] ?? '') . ', ' . ($bill['first_name'] ?? '') . ' ' . ($bill['middle_name'] ?? ''))) ?></td>
                        <td><?= esc($bill['bill_type'] ?? 'N/A') ?></td>
                        <td>₱<?= number_format($bill['total_amount'] ?? 0, 2) ?></td>
                        <td>
                            <span style="display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:500;<?= ($bill['status'] ?? 'pending') === 'paid' ? 'background:#d1fae5;color:#065f46;' : 'background:#fef3c7;color:#92400e;' ?>">
                                <?= ucfirst($bill['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td><?= $bill['created_at'] ? date('M d, Y', strtotime($bill['created_at'])) : 'N/A' ?></td>
                        <td>
                            <?php if (($bill['status'] ?? 'pending') === 'pending'): ?>
                                <form action="/WebSys_HMS_G3/admin/billing/payment" method="post" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="bill_id" value="<?= esc($bill['id']) ?>">
                                    <input type="hidden" name="payment_amount" value="<?= esc($bill['total_amount']) ?>">
                                    <button type="submit" style="padding:4px 12px;border-radius:6px;background:#16a34a;color:#ffffff;font-size:12px;font-weight:500;border:none;cursor:pointer;">Mark Paid</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center;padding:40px 20px;color:#6b7280;">
            <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">💲</div>
            <div style="font-size:14px;font-weight:500;margin-bottom:4px;">No bills found</div>
            <div style="font-size:12px;">Create a bill to get started.</div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

