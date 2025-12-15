<?php
// Shared navigation tabs for accountant views
$currentPath = uri_string();
$navItems = [
    ['url' => 'accounts/dashboard', 'icon' => '●', 'label' => 'Dashboard', 'id' => 'dashboard'],
    ['url' => 'accounts/billing', 'icon' => '💲', 'label' => 'Billing & Invoices', 'id' => 'billing'],
    ['url' => 'accounts/payments', 'icon' => '💳', 'label' => 'Payments', 'id' => 'payments'],
    ['url' => 'accounts/expenses', 'icon' => '💰', 'label' => 'Expenses', 'id' => 'expenses'],
    ['url' => 'accounts/reports', 'icon' => '📊', 'label' => 'Reports', 'id' => 'reports'],
];
?>
<style>
.accounts-nav-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.accounts-nav-tab {
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: -2px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}
.accounts-nav-tab:hover {
    color: #374151;
    background: #f9fafb;
}
.accounts-nav-tab.active {
    color: #16a34a;
    border-bottom-color: #16a34a;
    background: #f0fdf4;
    font-weight: 600;
}
</style>
<div class="accounts-nav-tabs">
    <?php foreach ($navItems as $item): ?>
        <?php
        $isActive = (strpos($currentPath, $item['id']) !== false) || 
                    ($item['id'] === 'dashboard' && $currentPath === 'accounts/dashboard');
        $url = base_url($item['url']);
        ?>
        <a href="<?= esc($url) ?>" class="accounts-nav-tab <?= $isActive ? 'active' : '' ?>">
            <span><?= $item['icon'] ?></span>
            <span><?= esc($item['label']) ?></span>
        </a>
    <?php endforeach; ?>
</div>

