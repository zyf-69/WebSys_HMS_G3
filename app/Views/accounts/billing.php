<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Billing & Invoices</div>
        <div class="page-subtitle">Manage patient bills, invoices, and payment records.</div>
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

<style>
.billing-tab-nav {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 24px;
}
.tab-button {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: -2px;
}
.tab-button:hover {
    color: #374151;
    background: #f9fafb;
}
.tab-button.active {
    color: #16a34a;
    border-bottom-color: #16a34a;
    background: #f0fdf4;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
.search-filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    align-items: center;
    flex-wrap: wrap;
}
.search-input {
    flex: 1;
    min-width: 200px;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
}
.filter-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
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
    background: white;
    border-radius: 12px;
    padding: 24px;
    max-width: 500px;
    width: 90%;
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
}
.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}
.form-group {
    margin-bottom: 16px;
}
.form-label {
    display: block;
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
    box-sizing: border-box;
}
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
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
</style>

<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Bills</div>
        </div>
        <div class="card-value"><?= number_format($totalBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Paid Bills</div>
        </div>
        <div class="card-value" style="color: #16a34a;"><?= number_format($paidBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Bills</div>
        </div>
        <div class="card-value" style="color: #dc2626;"><?= number_format($pendingBills ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Revenue</div>
        </div>
        <div class="card-value" style="color: #16a34a;">₱<?= number_format($totalRevenue ?? 0, 2) ?></div>
    </div>
</div>

<div class="billing-tab-nav">
    <button class="tab-button active" onclick="showTab('bills')">Bills & Invoices</button>
    <button class="tab-button" onclick="showTab('payments')">Payment History</button>
</div>

<!-- Bills Tab -->
<div id="billsTab" class="tab-content active">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div class="card-title" style="font-size: 14px; font-weight: 600;">All Bills & Invoices</div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button onclick="openCreateBillModal()" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    ➕ Create Bill
                </button>
                <div class="search-filter-bar" style="margin: 0;">
                    <input type="text" id="billSearch" class="search-input" placeholder="Search by patient name, bill ID, or type..." onkeyup="filterBills()">
                    <select id="billStatusFilter" class="filter-select" onchange="filterBills()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="billsTableContainer">
            <?php if (!empty($bills)): ?>
                <table id="billsTable">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Patient Type</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bills as $patientAccount): ?>
                            <?php 
                            $remaining = floatval($patientAccount['remaining_amount'] ?? 0);
                            $patientType = $patientAccount['patient_type'] ?? 'outpatient';
                            $isInpatient = $patientType === 'inpatient';
                            $status = $patientAccount['has_pending'] ? 'pending' : ($patientAccount['has_paid'] ? 'partial' : 'paid');
                            ?>
                            <tr data-status="<?= esc($status) ?>" data-patient-type="<?= esc($patientType) ?>" data-patient-id="<?= esc($patientAccount['patient_id']) ?>">
                                <td style="font-weight: 600;"><?= esc($patientAccount['patient_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; <?= $isInpatient ? 'background: #dbeafe; color: #1e40af;' : 'background: #fef3c7; color: #92400e;' ?>">
                                        <?= $isInpatient ? '🏥 Inpatient' : '🚶 Outpatient' ?>
                                    </span>
                                </td>
                                <td>₱<?= number_format($patientAccount['total_amount'] ?? 0, 2) ?></td>
                                <td style="color: #16a34a;">₱<?= number_format($patientAccount['paid_amount'] ?? 0, 2) ?></td>
                                <td style="color: <?= $remaining > 0 ? '#dc2626' : '#16a34a' ?>;">
                                    ₱<?= number_format($remaining, 2) ?>
                                </td>
                                <td>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 500; <?= $status === 'paid' ? 'background: #d1fae5; color: #065f46;' : ($status === 'partial' ? 'background: #dbeafe; color: #1e40af;' : 'background: #fef3c7; color: #92400e;') ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <?php if ($remaining > 0): ?>
                                            <button onclick='openPaymentModal(<?= esc($patientAccount['patient_id']) ?>, <?= json_encode($patientAccount['patient_name'] ?? 'N/A') ?>, <?= json_encode($patientAccount['patient_type'] ?? 'outpatient') ?>, <?= esc($patientAccount['total_amount']) ?>, <?= esc($patientAccount['paid_amount'] ?? 0) ?>, <?= esc($remaining) ?>)' style="padding: 6px 12px; background: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer;">
                                                Record Payment
                                            </button>
                                        <?php endif; ?>
                                        <button onclick='openFollowUpPaymentModal(<?= esc($patientAccount['patient_id']) ?>, <?= json_encode($patientAccount['patient_name'] ?? 'N/A') ?>)' style="padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer;">
                                            ➕ Follow-up Payment
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                    <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">💲</div>
                    <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No bills found</div>
                    <div style="font-size: 12px;">Bills will appear here once created.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payments Tab -->
<div id="paymentsTab" class="tab-content">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title" style="font-size: 14px; font-weight: 600;">Payment History</div>
            <div class="search-filter-bar" style="margin: 0;">
                <input type="text" id="paymentSearch" class="search-input" placeholder="Search by patient name or bill type..." onkeyup="filterPayments()">
                <select id="paymentMethodFilter" class="filter-select" onchange="filterPayments()">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="online">Online</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
        </div>
        <div id="paymentsTableContainer">
            <?php if (!empty($payments)): ?>
                <table id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Bill Type</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr data-method="<?= esc($payment['payment_method'] ?? 'cash') ?>">
                                <td><?= !empty($payment['payment_date'] ?? $payment['display_date'] ?? null) ? date('M d, Y', strtotime($payment['payment_date'] ?? $payment['display_date'])) : 'N/A' ?></td>
                                <td><?= esc($payment['patient_name'] ?? 'N/A') ?></td>
                                <td><?= esc($payment['bill_type'] ?? 'N/A') ?></td>
                                <td style="color: <?= floatval($payment['amount'] ?? 0) >= 0 ? '#16a34a' : '#dc2626' ?>;">
                                    ₱<?= number_format(abs(floatval($payment['amount'] ?? 0)), 2) ?>
                                    <?= floatval($payment['amount'] ?? 0) < 0 ? '(Refund)' : '' ?>
                                </td>
                                <td><?= esc(ucfirst($payment['payment_method'] ?? 'cash')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                    <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">💳</div>
                    <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No payments found</div>
                    <div style="font-size: 12px;">Payment records will appear here once payments are made.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Bill Modal -->
<div id="createBillModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Create New Bill</div>
            <button class="modal-close" onclick="closeCreateBillModal()">&times;</button>
        </div>
        <form action="/WebSys_HMS_G3/accounts/billing" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Patient *</label>
                <select name="patient_id" class="form-select" required>
                    <option value="">Select a patient</option>
                    <?php foreach ($patients ?? [] as $patient): ?>
                        <option value="<?= esc($patient['id']) ?>"><?= esc($patient['patient_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Bill Type *</label>
                <select name="bill_type" class="form-select" required>
                    <option value="">Select bill type</option>
                    <option value="Consultation">Consultation</option>
                    <option value="Laboratory">Laboratory</option>
                    <option value="Pharmacy">Pharmacy</option>
                    <option value="Procedure">Procedure</option>
                    <option value="Room & Board">Room & Board</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Total Amount *</label>
                <input type="number" name="total_amount" class="form-input" step="0.01" min="0.01" required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Enter the total amount for this bill.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Bill description (optional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeCreateBillModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Bill</button>
            </div>
        </form>
    </div>
</div>

<!-- Receive Payment Modal -->
<div id="paymentModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Record Payment</div>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form action="/WebSys_HMS_G3/accounts/payments/record" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="patient_id" id="payment_patient_id">
            <div class="form-group">
                <label class="form-label">Account Information</label>
                <div style="padding: 12px; background: #f9fafb; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">
                    <div><strong>Patient:</strong> <span id="payment_patient_name" style="font-weight: 600;"></span></div>
                    <div><strong>Patient Type:</strong> <span id="payment_patient_type" style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 4px;"></span></div>
                    <div><strong>Total:</strong> ₱<span id="payment_total_amount"></span></div>
                    <div><strong>Paid:</strong> ₱<span id="payment_paid_amount"></span></div>
                    <div><strong>Remaining:</strong> ₱<span id="payment_remaining_amount" style="color: #dc2626; font-weight: 600;"></span></div>
                </div>
                <div style="padding: 12px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; font-size: 12px;">
                    <div style="font-weight: 600; margin-bottom: 8px;">Bills Included:</div>
                    <div id="payment_bills_list" style="max-height: 150px; overflow-y: auto;">
                        <!-- Bills will be listed here -->
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Amount *</label>
                <input type="number" name="payment_amount" id="payment_amount" class="form-input" step="0.01" min="0.01" required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Enter the amount to record. Cannot exceed remaining balance.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Date *</label>
                <input type="date" name="payment_date" class="form-input" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="online">Online</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-input" rows="2" placeholder="Additional payment notes (optional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Follow-up Payment Modal -->
<div id="followUpPaymentModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Create Follow-up Bill</div>
            <button class="modal-close" onclick="closeFollowUpPaymentModal()">&times;</button>
        </div>
        <form action="/WebSys_HMS_G3/accounts/billing" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="patient_id" id="followup_payment_patient_id">
            <div class="form-group">
                <label class="form-label">Patient</label>
                <div style="padding: 12px; background: #f9fafb; border-radius: 8px; font-size: 13px;">
                    <div><strong>Name:</strong> <span id="followup_payment_patient_name" style="font-weight: 600;"></span></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bill Type *</label>
                <select name="bill_type" class="form-select" required>
                    <option value="">Select bill type</option>
                    <option value="Consultation">Consultation</option>
                    <option value="Laboratory">Laboratory</option>
                    <option value="Pharmacy">Pharmacy</option>
                    <option value="Procedure">Procedure</option>
                    <option value="Room & Board">Room & Board</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Total Amount *</label>
                <input type="number" name="total_amount" class="form-input" step="0.01" min="0.01" required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Enter the total amount for this bill.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Bill description (optional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeFollowUpPaymentModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Bill</button>
            </div>
        </form>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Record Refund</div>
            <button class="modal-close" onclick="closeRefundModal()">&times;</button>
        </div>
        <form action="/WebSys_HMS_G3/accounts/payments/refund" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="bill_id" id="refund_bill_id">
            <div class="form-group">
                <label class="form-label">Bill Information</label>
                <div style="padding: 12px; background: #f9fafb; border-radius: 8px; font-size: 13px;">
                    <div><strong>Invoice ID:</strong> <span id="refund_invoice_number" style="font-weight: 600;"></span></div>
                    <div><strong>Patient:</strong> <span id="refund_patient_name"></span></div>
                    <div><strong>Total:</strong> ₱<span id="refund_total_amount"></span></div>
                    <div><strong>Paid:</strong> ₱<span id="refund_paid_amount"></span></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Refund Amount *</label>
                <input type="number" name="refund_amount" id="refund_amount" class="form-input" step="0.01" min="0.01" required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Enter the refund amount. Cannot exceed paid amount.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-input" rows="3" placeholder="Reason for refund"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeRefundModal()">Cancel</button>
                <button type="submit" class="btn-primary">Record Refund</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.add('active');
    event.target.classList.add('active');
}

function filterBills() {
    const search = document.getElementById('billSearch').value.toLowerCase();
    const statusFilter = document.getElementById('billStatusFilter').value;
    const patientTypeFilter = document.getElementById('billPatientTypeFilter').value;
    const table = document.getElementById('billsTable');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        const patientType = row.getAttribute('data-patient-type');
        const matchesSearch = text.includes(search);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesPatientType = !patientTypeFilter || patientType === patientTypeFilter;
        
        row.style.display = (matchesSearch && matchesStatus && matchesPatientType) ? '' : 'none';
    });
}

function filterPayments() {
    const search = document.getElementById('paymentSearch').value.toLowerCase();
    const methodFilter = document.getElementById('paymentMethodFilter').value;
    const table = document.getElementById('paymentsTable');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const method = row.getAttribute('data-method');
        const matchesSearch = text.includes(search);
        const matchesMethod = !methodFilter || method === methodFilter;
        
        row.style.display = (matchesSearch && matchesMethod) ? '' : 'none';
    });
}

function openPaymentModal(patientId, patientName, patientType, totalAmount, paidAmount, remaining) {
    document.getElementById('payment_patient_id').value = patientId;
    document.getElementById('payment_patient_name').textContent = patientName;
    document.getElementById('payment_total_amount').textContent = parseFloat(totalAmount).toFixed(2);
    document.getElementById('payment_paid_amount').textContent = parseFloat(paidAmount).toFixed(2);
    document.getElementById('payment_remaining_amount').textContent = parseFloat(remaining).toFixed(2);
    document.getElementById('payment_amount').value = parseFloat(remaining).toFixed(2);
    document.getElementById('payment_amount').max = remaining;
    
    // Update patient type indicator
    const patientTypeSpan = document.getElementById('payment_patient_type');
    if (patientTypeSpan) {
        const isInpatient = patientType === 'inpatient';
        patientTypeSpan.textContent = isInpatient ? '🏥 Inpatient' : '🚶 Outpatient';
        patientTypeSpan.style.background = isInpatient ? '#dbeafe' : '#fef3c7';
        patientTypeSpan.style.color = isInpatient ? '#1e40af' : '#92400e';
    }
    
    // Fetch and display all bills for this patient
    fetch('/WebSys_HMS_G3/accounts/billing/patient-bills/' + patientId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('payment_bills_list').innerHTML = '<div style="color: #dc2626;">' + data.error + '</div>';
                return;
            }
            
            let html = '';
            if (data.bills && data.bills.length > 0) {
                data.bills.forEach(bill => {
                    const billRemaining = parseFloat(bill.remaining_amount || 0);
                    if (billRemaining > 0) {
                        html += `<div style="padding: 8px; margin-bottom: 6px; background: white; border-radius: 6px; border-left: 3px solid #3b82f6;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 600; font-size: 12px;">${bill.invoice_number || 'N/A'}</div>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">${bill.bill_type || 'N/A'}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 11px; color: #6b7280;">Remaining:</div>
                                    <div style="font-weight: 600; color: #dc2626; font-size: 12px;">₱${billRemaining.toFixed(2)}</div>
                                </div>
                            </div>
                        </div>`;
                    }
                });
            }
            
            if (!html) {
                html = '<div style="color: #6b7280; font-size: 12px;">No pending bills found.</div>';
            }
            
            document.getElementById('payment_bills_list').innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching bills:', error);
            document.getElementById('payment_bills_list').innerHTML = '<div style="color: #dc2626;">Error loading bills.</div>';
        });
    
    document.getElementById('paymentModal').classList.add('active');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.remove('active');
    document.getElementById('payment_patient_id').value = '';
    document.getElementById('payment_amount').value = '';
    document.getElementById('payment_bills_list').innerHTML = '';
}

function openRefundModal(patientId, patientName, totalAmount, paidAmount) {
    // Refund functionality removed - bills are auto-generated from services
    alert('Refunds should be handled through the service providers (medicine returns, lab test cancellations, etc.). Bills are automatically generated based on services provided.');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.remove('active');
    document.getElementById('refund_bill_id').value = '';
    document.getElementById('refund_amount').value = '';
}

function openCreateBillModal() {
    document.getElementById('createBillModal').classList.add('active');
}

function closeCreateBillModal() {
    document.getElementById('createBillModal').classList.remove('active');
    document.querySelector('#createBillModal form').reset();
}

function openFollowUpPaymentModal(patientId, patientName) {
    document.getElementById('followup_payment_patient_id').value = patientId;
    document.getElementById('followup_payment_patient_name').textContent = patientName;
    document.querySelector('#followUpPaymentModal form').reset();
    document.getElementById('followup_payment_patient_id').value = patientId; // Reset after form reset
    document.getElementById('followup_payment_patient_name').textContent = patientName;
    document.getElementById('followUpPaymentModal').classList.add('active');
}

function closeFollowUpPaymentModal() {
    document.getElementById('followUpPaymentModal').classList.remove('active');
    document.querySelector('#followUpPaymentModal form').reset();
}

</script>

<?= $this->endSection() ?>
