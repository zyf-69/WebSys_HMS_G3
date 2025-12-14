<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">My Patients</div>
        <div class="page-subtitle">View patient records with personal information and vitals for patients with appointments.</div>
    </div>
</div>

<style>
    .records-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 16px;
    }
    .patient-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 14px;
    }
    .patient-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
        padding: 8px 0;
        transition: background-color 0.2s;
    }
    .patient-header:hover {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 8px 12px;
        margin: -8px -12px;
    }
    .patient-header-toggle {
        font-size: 16px;
        color: #6b7280;
        transition: transform 0.2s;
        margin-left: 12px;
    }
    .patient-header-toggle.expanded {
        transform: rotate(180deg);
    }
    .patient-details {
        display: none;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #e5e7eb;
    }
    .patient-details.expanded {
        display: block;
    }
    .patient-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .patient-code {
        font-size: 12px;
        color: #6b7280;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }
    .info-section {
        background: #f9fafb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
    }
    .info-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-item {
        font-size: 13px;
        margin-bottom: 6px;
    }
    .info-label {
        color: #6b7280;
        font-weight: 500;
    }
    .info-value {
        color: #111827;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-active {
        background: #dcfce7;
        color: #166534;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-completed {
        background: #dbeafe;
        color: #1e40af;
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
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
</style>

<div class="records-card">
    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name, code, or email...">
    </div>
    
    <?php if (empty($patients)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">👤</div>
            <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No patients found</div>
            <div style="font-size: 12px;">Patients with appointments will appear here.</div>
        </div>
    <?php else: ?>
        <div id="patientsList">
            <?php foreach ($patients as $patient): ?>
                <div class="patient-card" data-search="<?= strtolower(esc($patient['full_name'] . ' ' . ($patient['patient_code'] ?? '') . ' ' . ($patient['email'] ?? ''))) ?>">
                    <div class="patient-header" onclick="togglePatientDetails(this)">
                        <div style="flex: 1; display: flex; align-items: center;">
                            <div>
                                <div class="patient-name"><?= esc($patient['full_name']) ?></div>
                            <div class="patient-code">
                                <?php if (!empty($patient['patient_code'])): ?>
                                    Patient Code: <?= esc($patient['patient_code']) ?>
                                <?php else: ?>
                                    ID: #<?= esc($patient['id']) ?>
                                <?php endif; ?>
                                <span style="margin-left: 8px; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 500; background: <?= ($patient['patient_type'] ?? 'outpatient') === 'inpatient' ? '#dbeafe' : '#dcfce7' ?>; color: <?= ($patient['patient_type'] ?? 'outpatient') === 'inpatient' ? '#1e40af' : '#166534' ?>;">
                                    <?= esc($patient['visit_type'] ?? 'Outpatient') ?>
                                </span>
                            </div>
                            </div>
                            <div class="patient-header-toggle">▼</div>
                        </div>
                        <div style="text-align: right;">
                            <?php if (!empty($patient['appointment_date'])): ?>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                                    Last Appointment: <?= date('M d, Y', strtotime($patient['appointment_date'])) ?>
                                    <?php if (!empty($patient['start_time'])): ?>
                                        <div style="font-size: 11px; margin-top: 2px;">
                                            <?= date('h:i A', strtotime($patient['start_time'])) ?>
                                            <?php if (!empty($patient['end_time'])): ?>
                                                - <?= date('h:i A', strtotime($patient['end_time'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="badge badge-<?= strtolower($patient['appointment_status'] ?? 'pending') ?>">
                                    <?= esc(ucfirst($patient['appointment_status'] ?? 'Pending')) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="patient-details">
                        <div class="info-grid">
                        <!-- Personal Information -->
                        <div class="info-section">
                            <div class="info-section-title">Personal Information</div>
                            <div class="info-item">
                                <span class="info-label">Age:</span>
                                <span class="info-value"><?= $patient['age'] ?? 'N/A' ?> years</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender:</span>
                                <span class="info-value"><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Birth:</span>
                                <span class="info-value">
                                    <?= $patient['date_of_birth'] ? date('M d, Y', strtotime($patient['date_of_birth'])) : 'N/A' ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Civil Status:</span>
                                <span class="info-value"><?= esc(ucfirst($patient['civil_status'] ?? 'N/A')) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Blood Type:</span>
                                <span class="info-value"><?= esc($patient['blood_type'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Place of Birth:</span>
                                <span class="info-value"><?= esc($patient['place_of_birth'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="info-section">
                            <div class="info-section-title">Contact Information</div>
                            <div class="info-item">
                                <span class="info-label">Mobile:</span>
                                <span class="info-value"><?= esc($patient['mobile_number'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?= esc($patient['email'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Province:</span>
                                <span class="info-value"><?= esc($patient['province'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">City/Municipality:</span>
                                <span class="info-value"><?= esc($patient['city_municipality'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Barangay:</span>
                                <span class="info-value"><?= esc($patient['barangay'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <!-- Vitals -->
                        <div class="info-section">
                            <div class="info-section-title">Vitals</div>
                            <div class="info-item">
                                <span class="info-label">Blood Pressure:</span>
                                <span class="info-value"><?= esc($patient['blood_pressure'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Heart Rate:</span>
                                <span class="info-value"><?= esc($patient['heart_rate'] ?? 'N/A') ?> bpm</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Temperature:</span>
                                <span class="info-value"><?= esc($patient['temperature'] ?? 'N/A') ?> °C</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Height:</span>
                                <span class="info-value"><?= esc($patient['height_cm'] ?? 'N/A') ?> cm</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Weight:</span>
                                <span class="info-value"><?= esc($patient['weight_kg'] ?? 'N/A') ?> kg</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">BMI:</span>
                                <span class="info-value"><?= esc($patient['bmi'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <!-- Emergency Contact & Insurance -->
                        <div class="info-section">
                            <div class="info-section-title">Emergency & Insurance</div>
                            <div class="info-item">
                                <span class="info-label">Emergency Contact:</span>
                                <span class="info-value"><?= esc($patient['emergency_contact_person'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Relationship:</span>
                                <span class="info-value"><?= esc($patient['emergency_relationship'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contact Number:</span>
                                <span class="info-value"><?= esc($patient['emergency_contact_number'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Insurance Provider:</span>
                                <span class="info-value"><?= esc($patient['insurance_provider'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Policy Number:</span>
                                <span class="info-value"><?= esc($patient['policy_number'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Simple search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.patient-card');
        
        cards.forEach(card => {
            const searchText = card.getAttribute('data-search') || '';
            card.style.display = searchText.includes(searchTerm) ? '' : 'none';
        });
    });

    // Toggle patient details expand/collapse
    function togglePatientDetails(header) {
        const card = header.closest('.patient-card');
        const details = card.querySelector('.patient-details');
        const toggle = header.querySelector('.patient-header-toggle');
        
        if (details.classList.contains('expanded')) {
            details.classList.remove('expanded');
            toggle.classList.remove('expanded');
        } else {
            details.classList.add('expanded');
            toggle.classList.add('expanded');
        }
    }
</script>

<?= $this->endSection() ?>

