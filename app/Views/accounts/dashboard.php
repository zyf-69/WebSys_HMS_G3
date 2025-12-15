<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Accounts Dashboard</div>
        <div class="page-subtitle">Financial overview: revenue, expenses, and profit summary.</div>
    </div>
</div>

<style>
.dashboard-section {
    margin-bottom: 32px;
}
.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e5e7eb;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.stat-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 20px;
    transition: all 0.2s;
}
.stat-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}
.stat-label {
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
}
.stat-period {
    font-size: 11px;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 6px;
}
.stat-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.2;
}
.stat-value.revenue {
    color: #16a34a;
}
.stat-value.expense {
    color: #dc2626;
}
.stat-value.profit {
    color: #16a34a;
}
.stat-value.profit.negative {
    color: #dc2626;
}
.stat-trend {
    font-size: 12px;
    color: #6b7280;
    margin-top: 8px;
}
.quick-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 32px;
}
.quick-nav-card {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    text-decoration: none;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
}
.quick-nav-card:hover {
    border-color: #16a34a;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
.quick-nav-icon {
    font-size: 28px;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0fdf4;
    border-radius: 10px;
}
.quick-nav-content {
    flex: 1;
}
.quick-nav-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}
.quick-nav-desc {
    font-size: 12px;
    color: #6b7280;
}
</style>

<!-- Quick Navigation -->
<div class="dashboard-section">
    <div class="section-title">Quick Actions</div>
    <div class="quick-nav-grid">
        <a href="/WebSys_HMS_G3/accounts/billing" class="quick-nav-card">
            <div class="quick-nav-icon">💲</div>
            <div class="quick-nav-content">
                <div class="quick-nav-title">Billing & Invoices</div>
                <div class="quick-nav-desc">Manage bills and invoices</div>
            </div>
        </a>
        <a href="/WebSys_HMS_G3/accounts/payments" class="quick-nav-card">
            <div class="quick-nav-icon">💳</div>
            <div class="quick-nav-content">
                <div class="quick-nav-title">Payments</div>
                <div class="quick-nav-desc">Receive payments & refunds</div>
            </div>
        </a>
        <a href="/WebSys_HMS_G3/accounts/expenses" class="quick-nav-card">
            <div class="quick-nav-icon">💰</div>
            <div class="quick-nav-content">
                <div class="quick-nav-title">Expenses</div>
                <div class="quick-nav-desc">Record and track expenses</div>
            </div>
        </a>
        <a href="/WebSys_HMS_G3/accounts/reports" class="quick-nav-card">
            <div class="quick-nav-icon">📊</div>
            <div class="quick-nav-content">
                <div class="quick-nav-title">Reports</div>
                <div class="quick-nav-desc">Generate financial reports</div>
            </div>
        </a>
    </div>
</div>

<!-- Today's Overview -->
<div class="dashboard-section">
    <div class="section-title">Today's Overview</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Revenue</div>
                <div class="stat-period">Today</div>
            </div>
            <div class="stat-value revenue">₱<?= number_format($todayRevenue ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Expenses</div>
                <div class="stat-period">Today</div>
            </div>
            <div class="stat-value expense">₱<?= number_format($todayExpenses ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Net Profit</div>
                <div class="stat-period">Today</div>
            </div>
            <div class="stat-value profit <?= ($todayProfit ?? 0) < 0 ? 'negative' : '' ?>">
                ₱<?= number_format($todayProfit ?? 0, 2) ?>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Invoices</div>
                <div class="stat-period">Today</div>
            </div>
            <div class="stat-value"><?= number_format($invoicesToday ?? 0) ?></div>
            <div class="stat-trend">New bills created</div>
        </div>
    </div>
</div>

<!-- Monthly Summary -->
<div class="dashboard-section">
    <div class="section-title">Monthly Summary (<?= date('F Y') ?>)</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Revenue</div>
                <div class="stat-period">This Month</div>
            </div>
            <div class="stat-value revenue">₱<?= number_format($monthRevenue ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Expenses</div>
                <div class="stat-period">This Month</div>
            </div>
            <div class="stat-value expense">₱<?= number_format($monthExpenses ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Net Profit</div>
                <div class="stat-period">This Month</div>
            </div>
            <div class="stat-value profit <?= ($monthProfit ?? 0) < 0 ? 'negative' : '' ?>">
                ₱<?= number_format($monthProfit ?? 0, 2) ?>
            </div>
        </div>
    </div>
</div>

<!-- Yearly Summary -->
<div class="dashboard-section">
    <div class="section-title">Yearly Summary (<?= date('Y') ?>)</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Revenue</div>
                <div class="stat-period">This Year</div>
            </div>
            <div class="stat-value revenue">₱<?= number_format($yearRevenue ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Expenses</div>
                <div class="stat-period">This Year</div>
            </div>
            <div class="stat-value expense">₱<?= number_format($yearExpenses ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Net Profit</div>
                <div class="stat-period">This Year</div>
            </div>
            <div class="stat-value profit <?= ($yearProfit ?? 0) < 0 ? 'negative' : '' ?>">
                ₱<?= number_format($yearProfit ?? 0, 2) ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="dashboard-section">
    <div class="section-title">Recent Transactions</div>
    <div class="card">
        <?php if (!empty($recentTransactions)): ?>
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
                    <?php foreach ($recentTransactions as $transaction): ?>
                        <tr>
                            <td><?= !empty($transaction['payment_date'] ?? $transaction['display_date'] ?? null) ? date('M d, Y', strtotime($transaction['payment_date'] ?? $transaction['display_date'])) : 'N/A' ?></td>
                            <td><?= esc($transaction['patient_name'] ?? 'N/A') ?></td>
                            <td><?= esc($transaction['bill_type'] ?? 'N/A') ?></td>
                            <td style="color: <?= floatval($transaction['amount'] ?? 0) >= 0 ? '#16a34a' : '#dc2626' ?>; font-weight: 600;">
                                <?= floatval($transaction['amount'] ?? 0) < 0 ? '-' : '' ?>₱<?= number_format(abs(floatval($transaction['amount'] ?? 0)), 2) ?>
                                <?= floatval($transaction['amount'] ?? 0) < 0 ? '<span style="font-size: 11px; margin-left: 4px;">(Refund)</span>' : '' ?>
                            </td>
                            <td><?= esc(ucfirst($transaction['payment_method'] ?? 'cash')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">💳</div>
                <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No recent transactions</div>
                <div style="font-size: 12px;">Transactions will appear here once payments are recorded.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
