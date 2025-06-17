// Login Page JavaScript

document.addEventListener("DOMContentLoaded", () => {
  initializeLogin()
})

function initializeLogin() {
  // Initialize form handlers
  initializeLoginForm()
  initializePasswordToggle()
  initializeDemoCredentials()
  initializeForgotPassword()
  initializeAlternativeLogin()

  // Focus on email input
  const emailInput = document.getElementById("email")
  if (emailInput) {
    emailInput.focus()
  }
}

function initializeLoginForm() {
  const loginForm = document.getElementById("login-form")
  if (!loginForm) return

  loginForm.addEventListener("submit", handleLogin)

  // Real-time validation
  const emailInput = document.getElementById("email")
  const passwordInput = document.getElementById("password")

  if (emailInput) {
    emailInput.addEventListener("blur", validateEmail)
    emailInput.addEventListener("input", clearError)
  }

  if (passwordInput) {
    passwordInput.addEventListener("blur", validatePassword)
    passwordInput.addEventListener("input", clearError)
  }
}

function initializePasswordToggle() {
  const passwordToggle = document.getElementById("password-toggle")
  const passwordInput = document.getElementById("password")

  if (!passwordToggle || !passwordInput) return

  passwordToggle.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password"
    passwordInput.type = isPassword ? "text" : "password"

    const icon = passwordToggle.querySelector("i")
    icon.className = isPassword ? "fas fa-eye-slash" : "fas fa-eye"
  })
}

function initializeDemoCredentials() {
  const demoButtons = document.querySelectorAll(".use-demo")

  demoButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const email = button.dataset.email
      const password = button.dataset.password

      const emailInput = document.getElementById("email")
      const passwordInput = document.getElementById("password")

      if (emailInput && passwordInput) {
        emailInput.value = email
        passwordInput.value = password

        // Clear any existing errors
        clearAllErrors()

        // Add visual feedback
        button.innerHTML = '<i class="fas fa-check"></i> הועתק'
        button.style.background = "#10b981"

        setTimeout(() => {
          button.innerHTML = "השתמש"
          button.style.background = ""
        }, 1500)
      }
    })
  })
}

function initializeForgotPassword() {
  const forgotPasswordLink = document.getElementById("forgot-password")
  const forgotPasswordModal = document.getElementById("forgot-password-modal")
  const closeForgotModal = document.getElementById("close-forgot-modal")
  const cancelReset = document.getElementById("cancel-reset")
  const forgotPasswordForm = document.getElementById("forgot-password-form")

  if (forgotPasswordLink && forgotPasswordModal) {
    forgotPasswordLink.addEventListener("click", (e) => {
      e.preventDefault()
      forgotPasswordModal.classList.add("active")
    })
  }

  if (closeForgotModal && forgotPasswordModal) {
    closeForgotModal.addEventListener("click", () => {
      forgotPasswordModal.classList.remove("active")
    })
  }

  if (cancelReset && forgotPasswordModal) {
    cancelReset.addEventListener("click", () => {
      forgotPasswordModal.classList.remove("active")
    })
  }

  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", handleForgotPassword)
  }

  // Close modal when clicking outside
  if (forgotPasswordModal) {
    forgotPasswordModal.addEventListener("click", (e) => {
      if (e.target === forgotPasswordModal) {
        forgotPasswordModal.classList.remove("active")
      }
    })
  }
}

function initializeAlternativeLogin() {
  const googleLogin = document.querySelector(".google-login")
  const microsoftLogin = document.querySelector(".microsoft-login")

  if (googleLogin) {
    googleLogin.addEventListener("click", () => {
      showNotification("התחברות עם Google תהיה זמינה בקרוב", "info")
    })
  }

  if (microsoftLogin) {
    microsoftLogin.addEventListener("click", () => {
      showNotification("התחברות עם Microsoft תהיה זמינה בקרוב", "info")
    })
  }
}

async function handleLogin(e) {
  e.preventDefault()

  const emailInput = document.getElementById("email")
  const passwordInput = document.getElementById("password")
  const rememberCheckbox = document.getElementById("remember-me")
  const loginButton = document.getElementById("login-button")

  if (!emailInput || !passwordInput || !loginButton) return

  const email = emailInput.value.trim()
  const password = passwordInput.value.trim()
  const remember = rememberCheckbox ? rememberCheckbox.checked : false

  // Clear previous errors
  clearAllErrors()

  // Validate inputs
  let hasErrors = false

  if (!email) {
    showFieldError("email", "אנא הזן כתובת אימייל")
    hasErrors = true
  } else if (!isValidEmail(email)) {
    showFieldError("email", "כתובת האימייל אינה תקינה")
    hasErrors = true
  }

  if (!password) {
    showFieldError("password", "אנא הזן סיסמה")
    hasErrors = true
  } else if (password.length < 6) {
    showFieldError("password", "הסיסמה חייבת להכיל לפחות 6 תווים")
    hasErrors = true
  }

  if (hasErrors) return

  // Show loading state
  setLoginButtonLoading(true)

  try {
    // Perform real login
    const loginData = await performLogin(email, password, remember)

    if (loginData.success) {
      // Store user data in sessionStorage/localStorage
      const storage = remember ? localStorage : sessionStorage
      storage.setItem("authToken", loginData.session.token)
      storage.setItem("userData", JSON.stringify(loginData.user))

      // Show success message
      showNotification(`ברוך הבא ${loginData.user.name}! מעביר לדשבורד...`, "success")

      // Redirect to dashboard
      setTimeout(() => {
        window.location.href = "index.php"
      }, 1500)
    } else {
      throw new Error(loginData.message || "Login failed")
    }
  } catch (error) {
    console.error("Login error:", error)

    // Handle specific error cases
    if (error.message.includes("locked")) {
      showNotification("החשבון נחסם זמנית עקב ניסיונות התחברות כושלים. נסה שוב מאוחר יותר.", "error")
    } else if (error.message.includes("Invalid email or password")) {
      showNotification("אימייל או סיסמה שגויים. אנא נסה שוב.", "error")
    } else {
      showNotification(error.message || "שגיאה בהתחברות. אנא נסה שוב.", "error")
    }
  } finally {
    setLoginButtonLoading(false)
  }
}

