<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Edit User Account</div>
        <div class="page-subtitle">Update user account details and permissions.</div>
    </div>
</div>

<style>
    .ua-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 14px 16px 16px;
        margin-bottom: 14px;
    }
    .ua-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 14px;
    }
    .ua-label {
        font-size: 13px;
        margin-bottom: 3px;
    }
    .ua-input,
    .ua-select {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        font-size: 13px;
    }
    .ua-message {
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 10px;
    }
    .ua-message.error {
        background: #ffebe9;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .ua-message.success {
        background: #ecfdf3;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .ua-saving {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #1d4ed8;
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
        align-items: center;
        gap: 8px;
    }
    .ua-saving.show {
        display: flex;
    }
    .ua-saving-icon {
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .ua-saved {
        background: #16a34a;
    }
    .ua-error-notif {
        background: #dc2626;
    }
</style>
<div id="save-indicator" class="ua-saving">
    <div class="ua-saving-icon"></div>
    <span id="save-message">Saving...</span>
</div>

<div class="ua-card">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ua-message error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="ua-message success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form id="user-edit-form" action="<?= base_url('admin/user-access/update/' . esc($user['id'])) ?>" method="post">
        <?= csrf_field() ?>
        <div class="ua-grid">
            <div>
                <div class="ua-label">First Name</div>
                <input type="text" name="first_name" id="first_name" class="ua-input" value="<?= esc($user['first_name'] ?? '') ?>">
            </div>
            <div>
                <div class="ua-label">Middle Name</div>
                <input type="text" name="middle_name" id="middle_name" class="ua-input" value="<?= esc($user['middle_name'] ?? '') ?>">
            </div>
            <div>
                <div class="ua-label">Last Name</div>
                <input type="text" name="last_name" id="last_name" class="ua-input" value="<?= esc($user['last_name'] ?? '') ?>">
            </div>
            <div style="grid-column: span 2;">
                <div class="ua-label">Address</div>
                <input type="text" name="address" id="address" class="ua-input" value="<?= esc($user['address'] ?? '') ?>" placeholder="Street, City, Province">
            </div>
            <div>
                <div class="ua-label">Username</div>
                <input type="text" name="username" id="username" class="ua-input" value="<?= esc($user['username'] ?? '') ?>">
            </div>
            <div>
                <div class="ua-label">Email</div>
                <input type="email" name="email" id="email" class="ua-input" value="<?= esc($user['email']) ?>" required>
            </div>
            <div>
                <div class="ua-label">Password <span style="color: #6b7280; font-size: 11px;">(leave blank to keep current)</span></div>
                <input type="password" name="password" id="password" class="ua-input" autocomplete="new-password">
            </div>
            <div>
                <div class="ua-label">Role</div>
                <select name="role_id" id="role_id" class="ua-select" required <?= ($user['id'] ?? 0) == 1 ? 'disabled' : '' ?>>
                    <option value="">Select role</option>
                    <?php foreach (($roles ?? []) as $role): ?>
                        <option value="<?= esc($role['id']) ?>" data-name="<?= esc($role['name']) ?>" <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                            <?= esc($role['display_name'] ?: ucfirst($role['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (($user['id'] ?? 0) == 1): ?>
                    <input type="hidden" name="role_id" value="<?= esc($user['role_id']) ?>">
                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">System Admin role cannot be changed</div>
                <?php endif; ?>
            </div>
            <div>
                <div class="ua-label">Account Status</div>
                <select name="status" id="status" class="ua-select" required>
                    <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div id="license-number-field" style="display: none;">
                <div class="ua-label">License Number</div>
                <input type="text" name="license_number" id="license_number" class="ua-input" value="<?= esc(($doctorInfo['license_number'] ?? $nurseInfo['license_number'] ?? '')) ?>">
                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">License number for doctor/nurse</div>
            </div>
            <div id="specialization-field" style="display: none;">
                <div class="ua-label">Specialization</div>
                <select name="specialization" id="specialization" class="ua-select">
                    <option value="">Select Specialization</option>
                    <option value="Cardiology" <?= ($doctorInfo['specialization'] ?? '') === 'Cardiology' ? 'selected' : '' ?>>Cardiology</option>
                    <option value="Dermatology" <?= ($doctorInfo['specialization'] ?? '') === 'Dermatology' ? 'selected' : '' ?>>Dermatology</option>
                    <option value="Emergency Medicine" <?= ($doctorInfo['specialization'] ?? '') === 'Emergency Medicine' ? 'selected' : '' ?>>Emergency Medicine</option>
                    <option value="Family Medicine" <?= ($doctorInfo['specialization'] ?? '') === 'Family Medicine' ? 'selected' : '' ?>>Family Medicine</option>
                    <option value="General Surgery" <?= ($doctorInfo['specialization'] ?? '') === 'General Surgery' ? 'selected' : '' ?>>General Surgery</option>
                    <option value="Internal Medicine" <?= ($doctorInfo['specialization'] ?? '') === 'Internal Medicine' ? 'selected' : '' ?>>Internal Medicine</option>
                    <option value="Neurology" <?= ($doctorInfo['specialization'] ?? '') === 'Neurology' ? 'selected' : '' ?>>Neurology</option>
                    <option value="Obstetrics and Gynecology" <?= ($doctorInfo['specialization'] ?? '') === 'Obstetrics and Gynecology' ? 'selected' : '' ?>>Obstetrics and Gynecology</option>
                    <option value="Oncology" <?= ($doctorInfo['specialization'] ?? '') === 'Oncology' ? 'selected' : '' ?>>Oncology</option>
                    <option value="Orthopedics" <?= ($doctorInfo['specialization'] ?? '') === 'Orthopedics' ? 'selected' : '' ?>>Orthopedics</option>
                    <option value="Pediatrics" <?= ($doctorInfo['specialization'] ?? '') === 'Pediatrics' ? 'selected' : '' ?>>Pediatrics</option>
                    <option value="Psychiatry" <?= ($doctorInfo['specialization'] ?? '') === 'Psychiatry' ? 'selected' : '' ?>>Psychiatry</option>
                    <option value="Pulmonology" <?= ($doctorInfo['specialization'] ?? '') === 'Pulmonology' ? 'selected' : '' ?>>Pulmonology</option>
                    <option value="Radiology" <?= ($doctorInfo['specialization'] ?? '') === 'Radiology' ? 'selected' : '' ?>>Radiology</option>
                    <option value="Urology" <?= ($doctorInfo['specialization'] ?? '') === 'Urology' ? 'selected' : '' ?>>Urology</option>
                    <option value="Anesthesiology" <?= ($doctorInfo['specialization'] ?? '') === 'Anesthesiology' ? 'selected' : '' ?>>Anesthesiology</option>
                    <option value="Pathology" <?= ($doctorInfo['specialization'] ?? '') === 'Pathology' ? 'selected' : '' ?>>Pathology</option>
                    <option value="Ophthalmology" <?= ($doctorInfo['specialization'] ?? '') === 'Ophthalmology' ? 'selected' : '' ?>>Ophthalmology</option>
                    <option value="ENT (Ear, Nose, Throat)" <?= ($doctorInfo['specialization'] ?? '') === 'ENT (Ear, Nose, Throat)' ? 'selected' : '' ?>>ENT (Ear, Nose, Throat)</option>
                    <option value="Other" <?= ($doctorInfo['specialization'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;">
            <a href="<?= base_url('admin/user-access') ?>" class="ua-input" style="width:auto;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;cursor:pointer;text-decoration:none;text-align:center;padding:6px 16px;color:#111827;">Cancel</a>
            <button type="submit" class="ua-input" style="width:auto;border-radius:999px;border:none;background:#1d4ed8;color:#ffffff;font-weight:600;cursor:pointer;">Save Changes</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('user-edit-form');
    const roleSelect = document.getElementById('role_id');
    const licenseField = document.getElementById('license-number-field');
    const specializationField = document.getElementById('specialization-field');
    const saveIndicator = document.getElementById('save-indicator');
    const saveMessage = document.getElementById('save-message');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Show saving indicator
    function showSaving(message = 'Saving...') {
        if (saveIndicator) {
            saveIndicator.classList.add('show');
            saveIndicator.classList.remove('ua-saved', 'ua-error-notif');
            if (saveMessage) saveMessage.textContent = message;
        }
    }

    // Show saved indicator
    function showSaved(message = 'Saved successfully!') {
        if (saveIndicator) {
            saveIndicator.classList.add('show', 'ua-saved');
            saveIndicator.classList.remove('ua-error-notif');
            if (saveMessage) saveMessage.textContent = message;
        }
    }

    // Show error indicator
    function showError(message = 'Error saving') {
        if (saveIndicator) {
            saveIndicator.classList.add('show', 'ua-error-notif');
            saveIndicator.classList.remove('ua-saved');
            if (saveMessage) saveMessage.textContent = message;
            setTimeout(() => {
                saveIndicator.classList.remove('show');
            }, 3000);
        }
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        showSaving('Saving changes...');
        
        // Disable submit button to prevent double submission
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }

        // Submit form using standard POST
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                // Success - redirect to user list
                showSaved('Saved successfully! Redirecting...');
                setTimeout(() => {
                    window.location.href = '<?= base_url('admin/user-access') ?>';
                }, 1000);
            } else if (response.ok) {
                // Check if response is JSON (API response)
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        if (data.success) {
                            showSaved('Saved successfully! Redirecting...');
                            setTimeout(() => {
                                window.location.href = data.redirect || '<?= base_url('admin/user-access') ?>';
                            }, 1000);
                        } else {
                            throw new Error(data.error || 'Failed to save');
                        }
                    });
                } else {
                    // HTML response - likely success, redirect
                    showSaved('Saved successfully! Redirecting...');
                    setTimeout(() => {
                        window.location.href = '<?= base_url('admin/user-access') ?>';
                    }, 1000);
                }
            } else {
                throw new Error('Save failed');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showError('Error saving changes. Please try again.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        });
    });

    // Update role-dependent fields
    function updateFields() {
        const roleId = parseInt(roleSelect.value);
        const roleName = roleSelect.options[roleSelect.selectedIndex]?.getAttribute('data-name');
        
        if (roleName === 'doctor' || roleName === 'nurse') {
            if (licenseField) licenseField.style.display = 'block';
            if (roleName === 'doctor') {
                if (specializationField) specializationField.style.display = 'block';
            } else {
                if (specializationField) specializationField.style.display = 'none';
            }
        } else {
            if (licenseField) licenseField.style.display = 'none';
            if (specializationField) specializationField.style.display = 'none';
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', updateFields);
        updateFields(); // Initialize on page load
    }
});
</script>

<?= $this->endSection() ?>

