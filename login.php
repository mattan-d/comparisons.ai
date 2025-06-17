<!DOCTYPE html>
<html lang="he" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>התחברות - ביטוח פלוס</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page">
<div class="login-container">
    <!-- Background Elements -->
    <div class="login-background">
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        <div class="bg-shape shape-3"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <!-- Logo Section -->
        <div class="login-header">
            <div class="logo-container">
                <i class="fas fa-shield-alt logo-icon"></i>
                <div class="logo-text">
                    <span class="logo-stream">ביטוח פלוס</span>
                    <span class="logo-university">פלטפורמת השוואות</span>
                </div>
            </div>
            <h1>התחברות למערכת</h1>
            <p>הזן את פרטי ההתחברות שלך כדי לגשת לחשבון</p>
        </div>

        <!-- Login Form -->
        <form class="login-form" id="login-form">
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    כתובת אימייל
                </label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="הזן את כתובת האימייל שלך"
                        required
                        autocomplete="email"
                >
                <div class="input-error" id="email-error"></div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    סיסמה
                </label>
                <div class="password-input-container">
                    <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="הזן את הסיסמה שלך"
                            required
                            autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" id="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="input-error" id="password-error"></div>
            </div>

            <div class="form-options">
                <label class="checkbox-container">
                    <input type="checkbox" id="remember-me" name="remember">
                    <span class="checkmark"></span>
                    זכור אותי
                </label>
                <a href="#" class="forgot-password" id="forgot-password">שכחת סיסמה?</a>
            </div>

            <button type="submit" class="login-button" id="login-button">
                <span class="button-text">התחבר</span>
                <i class="fas fa-spinner fa-spin button-spinner" style="display: none;"></i>
            </button>

            <div class="form-divider">
                <span>או</span>
            </div>

            <div class="alternative-login">
                <button type="button" class="alt-login-button google-login">
                    <i class="fab fa-google"></i>
                    התחבר עם Google
                </button>
                <button type="button" class="alt-login-button microsoft-login">
                    <i class="fab fa-microsoft"></i>
                    התחבר עם Microsoft
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="login-footer">
            <p>אין לך חשבון? <a href="#" id="contact-admin">צור קשר עם המנהל</a></p>
            <div class="login-links">
                <a href="#" id="privacy-policy">מדיניות פרטיות</a>
                <span>•</span>
                <a href="#" id="terms-service">תנאי שימוש</a>
                <span>•</span>
                <a href="#" id="support">תמיכה</a>
            </div>
        </div>
    </div>

    <!-- Demo Credentials -->
    <div class="demo-credentials">
        <h3><i class="fas fa-info-circle"></i> פרטי התחברות לדמו</h3>
        <div class="demo-users">
            <div class="demo-user">
                <strong>מנהל:</strong>
                <span>admin@insurance.co.il</span>
                <span>admin123</span>
                <button class="use-demo" data-email="admin@insurance.co.il" data-password="admin123">השתמש</button>
            </div>
            <div class="demo-user">
                <strong>סוכן:</strong>
                <span>agent@insurance.co.il</span>
                <span>agent123</span>
                <button class="use-demo" data-email="agent@insurance.co.il" data-password="agent123">השתמש</button>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal" id="forgot-password-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>איפוס סיסמה</h3>
            <button class="modal-close" id="close-forgot-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>הזן את כתובת האימייל שלך ונשלח לך קישור לאיפוס הסיסמה</p>
            <form id="forgot-password-form">
                <div class="form-group">
                    <label for="reset-email" class="form-label">כתובת אימייל</label>
                    <input
                            type="email"
                            id="reset-email"
                            class="form-input"
                            placeholder="הזן את כתובת האימייל שלך"
                            required
                    >
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancel-reset">ביטול</button>
                    <button type="submit" class="btn btn-primary" id="send-reset">שלח קישור</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/login.js"></script>
</body>
</html>
