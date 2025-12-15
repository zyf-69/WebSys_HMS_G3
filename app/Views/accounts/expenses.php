<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Patient Expenses</div>
        <div class="page-subtitle">View patient expenses based on bills, prescriptions, lab tests, and services received.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="card" style="background: #fee2e2; border-color: #fecaca; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="card" style="background: #dcfce7; border-color: #bbf7d0; color: #166534; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<!-- Summary Statistics -->
<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Expenses</div>
        </div>
        <div class="card-value" style="color: #dc2626;">₱<?= number_format($totalExpenses ?? 0, 2) ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">All patient expenses</div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">This Month</div>
        </div>
        <div class="card-value" style="color: #dc2626;">₱<?= number_format($thisMonthTotal ?? 0, 2) ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;"><?= date('F Y') ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Records</div>
        </div>
        <div class="card-value"><?= number_format(count($patientExpenses ?? [])) ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Expense entries</div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Patients</div>
        </div>
        <div class="card-value"><?= number_format(count($byPatient ?? [])) ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">With expenses</div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Filters</div>
    </div>
    <form method="get" style="padding: 16px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 500; margin-bottom: 6px;">Patient</label>
                <select name="patient_id" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
                    <option value="">All Patients</option>
                    <?php foreach ($patients ?? [] as $patient): ?>
                        <option value="<?= esc($patient['id']) ?>" <?= ($filterPatient ?? '') == $patient['id'] ? 'selected' : '' ?>>
                            <?= esc($patient['patient_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 500; margin-bottom: 6px;">Bill Type</label>
                <select name="type" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
                    <option value="">All Types</option>
                    <?php foreach ($billTypes ?? [] as $type): ?>
                        <option value="<?= esc($type) ?>" <?= ($filterType ?? '') === $type ? 'selected' : '' ?>>
                            <?= esc($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 500; margin-bottom: 6px;">Month</label>
                <select name="month" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
                    <option value="">All Months</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($filterMonth ?? '') == $i ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 500; margin-bottom: 6px;">Year</label>
                <input type="number" name="year" value="<?= esc($filterYear ?? date('Y')) ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
            </div>
            <div>
                <button type="submit" style="padding: 8px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; width: 100%;">Apply Filters</button>
            </div>
            <div>
                <a href="/WebSys_HMS_G3/accounts/expenses" style="display: block; padding: 8px 20px; background: #6b7280; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none;">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Expenses by Type Summary -->
<?php if (!empty($byType)): ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Expenses by Type</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Amount</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($byType as $type => $amount): ?>
                <tr>
                    <td><?= esc($type) ?></td>
                    <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($amount, 2) ?></td>
                    <td><?= $totalExpenses > 0 ? number_format(($amount / $totalExpenses) * 100, 1) : 0 ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Expenses by Patient (if no patient filter) -->
<?php if (empty($filterPatient) && !empty($byPatient)): ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Expenses by Patient</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Total Expenses</th>
                <th>Records</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            usort($byPatient, function($a, $b) {
                return $b['total'] <=> $a['total'];
            });
            foreach ($byPatient as $patientId => $patientData): 
            ?>
                <tr>
                    <td><?= esc($patientData['patient_name']) ?></td>
                    <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($patientData['total'], 2) ?></td>
                    <td><?= number_format($patientData['count']) ?></td>
                    <td>
                        <button onclick="viewPatientDetails(<?= esc($patientId) ?>)" style="padding: 4px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
                            View Details
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Patient Expenses Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Patient Expenses Details</div>
    </div>
    <?php if (!empty($patientExpenses)): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <?php if (empty($filterPatient)): ?>
                        <th>Patient</th>
                    <?php endif; ?>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patientExpenses as $expense): ?>
                    <tr>
                        <td><?= !empty($expense['expense_date']) ? date('M d, Y', strtotime($expense['expense_date'])) : 'N/A' ?></td>
                        <?php if (empty($filterPatient)): ?>
                            <td style="font-weight: 500;">
                                <button onclick="viewPatientDetails(<?= esc($expense['patient_id'] ?? '') ?>)" style="background: none; border: none; color: #3b82f6; cursor: pointer; text-decoration: underline; padding: 0;">
                                    <?= esc($expense['patient_name'] ?? 'N/A') ?>
                                </button>
                            </td>
                        <?php endif; ?>
                        <td>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; 
                                <?php
                                $type = strtolower($expense['expense_type'] ?? '');
                                if ($type === 'pharmacy') {
                                    echo 'background: #fef3c7; color: #92400e;';
                                } elseif ($type === 'laboratory') {
                                    echo 'background: #fce7f3; color: #9f1239;';
                                } elseif ($type === 'consultation') {
                                    echo 'background: #dbeafe; color: #1e40af;';
                                } elseif ($type === 'procedure') {
                                    echo 'background: #dcfce7; color: #166534;';
                                } elseif ($type === 'room & board') {
                                    echo 'background: #f3e8ff; color: #6b21a8;';
                                } else {
                                    echo 'background: #f3f4f6; color: #374151;';
                                }
                                ?>
                            ">
                                <?php
                                $typeLabel = $expense['expense_type'] ?? 'Other';
                                if ($type === 'pharmacy') echo '💊 ' . $typeLabel;
                                elseif ($type === 'laboratory') echo '⚗ ' . $typeLabel;
                                elseif ($type === 'consultation') echo '🩺 ' . $typeLabel;
                                elseif ($type === 'procedure') echo '🏥 ' . $typeLabel;
                                elseif ($type === 'room & board') echo '🏨 ' . $typeLabel;
                                else echo '📋 ' . $typeLabel;
                                ?>
                            </span>
                        </td>
                        <td><?= esc($expense['item_description'] ?? '-') ?></td>
                        <td style="color: #dc2626; font-weight: 600;">₱<?= number_format($expense['amount'] ?? 0, 2) ?></td>
                        <td style="font-size: 12px; color: #6b7280;"><?= esc($expense['reference'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
            <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;">💰</div>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No patient expenses found</div>
            <div style="font-size: 13px;"><?= (!empty($filterPatient) || !empty($filterMonth) || !empty($filterType)) ? 'Try adjusting your filters or ' : '' ?>Patient expenses are automatically generated when medicines are dispensed or lab tests are requested.</div>
        </div>
    <?php endif; ?>
</div>

<!-- Patient Details Modal -->
<div id="patientDetailsModal" class="modal-overlay">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Patient Expense Details</h2>
            <button onclick="closePatientDetailsModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div id="patientDetailsContent">
            <div style="text-align: center; padding: 40px;">
                <div style="font-size: 48px; margin-bottom: 16px;">⏳</div>
                <div>Loading...</div>
            </div>
        </div>
    </div>
</div>

<style>
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
    display: flex !important;
}
.modal-content {
    background: white;
    border-radius: 12px;
    padding: 24px;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}
</style>

<script>
function viewPatientDetails(patientId) {
    console.log('viewPatientDetails called with patientId:', patientId);
    const modal = document.getElementById('patientDetailsModal');
    const content = document.getElementById('patientDetailsContent');
    
    if (!modal || !content) {
        console.error('Modal elements not found!');
        alert('Error: Modal elements not found');
        return;
    }
    
    // Show modal with loading state
    modal.classList.add('active');
    console.log('Modal should be visible now');
    content.innerHTML = '<div style="text-align: center; padding: 40px;"><div style="font-size: 48px; margin-bottom: 16px;">⏳</div><div>Loading...</div></div>';
    
    // Fetch patient details
    fetch('/WebSys_HMS_G3/accounts/expenses/patient/' + patientId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = '<div style="text-align: center; padding: 40px; color: #dc2626;"><div style="font-size: 48px; margin-bottom: 16px;">❌</div><div>' + data.error + '</div></div>';
                return;
            }
            
            const patient = data.patient;
            const bills = data.bills;
            const summary = data.summary;
            
            let html = `
                <div style="margin-bottom: 24px;">
                    <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                        <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">${patient.patient_name}</div>
                        <div style="font-size: 13px; color: #6b7280;">
                            ${patient.patient_type} 
                            ${patient.date_of_birth ? '• ' + new Date(patient.date_of_birth).toLocaleDateString() : ''}
                            ${patient.gender ? '• ' + patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : ''}
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 20px;">
                        <div style="background: #eff6ff; padding: 12px; border-radius: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Bills</div>
                            <div style="font-size: 20px; font-weight: 600;">${summary.total_bills}</div>
                        </div>
                        <div style="background: #fef3c7; padding: 12px; border-radius: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Amount</div>
                            <div style="font-size: 20px; font-weight: 600; color: #dc2626;">₱${parseFloat(summary.total_amount).toFixed(2)}</div>
                        </div>
                        <div style="background: #d1fae5; padding: 12px; border-radius: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Paid</div>
                            <div style="font-size: 20px; font-weight: 600; color: #16a34a;">₱${parseFloat(summary.total_paid).toFixed(2)}</div>
                        </div>
                        <div style="background: #fee2e2; padding: 12px; border-radius: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Remaining</div>
                            <div style="font-size: 20px; font-weight: 600; color: #dc2626;">₱${parseFloat(summary.total_remaining).toFixed(2)}</div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Bill Details</div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 10px; text-align: left; font-weight: 600;">Invoice</th>
                                <th style="padding: 10px; text-align: left; font-weight: 600;">Date</th>
                                <th style="padding: 10px; text-align: left; font-weight: 600;">Type</th>
                                <th style="padding: 10px; text-align: left; font-weight: 600;">Description</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600;">Amount</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600;">Paid</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600;">Remaining</th>
                                <th style="padding: 10px; text-align: center; font-weight: 600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (bills.length === 0) {
                html += `
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #6b7280;">
                            No bills found for this patient
                        </td>
                    </tr>
                `;
            } else {
                bills.forEach(bill => {
                    const status = parseFloat(bill.remaining_amount) <= 0 ? 'Paid' : 'Pending';
                    const statusColor = status === 'Paid' ? '#16a34a' : '#dc2626';
                    const statusBg = status === 'Paid' ? '#d1fae5' : '#fee2e2';
                    
                    html += `
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 10px; font-weight: 500;">${bill.invoice_number || 'N/A'}</td>
                            <td style="padding: 10px;">${new Date(bill.created_at).toLocaleDateString()}</td>
                            <td style="padding: 10px;">${bill.bill_type || 'N/A'}</td>
                            <td style="padding: 10px;">${bill.description || '-'}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 600;">₱${parseFloat(bill.total_amount).toFixed(2)}</td>
                            <td style="padding: 10px; text-align: right; color: #16a34a;">₱${parseFloat(bill.paid_amount).toFixed(2)}</td>
                            <td style="padding: 10px; text-align: right; color: #dc2626; font-weight: 600;">₱${parseFloat(bill.remaining_amount).toFixed(2)}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: ${statusBg}; color: ${statusColor};">
                                    ${status}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            }
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            content.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div style="text-align: center; padding: 40px; color: #dc2626;"><div style="font-size: 48px; margin-bottom: 16px;">❌</div><div>Error loading patient details</div></div>';
        });
}

function closePatientDetailsModal() {
    document.getElementById('patientDetailsModal').classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('patientDetailsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePatientDetailsModal();
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
