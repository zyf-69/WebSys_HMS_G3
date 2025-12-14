<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Patient Records</div>
        <div class="page-subtitle">View and manage all registered patient records from receptionist registrations.</div>
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
    .badge-visit-type {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-inpatient {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-outpatient {
        background: #dcfce7;
        color: #166534;
    }
    .badge-gender {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }
    .badge-male {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-female {
        background: #fce7f3;
        color: #9f1239;
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
        <input type="text" class="search-input" id="searchInput" placeholder="Search by name, patient code, or contact number...">
    </div>
    
    <?php if (empty($patients)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No patient records found</div>
            <div style="font-size: 12px;">Patient records will appear here once they are registered.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Patient Code</th>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Visit Type</th>
                        <th>Contact</th>
                        <th>Emergency Contact</th>
                        <th>Registered Date</th>
                        <?php if (!empty($isAdmin)): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="patientsTableBody">
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td>
                                <?php if (!empty($patient['patient_code'])): ?>
                                    <strong><?= esc($patient['patient_code']) ?></strong>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">P<?= str_pad($patient['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($patient['full_name']) ?></strong>
                                <?php if (!empty($patient['date_of_birth'])): ?>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                        DOB: <?= date('M d, Y', strtotime($patient['date_of_birth'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($patient['age'])): ?>
                                    <?= $patient['age'] ?> years
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($patient['gender'])): ?>
                                    <span class="badge-gender badge-<?= esc($patient['gender']) ?>">
                                        <?= esc($patient['gender']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-visit-type badge-<?= strtolower($patient['visit_type']) ?>">
                                    <?= esc($patient['visit_type']) ?>
                                </span>
                                <?php if ($patient['visit_type'] === 'Inpatient' && !empty($patient['room_number'])): ?>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                        Room <?= esc($patient['room_number']) ?>
                                        <?php if (!empty($patient['bed_number'])): ?>
                                            - Bed <?= esc($patient['bed_number']) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($patient['mobile_number'])): ?>
                                    <div><?= esc($patient['mobile_number']) ?></div>
                                <?php elseif (!empty($patient['phone_number'])): ?>
                                    <div><?= esc($patient['phone_number']) ?></div>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                                <?php if (!empty($patient['email'])): ?>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                        <?= esc($patient['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($patient['emergency_contact'])): ?>
                                    <div><?= esc($patient['emergency_contact']) ?></div>
                                    <?php if (!empty($patient['relationship'])): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                            <?= esc($patient['relationship']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($patient['emergency_contact_number'])): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                            <?= esc($patient['emergency_contact_number']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                // Get registered date - use registered_date (aliased from created_at) or fallback to created_at
                                $registeredDate = $patient['registered_date'] ?? null;
                                
                                if (!empty($registeredDate)): 
                                    // Format the date and time
                                    $dateTime = strtotime($registeredDate);
                                    if ($dateTime !== false):
                                ?>
                                    <?= date('M d, Y', $dateTime) ?>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                        <?= date('h:i A', $dateTime) ?>
                                    </div>
                                <?php 
                                    else: 
                                ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php 
                                    endif;
                                else: 
                                ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <?php if (!empty($isAdmin)): ?>
                                <td>
                                    <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" style="padding: 4px 10px; background: #16a34a; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 500; display: inline-block;">
                                        Edit
                                    </a>
                                </td>
                            <?php endif; ?>
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
        const rows = document.querySelectorAll('#patientsTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>

