<?= isset($this) ? $this->extend ?? '' : '' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'HMS System') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #1f6feb;
            --primary-dark: #184fb0;
            --bg-light: #f5f7fb;
            --card-bg: #ffffff;
            --text-main: #1b1f23;
            --text-muted: #6a737d;
            --danger: #d73a49;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top left, #e2ebff 0, #f5f7fb 55%, #e9f0ff 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 960px;
            padding: 24px;
        }
        .auth-card {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 0;
            -webkit-border-radius: 16px;
            border-radius: 16px;
            overflow: hidden;
            -webkit-box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
            background-color: var(--card-bg);
        }
        .auth-brand {
            padding: 32px 32px 40px;
            background: linear-gradient(145deg, #102a6b 0, #1f6feb 45%, #2b8af2 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 600;
        }
        .brand-subtitle {
            font-size: 13px;
            opacity: 0.9;
        }
        .brand-highlight {
            margin-top: 32px;
        }
        .brand-highlight h2 {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .brand-highlight p {
            font-size: 14px;
            opacity: 0.95;
            line-height: 1.6;
        }
        .brand-footer {
            margin-top: 40px;
            font-size: 12px;
            opacity: 0.85;
        }
        .auth-form-pane {
            padding: 32px 32px 36px;
            background: var(--card-bg);
        }
        .auth-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .auth-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }
        .flash-message {
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .flash-error {
            background: #ffebe9;
            color: var(--danger);
            border: 1px solid #fdaeb7;
        }
        .flash-success {
            background: #e6ffed;
            color: #22863a;
            border: 1px solid #a2f0a1;
        }
        form {
            margin-top: 8px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: var(--text-main);
            font-weight: 500;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d0d7de;
            font-size: 14px;
            transition: border-color 0.16s ease, -webkit-box-shadow 0.16s ease, box-shadow 0.16s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            -webkit-box-shadow: 0 0 0 1px rgba(31, 111, 235, 0.25);
            box-shadow: 0 0 0 1px rgba(31, 111, 235, 0.25);
        }
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 16px;
        }
        .checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .checkbox input {
            width: 14px;
            height: 14px;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px 14px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.12s ease, -webkit-box-shadow 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
        }
        .btn-primary:hover {
            filter: brightness(1.03);
            -webkit-box-shadow: 0 10px 22px rgba(31, 111, 235, 0.35);
            box-shadow: 0 10px 22px rgba(31, 111, 235, 0.35);
            transform: translateY(-1px);
        }
        .auth-footer-text {
            margin-top: 16px;
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
        }
        @media (max-width: 768px) {
            .auth-card {
                grid-template-columns: minmax(0, 1fr);
            }
            .auth-brand {
                padding: 20px;
            }
            .auth-form-pane {
                padding: 22px 20px 28px;
            }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-brand">
            <div>
                <div class="brand-header">
                    <div class="brand-logo">HS</div>
                    <div>
                        <div class="brand-title">HMS System</div>
                        <div class="brand-subtitle">St. Peter Hospital</div>
                    </div>
                </div>
                <div class="brand-highlight">
                    <h2>Secure Hospital Access</h2>
                    <p>Sign in to manage schedules, patients, departments, staff, and hospital operations in a unified, role-based HMS.</p>
                </div>
            </div>
            <div class="brand-footer">
                © <?= date('Y') ?> HMS System · St. Peter Hospital
            </div>
        </div>
        <div class="auth-form-pane">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
</body>
</html>
