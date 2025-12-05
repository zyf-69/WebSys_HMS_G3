<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
    <div class="auth-title">Sign in to HMS System</div>
    <div class="auth-subtitle">Use your hospital account to access the Hospital Management System.</div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash-message flash-error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash-message flash-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('login') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                placeholder="name@example.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <div class="form-row">
            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Remember this device</span>
            </label>
            <span style="color:#6a737d;">Admin & staff only</span>
        </div>

        <button type="submit" class="btn-primary">Sign in</button>

        <div class="auth-footer-text">
            Having trouble signing in? Contact the IT department of St. Peter Hospital.
        </div>
    </form>
<?= $this->endSection() ?>
