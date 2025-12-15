<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Edit User</div>
        <div class="page-subtitle">Update user account details.</div>
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
    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
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
        text-decoration: none;
        display: inline-block;
    }
    .btn-secondary:hover {
        background: #f9fafb;
    }
    .form-input:read-only {
        background: #f3f4f6;
        cursor: not-allowed;
    }
</style>

<div class="form-card">
    <form action="/WebSys_HMS_G3/it/user-management/update/<?= esc($user['id']) ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-input" value="<?= esc($user['first_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" name="middle_name" class="form-input" value="<?= esc($user['middle_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-input" value="<?= esc($user['last_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" value="<?= esc($user['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input" value="<?= esc($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" class="form-input" value="<?= esc($user['role_display_name'] ?? ucfirst($user['role_name'] ?? 'N/A')) ?>" readonly style="background: #f3f4f6; cursor: not-allowed;">
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Role cannot be changed</div>
            </div>
            <?php
            // Show license number if user is a doctor or nurse (read-only)
            $roleId = $user['role_id'] ?? null;
            $isDoctorOrNurse = ($roleId == 3 || $roleId == 4);
            $licenseNumber = null;
            if ($roleId == 3 && !empty($doctorInfo['license_number'])) {
                $licenseNumber = $doctorInfo['license_number'];
            } elseif ($roleId == 4 && !empty($nurseInfo['license_number'])) {
                $licenseNumber = $nurseInfo['license_number'];
            }
            if ($isDoctorOrNurse):
            ?>
            <div class="form-group">
                <label class="form-label">License Number</label>
                <input type="text" class="form-input" value="<?= esc($licenseNumber ?? 'N/A') ?>" readonly style="background: #f3f4f6; cursor: not-allowed;">
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">License number cannot be changed</div>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-input" value="<?= esc($user['address'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <a href="/WebSys_HMS_G3/it/user-management" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update User</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

