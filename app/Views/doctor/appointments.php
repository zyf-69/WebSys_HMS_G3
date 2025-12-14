<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">My Appointments</div>
        <div class="page-subtitle">View and manage all patient appointments scheduled with you.</div>
    </div>
</div>

<style>
    .records-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
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
        font-weight: 500;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-confirmed {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-completed {
        background: #dcfce7;
        color: #166534;
    }
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
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
    .search-filter {
        margin-bottom: 16px;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .search-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
</style>

<div class="records-card">
    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name, code, or reason...">
    </div>
    
    <?php if (empty($appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📅</div>
            <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No appointments found</div>
            <div style="font-size: 12px;">Appointments scheduled with you will appear here.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Appointment Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Patient Code</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Schedule Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTableBody">
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td>
                                <?php if (!empty($appointment['appointment_date'])): ?>
                                    <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($appointment['start_time'])): ?>
                                    <?= date('h:i A', strtotime($appointment['start_time'])) ?>
                                    <?php if (!empty($appointment['end_time'])): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                            to <?= date('h:i A', strtotime($appointment['end_time'])) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($appointment['patient_name']) ?></strong>
                            </td>
                            <td>
                                <?php if (!empty($appointment['patient_code'])): ?>
                                    <strong><?= esc($appointment['patient_code']) ?></strong>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">#<?= esc($appointment['patient_id']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $appointment['patient_age'] ? $appointment['patient_age'] . ' years' : 'N/A' ?>
                            </td>
                            <td>
                                <?php if (!empty($appointment['gender'])): ?>
                                    <span class="badge" style="background: <?= $appointment['gender'] === 'male' ? '#dbeafe' : '#fce7f3' ?>; color: <?= $appointment['gender'] === 'male' ? '#1e40af' : '#9f1239' ?>;">
                                        <?= esc(ucfirst($appointment['gender'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($appointment['schedule_type'])): ?>
                                    <?= esc($appointment['schedule_type']) ?>
                                <?php elseif (!empty($appointment['shift_name'])): ?>
                                    <?= esc($appointment['shift_name']) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status = strtolower($appointment['status'] ?? 'pending');
                                $badgeClass = 'badge-' . $status;
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= esc(ucfirst($appointment['status'] ?? 'Pending')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    // Simple search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#appointmentsTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>

