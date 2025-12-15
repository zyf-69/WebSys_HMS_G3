<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Walk-in Management</div>
        <div class="page-subtitle">Manage walk-in patients (outpatients only).</div>
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
    .badge-male {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-female {
        background: #fce7f3;
        color: #9f1239;
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
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 24px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #374151;
    }
    .form-input,
    .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    .btn-primary {
        background: #3b82f6;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #2563eb;
    }
    .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #f9fafb;
    }
</style>

<div class="form-card">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Create Walk-in Request</h3>
    <form action="/WebSys_HMS_G3/receptionist/walk-in" method="post">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" name="middle_name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Test Type *</label>
                <select name="test_type" class="form-select" required>
                    <option value="">Select test type</option>
                    <option value="Blood Test">Blood Test</option>
                    <option value="X-Ray">X-Ray</option>
                    <option value="Urine Test">Urine Test</option>
                    <option value="ECG">ECG (Electrocardiogram)</option>
                    <option value="Ultrasound">Ultrasound</option>
                    <option value="CT Scan">CT Scan</option>
                    <option value="MRI">MRI</option>
                    <option value="Stool Test">Stool Test</option>
                    <option value="Sputum Test">Sputum Test</option>
                    <option value="Biopsy">Biopsy</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Assign Doctor</label>
                <select name="doctor_id" class="form-select">
                    <option value="">Select doctor (optional)</option>
                    <?php foreach ($doctors ?? [] as $doctor): ?>
                        <option value="<?= esc($doctor['id']) ?>">
                            <?= esc($doctor['full_name']) ?>
                            <?php if (!empty($doctor['specialization'])): ?>
                                - <?= esc($doctor['specialization']) ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="reset" class="btn-secondary">Clear</button>
            <button type="submit" class="btn-primary">Create Walk-in Request</button>
        </div>
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Walk-ins</div>
        <div class="stat-value"><?= esc($totalWalkIns ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Today's Walk-ins</div>
        <div class="stat-value"><?= esc($todayWalkIns ?? 0) ?></div>
    </div>
</div>

<div class="records-card">
    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name, code, or test type...">
    </div>
    
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Patient Code</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Test Type</th>
                <th>Assigned Doctor</th>
            </tr>
        </thead>
        <tbody id="walkInTableBody">
            <?php if (empty($outpatients)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon">🚶</div>
                        <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No walk-in patients found</div>
                        <div style="font-size: 12px;">Walk-in patients (outpatients) will appear here.</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($outpatients as $patient): ?>
                    <tr data-search="<?= strtolower(esc($patient['full_name'] . ' ' . ($patient['patient_code'] ?? '') . ' ' . ($patient['test_type'] ?? ''))) ?>">
                        <td><strong><?= esc($patient['full_name']) ?></strong></td>
                        <td>
                            <?php if (!empty($patient['patient_code'])): ?>
                                <strong><?= esc($patient['patient_code']) ?></strong>
                            <?php else: ?>
                                <span style="color: #9ca3af;">#<?= esc($patient['id']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $patient['age'] ? $patient['age'] . ' years' : 'N/A' ?>
                        </td>
                        <td>
                            <?php if (!empty($patient['gender'])): ?>
                                <span class="badge badge-<?= esc($patient['gender']) ?>">
                                    <?= esc(ucfirst($patient['gender'])) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #9ca3af;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($patient['test_type'])): ?>
                                <strong><?= esc($patient['test_type']) ?></strong>
                            <?php else: ?>
                                <span style="color: #9ca3af;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($patient['doctor_name'])): ?>
                                <?= esc($patient['doctor_name']) ?>
                                <?php if (!empty($patient['specialization'])): ?>
                                    <div style="font-size: 12px; color: #6b7280;"><?= esc($patient['specialization']) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #9ca3af;">Not assigned</span>
                            <?php endif; ?>
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
        const rows = document.querySelectorAll('#walkInTableBody tr');
        
        rows.forEach(row => {
            const searchText = row.getAttribute('data-search') || row.textContent.toLowerCase();
            row.style.display = searchText.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>

