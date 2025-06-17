<?php
require_once 'classes/init.php';
require_once 'classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

include 'htmls/header.php';
?>

<div class="content-area">
    <div class="page-header">
        <h1>פרופיל משתמש</h1>
        <p>נהל את הפרטים האישיים והגדרות החשבון</p>
    </div>

    <!-- Loading State -->
    <div class="loading-state" id="loading-state" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
        </div>
        <p>טוען נתוני פרופיל...</p>
    </div>

    <!-- Error State -->
    <div class="error-state" id="error-state" style="display: none;">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>שגיאה בטעינת הנתונים</h3>
        <p>לא ניתן לטעון את נתוני הפרופיל. אנא נסה שוב.</p>
        <button class="btn btn-primary" onclick="loadProfileData()">נסה שוב</button>
    </div>

    <div class="profile-container" id="profile-container" style="display: none;">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-container">
                <div class="profile-avatar" id="profile-avatar">
                    <!-- Will be filled by JS -->
                </div>
                <button class="avatar-upload-btn" id="avatar-upload-btn">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <div class="profile-header-info">
                <h2 id="profile-name"><!-- Will be filled by JS --></h2>
                <p class="profile-role" id="profile-role"><!-- Will be filled by JS --></p>
                <p class="profile-joined">חבר מאז <span id="profile-joined-date"><!-- Will be filled by JS --></span></p>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="profile-section">
            <div class="section-header">
                <h2>פרטים אישיים</h2>
                <p>עדכן את פרטי החשבון שלך</p>
            </div>

            <form id="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">שם מלא</label>
                        <input type="text" class="form-input" id="full-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">שם משתמש</label>
                        <input type="text" class="form-input" id="username" name="username" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">אימייל</label>
                        <input type="email" class="form-input" id="email" name="email" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">טלפון</label>
                        <input type="tel" class="form-input" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">כתובת</label>
                    <input type="text" class="form-input" id="address" name="address">
                </div>

                <div class="form-group">
                    <label class="form-label">אודות</label>
                    <textarea class="form-textarea" id="bio" name="bio" rows="3" placeholder="ספר קצת על עצמך..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="save-profile-btn">שמור שינויים</button>
                </div>
            </form>
        </div>

        <!-- Account Settings -->
        <div class="profile-section">
            <div class="section-header">
                <h2>הגדרות חשבון</h2>
                <p>עדכן את הגדרות החשבון והאבטחה שלך</p>
            </div>

            <form id="password-form">
                <div class="form-group">
                    <label class="form-label">סיסמה נוכחית</label>
                    <div class="password-input-container">
                        <input type="password" class="form-input" id="current-password" name="current_password">
                        <button type="button" class="password-toggle-btn">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">סיסמה חדשה</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="new-password" name="new_password">
                            <button type="button" class="password-toggle-btn">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">אימות סיסמה</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="confirm-password" name="confirm_password">
                            <button type="button" class="password-toggle-btn">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="password-strength" id="password-strength">
                    <div class="strength-meter">
                        <div class="strength-meter-fill" style="width: 0%"></div>
                    </div>
                    <div class="strength-text">חוזק הסיסמה: <span>חלשה</span></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="save-password-btn">עדכן סיסמה</button>
                </div>
            </form>
        </div>

        <!-- WhatsApp Messages -->
        <div class="profile-section">
            <div class="section-header">
                <h2>הודעות ווטסאפ</h2>
                <p>הגדר הודעות אוטומטיות שיישלחו ללקוחות</p>
            </div>

            <form id="whatsapp-form">
                <div class="form-group">
                    <label class="form-label">הודעת נסיעה - הלוך</label>
                    <textarea class="form-textarea" id="outbound-message" name="outbound_message" rows="4"
                              placeholder="הכנס הודעה שתישלח ללקוח בתחילת הנסיעה..."></textarea>
                    <div class="form-hint">הודעה זו תישלח אוטומטית ללקוח לפני הגעתך</div>
                </div>

                <div class="form-group">
                    <label class="form-label">הודעת נסיעה - חזור</label>
                    <textarea class="form-textarea" id="return-message" name="return_message" rows="4"
                              placeholder="הכנס הודעה שתישלח ללקוח בסיום הפגישה..."></textarea>
                    <div class="form-hint">הודעה זו תישלח אוטומטית ללקוח לאחר סיום הפגישה</div>
                </div>

                <div class="form-group">
                    <label class="form-label">הודעת מעקב</label>
                    <textarea class="form-textarea" id="followup-message" name="followup_message" rows="4"
                              placeholder="הכנס הודעת מעקב..."></textarea>
                    <div class="form-hint">הודעה זו תישלח כמעקב לאחר מספר ימים</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="save-messages-btn">שמור הודעות</button>
                    <button type="button" class="btn btn-outline" id="test-message">שלח הודעת בדיקה</button>
                </div>
            </form>
        </div>

        <!-- Message Templates -->
        <div class="profile-section">
            <div class="section-header">
                <h2>תבניות הודעות</h2>
                <p>תבניות מוכנות לשימוש מהיר</p>
            </div>

            <div class="templates-grid" id="templates-grid">
                <!-- Templates will be loaded here -->
            </div>
        </div>

        <!-- Statistics -->
        <div class="profile-section">
            <div class="section-header">
                <h2>סטטיסטיקות אישיות</h2>
            </div>

            <div class="stats-grid" id="stats-grid">
                <!-- Stats will be loaded here -->
            </div>
        </div>

        <!-- Sessions -->
        <div class="profile-section">
            <div class="section-header">
                <h2>התחברויות פעילות</h2>
                <p>מכשירים ודפדפנים שמחוברים לחשבון שלך</p>
            </div>

            <div class="sessions-list" id="sessions-list">
                <!-- Sessions will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof initializeProfilePage === 'function') {
      initializeProfilePage();
    }

    // Password toggle functionality
    document.querySelectorAll('.password-toggle-btn').forEach(button => {
      button.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const icon = this.querySelector('i');

        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });
    });

    // Password strength meter
    const newPasswordInput = document.getElementById('new-password');
    if (newPasswordInput) {
      newPasswordInput.addEventListener('input', function() {
        updatePasswordStrength(this.value);
      });
    }

    // Avatar upload button
    const avatarUploadBtn = document.getElementById('avatar-upload-btn');
    if (avatarUploadBtn) {
      avatarUploadBtn.addEventListener('click', function() {
        // Create a hidden file input
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';

        // Trigger click on the file input
        document.body.appendChild(fileInput);
        fileInput.click();

        // Handle file selection
        fileInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            uploadAvatar(this.files[0]);
          }
          document.body.removeChild(fileInput);
        });
      });
    }
  });

  function updatePasswordStrength(password) {
    const strengthMeter = document.querySelector('.strength-meter-fill');
    const strengthText = document.querySelector('.strength-text span');

    if (!strengthMeter || !strengthText) return;

    // Calculate password strength
    let strength = 0;

    // Length check
    if (password.length >= 8) strength += 25;

    // Contains lowercase
    if (/[a-z]/.test(password)) strength += 25;

    // Contains uppercase
    if (/[A-Z]/.test(password)) strength += 25;

    // Contains number or special char
    if (/[0-9!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 25;

    // Update UI
    strengthMeter.style.width = strength + '%';

    // Set color based on strength
    if (strength < 25) {
      strengthMeter.style.backgroundColor = '#ef4444';
      strengthText.textContent = 'חלשה מאוד';
    } else if (strength < 50) {
      strengthMeter.style.backgroundColor = '#f59e0b';
      strengthText.textContent = 'חלשה';
    } else if (strength < 75) {
      strengthMeter.style.backgroundColor = '#10b981';
      strengthText.textContent = 'בינונית';
    } else {
      strengthMeter.style.backgroundColor = '#059669';
      strengthText.textContent = 'חזקה';
    }
  }

  function uploadAvatar(file) {
    // Create form data
    const formData = new FormData();
    formData.append('avatar', file);

    // Show loading state
    const avatarElement = document.getElementById('profile-avatar');
    if (avatarElement) {
      avatarElement.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i>';
    }

    // Upload avatar
    fetch('api/UpdateAvatar.php', {
      method: 'POST',
      body: formData,
    }).then(response => response.json()).then(data => {
      if (data.success) {
        // Update avatar in UI
        loadProfileData();
        showNotification('success', 'תמונת הפרופיל עודכנה בהצלחה');
      } else {
        showNotification('error', data.message || 'שגיאה בהעלאת התמונה');
      }
    }).catch(error => {
      console.error('Error uploading avatar:', error);
      showNotification('error', 'שגיאה בהעלאת התמונה');
    });
  }
</script>

<style>
    /* Additional styles for the profile page */
    .loading-state,
    .error-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
        min-height: 400px;
    }

    .loading-spinner {
        font-size: 3rem;
        color: rgb(255, 23, 68);
        margin-bottom: 1rem;
    }

    .error-icon {
        font-size: 4rem;
        color: #9ca3af;
        margin-bottom: 1.5rem;
    }

    .error-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #f9fafb;
    }

    .error-state p {
        color: #9ca3af;
        margin-bottom: 2rem;
        max-width: 400px;
    }

    .profile-container {
        width: 100%;
    }

    /* Profile header */
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background-color: #1f2937;
        border-radius: 0.75rem;
        border: 1px solid #374151;
        width: 100%;
    }

    .profile-avatar-container {
        position: relative;
        margin-left: 1.5rem;
    }

    .profile-avatar {
        width: 6rem;
        height: 6rem;
        border-radius: 50%;
        background-color: #2d3748;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 600;
        color: #f9fafb;
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background-color: rgb(255, 23, 68);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #1f2937;
        cursor: pointer;
        transition: all 0.2s;
    }

    .avatar-upload-btn:hover {
        background-color: rgb(204, 18, 54);
    }

    .profile-header-info h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .profile-role {
        color: rgb(255, 23, 68);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .profile-joined {
        color: #9ca3af;
        font-size: 0.75rem;
    }

    .profile-section {
        background-color: #1f2937;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #374151;
        width: 100%;
    }

    .profile-section .section-header {
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #374151;
        padding-bottom: 1rem;
    }

    .profile-section .section-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .profile-section .section-header p {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .template-card {
        background-color: #2d3748;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #374151;
        transition: all 0.2s ease;
    }

    .template-card:hover {
        border-color: rgb(255, 23, 68);
        transform: translateY(-2px);
    }

    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .template-header h4 {
        font-weight: 600;
        font-size: 1rem;
    }

    .template-content {
        color: #9ca3af;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background-color: #2d3748;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #374151;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: rgb(255, 23, 68);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        background-color: rgba(255, 23, 68, 0.1);
        color: rgb(255, 23, 68);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .stat-content h3 {
        font-size: 0.875rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-change {
        font-size: 0.75rem;
    }

    .stat-change.positive {
        color: #10b981;
    }

    .stat-change.negative {
        color: #ef4444;
    }

    /* Password strength meter */
    .password-strength {
        margin-top: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .strength-meter {
        height: 4px;
        background-color: #374151;
        border-radius: 2px;
        margin-bottom: 0.5rem;
        overflow: hidden;
    }

    .strength-meter-fill {
        height: 100%;
        background-color: #10b981;
        border-radius: 2px;
        transition: width 0.3s, background-color 0.3s;
    }

    .strength-text {
        font-size: 0.75rem;
        color: #9ca3af;
        text-align: left;
    }

    /* Password input container */
    .password-input-container {
        position: relative;
        display: flex;
    }

    .password-input-container .form-input {
        flex: 1;
        padding-left: 2.5rem;
    }

    .password-toggle-btn {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.25rem;
        transition: color 0.2s;
    }

    .password-toggle-btn:hover {
        color: #f9fafb;
    }

    /* Sessions list */
    .sessions-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .session-item {
        display: flex;
        align-items: center;
        background-color: #2d3748;
        border-radius: 0.5rem;
        padding: 1rem;
        border: 1px solid #374151;
    }

    .session-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        background-color: rgba(255, 23, 68, 0.1);
        color: rgb(255, 23, 68);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .session-info {
        flex: 1;
    }

    .session-device {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .session-details {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .session-current {
        background-color: rgba(255, 23, 68, 0.05);
        border-color: rgba(255, 23, 68, 0.3);
    }

    .session-current .session-device::after {
        content: 'התחברות נוכחית';
        font-size: 0.75rem;
        font-weight: normal;
        color: rgb(255, 23, 68);
        margin-right: 0.5rem;
        padding: 0.125rem 0.375rem;
        background-color: rgba(255, 23, 68, 0.1);
        border-radius: 1rem;
    }

    .session-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Form improvements */
    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #f9fafb;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: #111827;
        border: 1px solid #374151;
        border-radius: 0.5rem;
        color: #f9fafb;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: rgb(255, 23, 68);
        box-shadow: 0 0 0 3px rgba(255, 23, 68, 0.2);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
    }

    .form-input[readonly] {
        background-color: #2d3748;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-hint {
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background-color: rgb(255, 23, 68);
        color: white;
    }

    .btn-primary:hover {
        background-color: rgb(204, 18, 54);
    }

    .btn-primary:disabled {
        background-color: #6b7280;
        cursor: not-allowed;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid #374151;
        color: #f9fafb;
    }

    .btn-outline:hover {
        background-color: #2d3748;
    }

    .btn-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        border: 1px solid #374151;
        background-color: #2d3748;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        background-color: #374151;
        color: #f9fafb;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .form-row .form-group {
            margin-bottom: 1.5rem;
        }

        .templates-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .profile-section {
            padding: 1.5rem;
        }

        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-avatar-container {
            margin-left: 0;
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 480px) {
        .profile-section {
            padding: 1rem;
        }

        .loading-state,
        .error-state {
            padding: 2rem 1rem;
            min-height: 300px;
        }

        .error-icon {
            font-size: 3rem;
        }

        .session-details {
            flex-direction: column;
            gap: 0.25rem;
        }
    }

    /* Loading animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Notification Styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 500px;
        background-color: #1f2937;
        border: 1px solid #374151;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        z-index: 9999;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        padding: 1rem;
        gap: 0.75rem;
    }

    .notification.notification-visible {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.notification-hiding {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-success {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.1);
    }

    .notification-error {
        border-color: #ef4444;
        background-color: rgba(239, 68, 68, 0.1);
    }

    .notification-warning {
        border-color: #f59e0b;
        background-color: rgba(245, 158, 11, 0.1);
    }

    .notification-info {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
    }

    .notification-icon {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .notification-success .notification-icon {
        background-color: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .notification-error .notification-icon {
        background-color: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .notification-warning .notification-icon {
        background-color: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .notification-info .notification-icon {
        background-color: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }

    .notification-content {
        flex: 1;
    }

    .notification-content p {
        margin: 0;
        color: #f9fafb;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .notification-close {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .notification-close:hover {
        background-color: rgba(156, 163, 175, 0.1);
        color: #f9fafb;
    }

    /* Responsive notifications */
    @media (max-width: 640px) {
        .notification {
            top: 10px;
            right: 10px;
            left: 10px;
            min-width: auto;
            max-width: none;
            transform: translateY(-100%);
        }

        .notification.notification-visible {
            transform: translateY(0);
        }

        .notification.notification-hiding {
            transform: translateY(-100%);
        }
    }
</style>

<?php
include 'htmls/footer.php';
?>