async function performLogin(email, password, remember) {
  try {
    const response = await fetch("api/Login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email,
        password: password,
        remember: remember,
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.error || "Login failed")
    }

    return data
  } catch (error) {
    console.error("Login API error:", error)
    throw error
  }
}

async function handleForgotPassword(e) {
  e.preventDefault()

  const resetEmailInput = document.getElementById("reset-email")
  const sendResetButton = document.getElementById("send-reset")

  if (!resetEmailInput || !sendResetButton) return

  const email = resetEmailInput.value.trim()

  if (!email) {
    showNotification("אנא הזן כתובת אימייל", "error")
    return
  }

  if (!isValidEmail(email)) {
    showNotification("כתובת האימייל אינה תקינה", "error")
    return
  }

  // Show loading state
  const originalText = sendResetButton.textContent
  sendResetButton.disabled = true
  sendResetButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שולח...'

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 2000))

    showNotification("קישור לאיפוס סיסמה נשלח לאימייל שלך", "success")

    // Close modal
    const modal = document.getElementById("forgot-password-modal")
    if (modal) {
      modal.classList.remove("active")
    }

    // Reset form
    resetEmailInput.value = ""
  } catch (error) {
    showNotification("שגיאה בשליחת האימייל. אנא נסה שוב.", "error")
  } finally {
    sendResetButton.disabled = false
    sendResetButton.textContent = originalText
  }
}

function validateEmail() {
  const emailInput = document.getElementById("email")
  if (!emailInput) return

  const email = emailInput.value.trim()

  if (email && !isValidEmail(email)) {
    showFieldError("email", "כתובת האימייל אינה תקינה")
    return false
  }

  clearFieldError("email")
  return true
}

function validatePassword() {
  const passwordInput = document.getElementById("password")
  if (!passwordInput) return

  const password = passwordInput.value.trim()

  if (password && password.length < 6) {
    showFieldError("password", "הסיסמה חייבת להכיל לפחות 6 תווים")
    return false
  }

  clearFieldError("password")
  return true
}

function clearError(e) {
  const fieldName = e.target.name || e.target.id
  clearFieldError(fieldName)
}

function showFieldError(fieldName, message) {
  const errorElement = document.getElementById(`${fieldName}-error`)
  const inputElement = document.getElementById(fieldName)

  if (errorElement) {
    errorElement.textContent = message
  }

  if (inputElement) {
    inputElement.style.borderColor = "#ef4444"
  }
}

function clearFieldError(fieldName) {
  const errorElement = document.getElementById(`${fieldName}-error`)
  const inputElement = document.getElementById(fieldName)

  if (errorElement) {
    errorElement.textContent = ""
  }

  if (inputElement) {
    inputElement.style.borderColor = ""
  }
}

function clearAllErrors() {
  const errorElements = document.querySelectorAll(".input-error")
  const inputElements = document.querySelectorAll(".form-input")

  errorElements.forEach((el) => (el.textContent = ""))
  inputElements.forEach((el) => (el.style.borderColor = ""))
}

function setLoginButtonLoading(loading) {
  const loginButton = document.getElementById("login-button")
  const buttonText = loginButton.querySelector(".button-text")
  const buttonSpinner = loginButton.querySelector(".button-spinner")

  if (loading) {
    loginButton.disabled = true
    buttonText.style.display = "none"
    buttonSpinner.style.display = "inline-block"
  } else {
    loginButton.disabled = false
    buttonText.style.display = "inline-block"
    buttonSpinner.style.display = "none"
  }
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas ${getNotificationIcon(type)}"></i>
        </div>
        <div class="notification-content">
            <p>${message}</p>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `

  // Add notification styles
  notification.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 0.5rem;
        padding: 1rem;
        color: #f9fafb;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 400px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `

  // Add to page
  document.body.appendChild(notification)

  // Show notification
  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  // Auto hide after 5 seconds
  setTimeout(() => {
    hideNotification(notification)
  }, 5000)

  // Close button functionality
  const closeBtn = notification.querySelector(".notification-close")
  closeBtn.addEventListener("click", () => {
    hideNotification(notification)
  })
}

function hideNotification(notification) {
  notification.style.transform = "translateX(100%)"
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification)
    }
  }, 300)
}

function getNotificationIcon(type) {
  switch (type) {
    case "success":
      return "fa-check-circle"
    case "error":
      return "fa-exclamation-circle"
    case "warning":
      return "fa-exclamation-triangle"
    default:
      return "fa-info-circle"
  }
}

// Handle contact admin link
document.addEventListener("click", (e) => {
  if (e.target.id === "contact-admin") {
    e.preventDefault()
    showNotification("לקבלת חשבון חדש, אנא פנה למנהל המערכת בטלפון: 03-1234567", "info")
  }

  if (e.target.id === "privacy-policy") {
    e.preventDefault()
    showNotification("מדיניות הפרטיות תהיה זמינה בקרוב", "info")
  }

  if (e.target.id === "terms-service") {
    e.preventDefault()
    showNotification("תנאי השימוש יהיו זמינים בקרוב", "info")
  }

  if (e.target.id === "support") {
    e.preventDefault()
    showNotification("לתמיכה טכנית: support@insurance.co.il או 03-1234567", "info")
  }
})
