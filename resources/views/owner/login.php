<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div style="width:72px;height:72px;margin:0 auto 12px;border-radius:50%;overflow:hidden;box-shadow:0 4px 14px rgba(46,125,50,0.18);border:2px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;">
                <img src="<?= asset('images/logo.png') ?>" alt="Healthy Bite Logo" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <h1 class="auth-title">Partner Sign In</h1>
            <p class="auth-subtitle">Healthy Bite Restaurant Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-banner error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert-banner success">
                <i class="bi bi-check-circle-fill"></i>
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('/owner/login') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>

            <div class="form-group" style="margin-bottom:16px;">
                <label for="email" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text-main);">Email Address</label>
                <input type="email" id="email" name="email" value="owner@greenhousekitchen.com" required 
                       class="form-control" style="width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:var(--radius-btn);font-size:14px;">
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label for="password" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text-main);">Password</label>
                <input type="password" id="password" name="password" value="Secret@123" required 
                       class="form-control" style="width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:var(--radius-btn);font-size:14px;">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-weight:700;font-size:15px;display:flex;align-items:center;justify-content:center;gap:8px;">
                <span>Sign In to Restaurant</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border-color);text-align:center;">
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">Demo Partner Credentials:</p>
            <div style="display:inline-block;background:#f8fafc;padding:8px 14px;border-radius:6px;font-size:12px;color:var(--text-main);font-family:monospace;border:1px solid #e2e8f0;">
                <strong>aarav@greenhouse.in</strong> (or <strong>owner@greenhousekitchen.com</strong>)<br>
                Password: <strong>Secret@123</strong>
            </div>
            <div style="margin-top:10px;">
                <button type="button" onclick="document.getElementById('email').value='aarav@greenhouse.in';document.getElementById('password').value='Secret@123';" 
                        style="background:#f1f5f9;border:1px solid #cbd5e1;padding:4px 10px;border-radius:4px;font-size:11px;cursor:pointer;color:#334155;font-weight:600;">
                    Fill Demo Credentials
                </button>
            </div>
            <div style="margin-top:16px;">
                <a href="<?= url('/admin/login') ?>" style="font-size:12px;color:var(--primary-green);text-decoration:none;font-weight:600;">Switch to Platform Admin Login &rarr;</a>
            </div>
        </div>
    </div>
</div>
