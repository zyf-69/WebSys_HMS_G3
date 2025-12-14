<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">User Access &amp; Security</div>
        <div class="page-subtitle">Create user accounts and assign appropriate hospital roles.</div>
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
</style>

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

    <form action="<?= base_url('admin/user-access') ?>" method="post">
        <?= csrf_field() ?>
        <div class="ua-grid">
            <div>
                <div class="ua-label">First Name</div>
                <input type="text" name="first_name" class="ua-input" value="<?= old('first_name') ?>">
            </div>
            <div>
                <div class="ua-label">Middle Name</div>
                <input type="text" name="middle_name" class="ua-input" value="<?= old('middle_name') ?>">
            </div>
            <div>
                <div class="ua-label">Last Name</div>
                <input type="text" name="last_name" class="ua-input" value="<?= old('last_name') ?>">
            </div>
            <div>
                <div class="ua-label">Address</div>
                <input type="text" name="address" class="ua-input" value="<?= old('address') ?>" placeholder="Street, City, Province">
            </div>
            <div>
                <div class="ua-label">Username</div>
                <input type="text" name="username" class="ua-input" value="<?= old('username') ?>">
            </div>
            <div>
                <div class="ua-label">Email</div>
                <input type="email" name="email" class="ua-input" value="<?= old('email') ?>" required>
            </div>
            <div>
                <div class="ua-label">Password</div>
                <input type="password" name="password" class="ua-input" required>
            </div>
            <div>
                <div class="ua-label">Confirm Password</div>
                <input type="password" name="confirm_password" class="ua-input" required>
            </div>
            <div>
                <div class="ua-label">Role</div>
                <select name="role_id" id="role_id" class="ua-select" required>
                    <option value="">Select role</option>
                    <?php foreach (($roles ?? []) as $role): ?>
                        <option value="<?= esc($role['id']) ?>" data-name="<?= esc($role['name']) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                            <?= esc($role['display_name'] ?: ucfirst($role['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <div class="ua-label">Account Status</div>
                <select name="status" class="ua-select" required>
                    <option value="active" <?= old('status') == 'active' || old('status') === null ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div id="license-number-field" style="display: none;">
                <div class="ua-label">License Number</div>
                <input type="text" name="license_number" id="license_number" class="ua-input" readonly>
                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">Auto-generated license number</div>
            </div>
            <div id="specialization-field" style="display: none;">
                <div class="ua-label">Specialization</div>
                <select name="specialization" id="specialization" class="ua-select">
                    <option value="">Select Specialization</option>
                    <option value="Cardiology">Cardiology</option>
                    <option value="Dermatology">Dermatology</option>
                    <option value="Emergency Medicine">Emergency Medicine</option>
                    <option value="Family Medicine">Family Medicine</option>
                    <option value="General Surgery">General Surgery</option>
                    <option value="Internal Medicine">Internal Medicine</option>
                    <option value="Neurology">Neurology</option>
                    <option value="Obstetrics and Gynecology">Obstetrics and Gynecology</option>
                    <option value="Oncology">Oncology</option>
                    <option value="Orthopedics">Orthopedics</option>
                    <option value="Pediatrics">Pediatrics</option>
                    <option value="Psychiatry">Psychiatry</option>
                    <option value="Pulmonology">Pulmonology</option>
                    <option value="Radiology">Radiology</option>
                    <option value="Urology">Urology</option>
                    <option value="Anesthesiology">Anesthesiology</option>
                    <option value="Pathology">Pathology</option>
                    <option value="Ophthalmology">Ophthalmology</option>
                    <option value="ENT (Ear, Nose, Throat)">ENT (Ear, Nose, Throat)</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="reset" class="ua-input" style="width:auto;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;cursor:pointer;">Clear</button>
            <button type="submit" class="ua-input" style="width:auto;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-weight:600;cursor:pointer;">Create Account</button>
        </div>
    </form>
</div>

<?php if (!empty($users)): ?>
<div class="ua-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <div class="patient-section-title" style="font-size:14px;font-weight:600;">All User Accounts</div>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb;">
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">ID</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Full Name</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Username</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Email</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Role</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Status</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Created</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:600;font-size:12px;color:#6b7280;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:12px;">#<?= esc($user['id']) ?></td>
                        <td style="padding:12px;">
                            <strong><?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></strong>
                            <?php if (!empty($user['address'])): ?>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;"><?= esc($user['address']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="padding:12px;"><?= esc($user['username'] ?? '—') ?></td>
                        <td style="padding:12px;"><?= esc($user['email']) ?></td>
                        <td style="padding:12px;">
                            <span style="display:inline-block;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:500;background:#dbeafe;color:#1e40af;">
                                <?= esc($user['role_display_name'] ?? ucfirst($user['role_name'] ?? 'N/A')) ?>
                            </span>
                        </td>
                        <td style="padding:12px;">
                            <span style="display:inline-block;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:500;<?= $user['status'] === 'active' ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;' ?>">
                                <?= esc(ucfirst($user['status'])) ?>
                            </span>
                        </td>
                        <td style="padding:12px;">
                            <?php if (!empty($user['created_at'])): ?>
                                <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                    <?= date('h:i A', strtotime($user['created_at'])) ?>
                                </div>
                            <?php else: ?>
                                <span style="color:#9ca3af;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:12px;">
                            <a href="<?= base_url('admin/user-access/edit/' . esc($user['id'])) ?>" style="display:inline-block;padding:4px 12px;border-radius:6px;background:#1d4ed8;color:#ffffff;text-decoration:none;font-size:12px;font-weight:500;">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
    (function() {
        const roleSelect = document.getElementById('role_id');
        const licenseField = document.getElementById('license-number-field');
        const licenseInput = document.getElementById('license_number');
        const specializationField = document.getElementById('specialization-field');
        const specializationSelect = document.getElementById('specialization');
        
        function generateLicenseNumber(roleName) {
            // Get current timestamp and random number for uniqueness
            const timestamp = Date.now().toString().slice(-6);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            
            if (roleName === 'doctor') {
                return 'DR-' + timestamp + random;
            } else if (roleName === 'nurse') {
                return 'NR-' + timestamp + random;
            }
            return '';
        }
        
        function updateRoleFields() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption.getAttribute('data-name');
            const roleId = parseInt(roleSelect.value);
            
            // Show license field for doctor (role_id 3) or nurse (role_id 4)
            if (roleId === 3 || roleId === 4) {
                licenseField.style.display = 'block';
                const licenseNumber = generateLicenseNumber(roleName);
                licenseInput.value = licenseNumber;
            } else {
                licenseField.style.display = 'none';
                licenseInput.value = '';
            }
            
            // Show specialization field only for doctor (role_id 3)
            if (roleId === 3) {
                specializationField.style.display = 'block';
            } else {
                specializationField.style.display = 'none';
                specializationSelect.value = '';
            }
        }
        
        // Initial check on page load
        updateRoleFields();
        
        // Update when role changes
        roleSelect.addEventListener('change', updateRoleFields);
    })();
</script>

<?= $this->endSection() ?>
