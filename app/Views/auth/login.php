<?php
ob_start();
?>
<div class="auth-container">
    <div class="auth-card glass-panel">
        <div class="auth-header">
            <h1 class="logo">IRB System</h1>
            <h2>تسجيل الدخول</h2>
            <p>منصة إدارة الموافقات البحثية بكلية الطب</p>
        </div>
        
        <form class="auth-form" action="/ITI/IRP_System_PHP/public/login" method="POST">
            <div class="input-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required placeholder="name@example.com">
            </div>
            
            <div class="input-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">دخول الأمان</button>
            </div>
            
            <div class="auth-footer">
                <p>ليس لديك حساب؟ <a href="/ITI/IRP_System_PHP/public/register">طالب جديد؟ سجل الآن</a></p>
            </div>
        </form>
    </div>
    
    <div class="auth-illustration portrait"></div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
