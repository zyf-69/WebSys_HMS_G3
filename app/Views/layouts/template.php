<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'HMS System') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #16a34a; /* green for active dashboard */
            --primary-dark: #15803d;
            --bg-body: #f3f4f8;
            --sidebar-bg: #f9fafb;
            --sidebar-border: #e5e7eb;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --icon-muted: #4b5563;
            --border-soft: #e5e7eb;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }
        .layout-root {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--sidebar-border);
        }
        .sidebar-header {
            padding: 18px 18px 12px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: radial-gradient(circle at 0 0, #bbf7d0, #22c55e 60%, #16a34a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }
        .sidebar-title {
            font-size: 15px;
            font-weight: 600;
        }
        .sidebar-subtitle {
            font-size: 11px;
            color: var(--text-muted);
        }
        .sidebar-nav {
            margin-top: 10px;
            padding: 8px 10px 18px;
            flex: 1;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 10px;
            margin: 4px 0;
            font-size: 13px;
            color: var(--text-main);
            text-decoration: none;
            transition: background 0.12s ease, color 0.12s ease, transform 0.08s ease, box-shadow 0.12s ease;
        }
        .nav-link:hover {
            background: #e5f6ed;
            transform: translateX(1px);
        }
        .nav-link.primary {
            /* no special background; same style as normal link */
        }
        .nav-link.primary .nav-label {
            /* keep default text color */
        }
        .nav-icon {
            width: 18px;
            text-align: center;
            color: var(--icon-muted);
            font-size: 14px;
        }
        .nav-link.primary .nav-icon {
            color: #ffffff;
        }
        .nav-label {
            flex: 1;
        }
        .nav-dropdown {
            position: relative;
        }
        .nav-dropdown-toggle {
            cursor: pointer;
            user-select: none;
        }
        .nav-dropdown-toggle::after {
            content: '▼';
            font-size: 10px;
            margin-left: auto;
            transition: transform 0.2s ease;
        }
        .nav-dropdown-toggle.active::after {
            transform: rotate(180deg);
        }
        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            margin-left: 28px;
        }
        .nav-submenu.active {
            max-height: 500px;
        }
        .nav-submenu .nav-link {
            padding-left: 20px;
            font-size: 12px;
            margin: 2px 0;
        }
        .sidebar-footer {
            padding: 10px 14px 14px;
            border-top: 1px solid var(--sidebar-border);
            font-size: 11px;
            color: var(--text-muted);
        }
        .sidebar-footer strong {
            color: var(--text-main);
        }
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .topbar {
            height: 56px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        .topbar-left-title {
            font-size: 16px;
            font-weight: 600;
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1d4ed8, #38bdf8);
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }
        .user-name {
            font-weight: 500;
        }
        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }
        .logout-link {
            font-size: 11px;
            color: #ef4444;
            text-decoration: none;
            margin-left: 8px;
        }
        .content {
            padding: 18px 20px 22px;
            flex: 1;
            overflow: auto;
        }
        .page-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .page-title {
            font-size: 20px;
            font-weight: 600;
        }
        .page-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }
        .badge-role {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            border: 1px solid rgba(37, 99, 235, 0.25);
        }
        .grid {
            display: grid;
            gap: 14px;
        }
        .grid-4 {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            padding: 14px 14px 16px;
        }
        .card-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .card-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .card-value {
            font-size: 22px;
            font-weight: 600;
        }
        .card-trend {
            font-size: 11px;
            color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            padding: 8px 8px;
            border-bottom: 1px solid var(--border-soft);
            text-align: left;
        }
        th {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }
        tr:last-child td {
            border-bottom: none;
        }
        @media (max-width: 900px) {
            .sidebar { display: none; }
            body { display: block; }
            .main { width: 100%; }
        }
    </style>
