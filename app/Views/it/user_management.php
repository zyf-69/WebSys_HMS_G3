<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">User Management</div>
        <div class="page-subtitle">View and manage system users and their access.</div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        text-align: center;
    }
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        margin: 8px 0;
    }
    .stat-label {
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .records-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        overflow-x: auto;
    }
    .records-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .records-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .records-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .records-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
    }
    .records-table tbody tr:hover {
        background: #f9fafb;
    }
    .records-table tbody tr:last-child td {
        border-bottom: none;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-active {
        background: #dcfce7;
        color: #166534;
    }
    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-role {
        background: #dbeafe;
        color: #1e40af;
    }
    .search-filter {
        margin-bottom: 16px;
    }
    .search-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?= esc($totalUsers ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Users</div>
        <div class="stat-value"><?= esc($activeUsers ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Inactive Users</div>
        <div class="stat-value"><?= esc($inactiveUsers ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">New Users Today</div>
        <div class="stat-value"><?= esc($recentUsers ?? 0) ?></div>
    </div>
</div>

<div class="records-card">
    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by name, email, username, or role...">
    </div>
    
    <table class="records-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No users found</div>
                        <div style="font-size: 12px;">Users will appear here.</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr data-search="<?= strtolower(esc(trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) . ' ' . ($user['email'] ?? '') . ' ' . ($user['username'] ?? '') . ' ' . ($user['role_display_name'] ?? ''))) ?>">
                        <td>#<?= esc($user['id']) ?></td>
                        <td>
                            <strong><?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></strong>
                            <?php if (!empty($user['address'])): ?>
                                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;"><?= esc($user['address']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($user['username'] ?? '—') ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td>
                            <span class="badge badge-role">
                                <?= esc($user['role_display_name'] ?? ucfirst($user['role_name'] ?? 'N/A')) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $status = strtolower($user['status'] ?? 'active');
                            $badgeClass = 'badge-' . $status;
                            ?>
                            <span class="badge <?= esc($badgeClass) ?>">
                                <?= esc(ucfirst($user['status'] ?? 'Active')) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($user['created_at'])): ?>
                                <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                <div style="font-size: 11px; color: #6b7280;">
                                    <?= date('h:i A', strtotime($user['created_at'])) ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #9ca3af;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/WebSys_HMS_G3/it/user-management/edit/<?= esc($user['id']) ?>" style="padding: 6px 12px; background: #3b82f6; color: #ffffff; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;">
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTableBody tr');
        
        rows.forEach(row => {
            const searchText = row.getAttribute('data-search') || row.textContent.toLowerCase();
            row.style.display = searchText.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>

