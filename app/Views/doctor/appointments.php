<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">My Appointments</div>
        <div class="page-subtitle">View and manage all patient appointments scheduled with you.</div>
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
    .btn-complete, .btn-followup {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-complete {
        background: #10b981;
        color: #ffffff;
    }
    .btn-complete:hover {
        background: #059669;
    }
    .btn-followup {
        background: #3b82f6;
        color: #ffffff;
    }
    .btn-followup:hover {
        background: #2563eb;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }
    .modal-form-group {
        margin-bottom: 16px;
    }
    .modal-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #374151;
    }
    .modal-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
    .modal-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        min-height: 80px;
        resize: vertical;
    }
    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        justify-content: flex-end;
    }
    .btn-modal-cancel {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-cancel:hover {
        background: #f9fafb;
    }
    .btn-modal-submit {
        background: #3b82f6;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-submit:hover {
        background: #2563eb;
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
                        <th>Actions</th>
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
                            <td>
                                <?php
                                $status = strtolower($appointment['status'] ?? 'pending');
                                if ($status !== 'completed'):
                                ?>
                                    <form method="post" action="/WebSys_HMS_G3/doctor/appointments/complete/<?= esc($appointment['id']) ?>" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-complete" onclick="return confirm('Mark this appointment as completed?')">
                                            Mark Complete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn-followup" onclick="openFollowUpModal(<?= esc($appointment['id']) ?>, <?= esc($appointment['patient_id']) ?>, '<?= esc($appointment['patient_name']) ?>')">
                                        Schedule Follow-up
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="records-card" style="margin-top: 24px;">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Follow-up Checkups</h3>
    <div class="search-filter">
        <input type="text" class="search-input" id="searchFollowUpInput" placeholder="Search follow-ups by patient name, code, or reason...">
    </div>
    
    <?php if (empty($followUps ?? [])): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔄</div>
            <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No follow-up checkups found</div>
            <div style="font-size: 12px;">Follow-up appointments will appear here.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Follow-up Date</th>
                        <th>Follow-up Time</th>
                        <th>Patient</th>
                        <th>Patient Code</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Reason</th>
                        <th>Original Appointment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="followUpsTableBody">
                    <?php foreach ($followUps ?? [] as $followUp): ?>
                        <tr>
                            <td>
                                <?php if (!empty($followUp['follow_up_date'])): ?>
                                    <?= date('M d, Y', strtotime($followUp['follow_up_date'])) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($followUp['follow_up_time'])): ?>
                                    <?= date('h:i A', strtotime($followUp['follow_up_time'])) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($followUp['patient_name'] ?? 'N/A') ?></strong>
                            </td>
                            <td>
                                <?php if (!empty($followUp['patient_code'])): ?>
                                    <strong><?= esc($followUp['patient_code']) ?></strong>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">#<?= esc($followUp['patient_id'] ?? 'N/A') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $followUp['patient_age'] ? $followUp['patient_age'] . ' years' : 'N/A' ?>
                            </td>
                            <td>
                                <?php if (!empty($followUp['gender'])): ?>
                                    <span class="badge" style="background: <?= $followUp['gender'] === 'male' ? '#dbeafe' : '#fce7f3' ?>; color: <?= $followUp['gender'] === 'male' ? '#1e40af' : '#9f1239' ?>;">
                                        <?= esc(ucfirst($followUp['gender'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($followUp['reason'])): ?>
                                    <?= esc($followUp['reason']) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($followUp['original_appointment_date'])): ?>
                                    <?= date('M d, Y', strtotime($followUp['original_appointment_date'])) ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status = strtolower($followUp['status'] ?? 'scheduled');
                                $badgeClass = 'badge-' . $status;
                                if ($status === 'no_show') {
                                    $badgeClass = 'badge-cancelled';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= esc(ucfirst(str_replace('_', ' ', $followUp['status'] ?? 'Scheduled'))) ?>
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
    // Simple search functionality for appointments
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#appointmentsTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Simple search functionality for follow-ups
    document.getElementById('searchFollowUpInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#followUpsTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Follow-up modal functionality
    function openFollowUpModal(appointmentId, patientId, patientName) {
        document.getElementById('followUpAppointmentId').value = appointmentId;
        document.getElementById('followUpPatientId').value = patientId;
        document.getElementById('followUpPatientName').textContent = patientName;
        document.getElementById('followUpModal').classList.add('active');
    }

    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.remove('active');
        document.getElementById('followUpForm').reset();
    }

    // Close modal when clicking overlay
    document.getElementById('followUpModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFollowUpModal();
        }
    });
</script>

<!-- Follow-up Modal -->
<div id="followUpModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Schedule Follow-up Checkup</h3>
            <button type="button" class="modal-close" onclick="closeFollowUpModal()">×</button>
        </div>
        <form id="followUpForm" action="/WebSys_HMS_G3/doctor/appointments/create-follow-up" method="post">
            <?= csrf_field() ?>
            <input type="hidden" id="followUpAppointmentId" name="appointment_id" value="">
            <input type="hidden" id="followUpPatientId" name="patient_id" value="">
            
            <div class="modal-form-group">
                <label class="modal-label">Patient</label>
                <div class="modal-input" style="background: #f3f4f6; cursor: not-allowed;" id="followUpPatientName"></div>
            </div>
            
            <div class="modal-form-group">
                <label class="modal-label">Follow-up Date *</label>
                <input type="date" name="follow_up_date" class="modal-input" required>
            </div>
            
            <div class="modal-form-group">
                <label class="modal-label">Follow-up Time</label>
                <input type="time" name="follow_up_time" class="modal-input">
            </div>
            
            <div class="modal-form-group">
                <label class="modal-label">Reason</label>
                <textarea name="reason" class="modal-textarea" placeholder="Reason for follow-up checkup"></textarea>
            </div>
            
            <div class="modal-form-group">
                <label class="modal-label">Notes</label>
                <textarea name="notes" class="modal-textarea" placeholder="Additional notes"></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeFollowUpModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Schedule Follow-up</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

