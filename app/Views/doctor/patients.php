<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">My Patients</div>
        <div class="page-subtitle">View patient records with personal information and vitals for patients with appointments.</div>
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
    .search-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 20px;
    }
    .search-input {
        width: 100%;
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .patient-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 16px;
        transition: box-shadow 0.2s;
    }
    .patient-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .patient-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        cursor: pointer;
        user-select: none;
        padding: 12px;
        margin: -12px;
        border-radius: 8px;
        transition: background-color 0.2s;
    }
    .patient-header:hover {
        background-color: #f9fafb;
    }
    .patient-header-left {
        flex: 1;
    }
    .patient-name {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
    }
    .patient-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .patient-code {
        font-size: 13px;
        color: #6b7280;
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-inpatient {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-outpatient {
        background: #dcfce7;
        color: #166534;
    }
    .badge-appointment {
        background: #fef3c7;
        color: #92400e;
    }
    .patient-header-right {
        text-align: right;
        min-width: 150px;
    }
    .appointment-info {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 6px;
    }
    .patient-header-toggle {
        font-size: 14px;
        color: #6b7280;
        transition: transform 0.2s;
        margin-top: 4px;
    }
    .patient-header-toggle.expanded {
        transform: rotate(180deg);
    }
    .patient-details {
        display: none;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }
    .patient-details.expanded {
        display: block;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .info-section {
        background: #f9fafb;
        border-radius: 10px;
        padding: 16px;
        border-left: 4px solid #3b82f6;
    }
    .info-section-title {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .info-item {
        font-size: 13px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .info-item:last-child {
        margin-bottom: 0;
    }
    .info-label {
        color: #6b7280;
        font-weight: 500;
        min-width: 140px;
    }
    .info-value {
        color: #111827;
        font-weight: 500;
        text-align: right;
        flex: 1;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }
    .empty-state-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }
    .empty-state-text {
        font-size: 14px;
    }
</style>

<div class="search-container">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name, code, or email...">
    </div>
    
    <?php if (empty($patients)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">👤</div>
        <div class="empty-state-title">No patients found</div>
        <div class="empty-state-text">Patients with appointments will appear here.</div>
        </div>
    <?php else: ?>
        <div id="patientsList">
            <?php foreach ($patients as $patient): ?>
                <div class="patient-card" data-search="<?= strtolower(esc($patient['full_name'] . ' ' . ($patient['patient_code'] ?? '') . ' ' . ($patient['email'] ?? ''))) ?>">
                    <div class="patient-header" onclick="togglePatientDetails(this)">
                    <div class="patient-header-left">
                                <div class="patient-name"><?= esc($patient['full_name']) ?></div>
                        <div class="patient-meta">
                            <span class="patient-code">
                                <?php if (!empty($patient['patient_code'])): ?>
                                    ID: #<?= esc($patient['patient_code']) ?>
                                <?php else: ?>
                                    ID: #<?= esc($patient['id']) ?>
                                <?php endif; ?>
                            </span>
                            <span class="badge badge-<?= ($patient['patient_type'] ?? 'outpatient') === 'inpatient' ? 'inpatient' : 'outpatient' ?>">
                                    <?= esc($patient['visit_type'] ?? 'Outpatient') ?>
                            </span>
                            <?php if (!empty($patient['room_number']) || !empty($patient['bed_number'])): ?>
                                <span style="font-size: 12px; color: #6b7280;">
                                    Room: <?= esc($patient['room_number'] ?? 'N/A') ?> | Bed: <?= esc($patient['bed_number'] ?? 'N/A') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="patient-header-right">
                            <?php if (!empty($patient['appointment_date'])): ?>
                            <div class="appointment-info">
                                <div style="font-weight: 600; margin-bottom: 4px;">Last Appointment</div>
                                <div><?= date('M d, Y', strtotime($patient['appointment_date'])) ?></div>
                                    <?php if (!empty($patient['start_time'])): ?>
                                    <div style="margin-top: 2px;">
                                        <?= date('g:i A', strtotime($patient['start_time'])) ?>
                                            <?php if (!empty($patient['end_time'])): ?>
                                            - <?= date('g:i A', strtotime($patient['end_time'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <span class="badge badge-appointment" style="margin-top: 8px; display: inline-block;">
                                    <?= esc(ucfirst($patient['appointment_status'] ?? 'Pending')) ?>
                                </span>
                            <?php endif; ?>
                        <div class="patient-header-toggle">▼</div>
                        </div>
                    </div>

                    <div class="patient-details">
                        <div class="info-grid">
                        <!-- Personal Information -->
                        <div class="info-section">
                            <div class="info-section-title">Personal Information</div>
                            <div class="info-item">
                                <span class="info-label">Age:</span>
                                <span class="info-value"><?= esc($patient['age'] ?? 'N/A') ?> years</span>
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

<script>
    // Search functionality
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
            // Close all other expanded cards
            document.querySelectorAll('.patient-details.expanded').forEach(expanded => {
                expanded.classList.remove('expanded');
            });
            document.querySelectorAll('.patient-header-toggle.expanded').forEach(expanded => {
                expanded.classList.remove('expanded');
            });
            
            // Open this card
            details.classList.add('expanded');
            toggle.classList.add('expanded');
        }
    }
</script>

<?= $this->endSection() ?>