</head>
<body>
<div class="layout-root">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">HS</div>
            <div>
                <div class="sidebar-title">HMS System</div>
                <div class="sidebar-subtitle">St. Peter Hospital</div>
            </div>
        </div>
        <div class="sidebar-nav">
            <?php
            $role = session()->get('role') ?? 'admin';
            $currentUrl = current_url();
            
            // Define role-specific navigation menus
            $menus = [
                'admin' => [
                    ['url' => 'admin/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => 'patients/records', 'icon' => '👤', 'label' => 'Patient Records'],
                    ['url' => 'admin/scheduling', 'icon' => '📅', 'label' => 'Doctor Scheduling'],
                    ['url' => '#', 'icon' => '💲', 'label' => 'Billing'],
                    ['url' => '#', 'icon' => '⚗', 'label' => 'Laboratory'],
                    ['url' => 'admin/pharmacy', 'icon' => '💊', 'label' => 'Pharmacy'],
                    ['url' => '#', 'icon' => '📊', 'label' => 'Hospital Reports'],
                    ['url' => 'admin/user-access', 'icon' => '🛡', 'label' => 'User Management'],
                ],
                'hospital_administrator' => [
                    ['url' => 'admin/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => 'patients/records', 'icon' => '👤', 'label' => 'Patient Records'],
                    ['url' => 'admin/scheduling', 'icon' => '📅', 'label' => 'Doctor Scheduling'],
                    ['url' => '#', 'icon' => '💲', 'label' => 'Billing'],
                    ['url' => '#', 'icon' => '⚗', 'label' => 'Laboratory'],
                    ['url' => '#', 'icon' => '💊', 'label' => 'Pharmacy'],
                    ['url' => '#', 'icon' => '📊', 'label' => 'Hospital Reports'],
                    ['url' => 'admin/user-access', 'icon' => '🛡', 'label' => 'User Management'],
                ],
                'receptionist' => [
                    ['url' => 'receptionist/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    [
                        'type' => 'dropdown',
                        'icon' => '👤',
                        'label' => 'Patient Registration & EHR',
                        'children' => [
                            ['url' => 'patients/register', 'label' => 'Patient Registration & EHR'],
                            ['url' => 'patients/records', 'label' => 'Patient Records'],
                        ]
                    ],
                    ['url' => 'admin/appointments', 'icon' => '🗓', 'label' => 'Patient Scheduling'],
                ],
                'doctor' => [
                    ['url' => 'doctor/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => 'doctor/patients', 'icon' => '👤', 'label' => 'Patients'],
                    ['url' => 'doctor/appointments', 'icon' => '🗓', 'label' => 'Appointments'],
                    ['url' => 'doctor/schedule', 'icon' => '📅', 'label' => 'My Schedule'],
                    ['url' => '#', 'icon' => '⚗', 'label' => 'Lab Results'],
                    ['url' => '#', 'icon' => '💊', 'label' => 'Prescriptions'],
                ],
                'nurse' => [
                    ['url' => 'nurse/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '👤', 'label' => 'Assigned Patients'],
                    ['url' => '#', 'icon' => '📋', 'label' => 'Vitals Monitoring'],
                    ['url' => '#', 'icon' => '💊', 'label' => 'Medications'],
                ],
                'lab_staff' => [
                    ['url' => 'lab/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '⚗', 'label' => 'Test Requests'],
                    ['url' => '#', 'icon' => '📊', 'label' => 'Test Results'],
                ],
                'laboratory_staff' => [
                    ['url' => 'lab/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '⚗', 'label' => 'Test Requests'],
                    ['url' => '#', 'icon' => '📊', 'label' => 'Test Results'],
                ],
                'pharmacist' => [
                    ['url' => 'pharmacy/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '💊', 'label' => 'Prescriptions'],
                    ['url' => '#', 'icon' => '📦', 'label' => 'Inventory'],
                ],
                'accountant' => [
                    ['url' => 'accounts/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '💲', 'label' => 'Billing'],
                    ['url' => '#', 'icon' => '📄', 'label' => 'Invoices'],
                    ['url' => '#', 'icon' => '🏥', 'label' => 'Insurance Claims'],
                ],
                'it_staff' => [
                    ['url' => 'it/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '🔧', 'label' => 'System Maintenance'],
                    ['url' => '#', 'icon' => '🛡', 'label' => 'Security'],
                    ['url' => '#', 'icon' => '💾', 'label' => 'Backups'],
                ],
                'it' => [
                    ['url' => 'it/dashboard', 'icon' => '●', 'label' => 'Dashboard'],
                    ['url' => '#', 'icon' => '🔧', 'label' => 'System Maintenance'],
                    ['url' => '#', 'icon' => '🛡', 'label' => 'Security'],
                    ['url' => '#', 'icon' => '💾', 'label' => 'Backups'],
                ],
            ];
            
            $menuItems = $menus[$role] ?? $menus['admin'];
            
            foreach ($menuItems as $item) {
                if (isset($item['type']) && $item['type'] === 'dropdown') {
                    // Check if any child is active
                    $hasActiveChild = false;
                    foreach ($item['children'] as $child) {
                        if (strpos($currentUrl, $child['url']) !== false) {
                            $hasActiveChild = true;
                            break;
                        }
                    }
                    
                    echo '<div class="nav-dropdown">';
                    echo '<div class="nav-link nav-dropdown-toggle' . ($hasActiveChild ? ' active' : '') . '" onclick="this.classList.toggle(\'active\'); this.nextElementSibling.classList.toggle(\'active\');">';
                    echo '<span class="nav-icon">' . esc($item['icon']) . '</span>';
                    echo '<span class="nav-label">' . esc($item['label']) . '</span>';
                    echo '</div>';
                    echo '<div class="nav-submenu' . ($hasActiveChild ? ' active' : '') . '">';
                    foreach ($item['children'] as $child) {
                        $childUrl = $child['url'] === '#' ? '#' : base_url($child['url']);
                        $isChildActive = ($child['url'] !== '#' && strpos($currentUrl, $child['url']) !== false) ? ' primary' : '';
                        echo '<a href="' . esc($childUrl) . '" class="nav-link' . $isChildActive . '">';
                        echo '<span class="nav-label">' . esc($child['label']) . '</span>';
                        echo '</a>';
                    }
                    echo '</div>';
                    echo '</div>';
                } else {
                    $url = $item['url'] === '#' ? '#' : base_url($item['url']);
                    $isActive = ($item['url'] !== '#' && strpos($currentUrl, $item['url']) !== false) ? ' primary' : '';
                    echo '<a href="' . esc($url) . '" class="nav-link' . $isActive . '">';
                    echo '<span class="nav-icon">' . esc($item['icon']) . '</span>';
                    echo '<span class="nav-label">' . esc($item['label']) . '</span>';
                    echo '</a>';
                }
            }
            ?>
        </div>
        <div class="sidebar-footer">
            <div><strong>HMS System</strong> · Hospital Management</div>
            <div>Administration Console · <?= date('Y') ?></div>
        </div>
    </aside>
    <main class="main">
        <header class="topbar">
            <div class="topbar-left-title">
                <?php
                $role = session()->get('role') ?? 'admin';
                $roleTitles = [
                    'admin' => 'Admin Panel',
                    'hospital_administrator' => 'Admin Panel',
                    'doctor' => 'Doctor Panel',
                    'nurse' => 'Nurse Panel',
                    'receptionist' => 'Receptionist Panel',
                    'lab_staff' => 'Laboratory Panel',
                    'laboratory_staff' => 'Laboratory Panel',
                    'pharmacist' => 'Pharmacy Panel',
                    'accountant' => 'Accounts Panel',
                    'it_staff' => 'IT Panel',
                    'it' => 'IT Panel',
                ];
                echo esc($roleTitles[$role] ?? 'HMS Panel');
                ?>
            </div>
            <div class="topbar-user">
                <?php
                $first = session()->get('first_name') ?? '';
                $last  = session()->get('last_name') ?? '';
                $name  = trim($first . ' ' . $last) ?: (session()->get('username') ?? 'Administrator');
                $role  = session()->get('role') ?? 'admin';
                $initials = strtoupper(substr($name, 0, 1));
                ?>
                <div class="user-avatar"><?= esc($initials) ?></div>
                <div>
                    <div class="user-name"><?= esc($name) ?></div>
                    <div class="user-role">Role: <span class="badge-role"><?= esc($role) ?></span>
                        <a class="logout-link" href="<?= base_url('logout') ?>">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        <section class="content">
            <?= $this->renderSection('content') ?>
        </section>
    </main>
</div>
</body>
</html>
