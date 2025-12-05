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
                <div class="ua-label">Last Name</div>
                <input type="text" name="last_name" class="ua-input" value="<?= old('last_name') ?>">
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
                <select name="role_id" class="ua-select" required>
                    <option value="">Select role</option>
                    <?php foreach (($roles ?? []) as $role): ?>
                        <option value="<?= esc($role['id']) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                            <?= esc($role['display_name'] ?: ucfirst($role['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="reset" class="ua-input" style="width:auto;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;cursor:pointer;">Clear</button>
            <button type="submit" class="ua-input" style="width:auto;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-weight:600;cursor:pointer;">Create Account</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
