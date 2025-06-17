// Main JavaScript functionality for Insurance Platform

let profileData // Declare profileData variable

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the application
  initializeApp()
})

function initializeApp() {
  // Menu toggle functionality
  initializeMenuToggle()

  // User menu functionality
  initializeUserMenu()

  // Collapsible sections
  initializeCollapsibles()

  // Logout functionality
  initializeLogout()

  // Check user role and show/hide admin elements
  checkUserRole()

  // Set active navigation item
  setActiveNavItem()

  // Initialize profile (global - loads user data for header/navigation)
  initializeProfile()

  // Initialize page-specific functionality
  initializePageSpecific()
}

function initializeMenuToggle() {
  const menuToggle = document.getElementById("menu-toggle")
  const sidebar = document.getElementById("sidebar")
  const overlay = document.getElementById("overlay")
  const closeSidebar = document.getElementById("close-sidebar")

  if (menuToggle && sidebar && overlay) {
    menuToggle.addEventListener("click", () => {
      sidebar.classList.add("open")
      overlay.classList.add("active")
    })

    if (closeSidebar) {
      closeSidebar.addEventListener("click", () => {
        sidebar.classList.remove("open")
        overlay.classList.remove("active")
      })
    }

    overlay.addEventListener("click", () => {
      sidebar.classList.remove("open")
      overlay.classList.remove("active")
    })
  }
}

function initializeUserMenu() {
  // User menu is handled by CSS checkbox technique
  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    const userMenuContainer = document.querySelector(".user-menu-container")
    const userMenuToggle = document.getElementById("user-menu-toggle")

    if (userMenuContainer && !userMenuContainer.contains(e.target)) {
      if (userMenuToggle) {
        userMenuToggle.checked = false
      }
    }
  })
}

function initializeCollapsibles() {
  const collapsibles = document.querySelectorAll(".collapsible-header")
  collapsibles.forEach((collapsible) => {
    collapsible.addEventListener("click", function () {
      this.parentElement.classList.toggle("expanded")
    })
  })
}

function initializeLogout() {
  const logoutLinks = document.querySelectorAll(".logout-link, .logout-item")
  logoutLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault()
      handleLogout()
    })
  })
}

function handleLogout() {
  if (confirm("האם אתה בטוח שברצונך להתנתק?")) {
    // Call logout API
    fetch("api/Logout.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
    })
    .then(() => {
      // Clear any stored session data
      localStorage.removeItem("userSession")
      localStorage.removeItem("authToken")
      sessionStorage.clear()

      // Show logout message
      showNotification("התנתקת בהצלחה", "success")

      // Redirect to login page after a short delay
      setTimeout(() => {
        window.location.href = "login.php"
      }, 1500)
    })
    .catch((error) => {
      console.error("Logout error:", error)
      // Still redirect even if logout API fails
      window.location.href = "login.php"
    })
  }
}

function checkUserRole() {
  // Check user role from profileData if available
  if (profileData && profileData.role) {
    if (profileData.role === "manager" || profileData.role === "admin") {
      document.body.classList.add("admin")
    }
  } else {
    // Fallback to localStorage
    const userRole = localStorage.getItem("userRole") || "agent"
    if (userRole === "manager") {
      document.body.classList.add("admin")
    }
  }
}

function setActiveNavItem() {
  // Get current page name
  const currentPage = getCurrentPage()

  // Remove active class from all nav items
  const navItems = document.querySelectorAll(".nav-item")
  navItems.forEach((item) => {
    item.classList.remove("active")
  })

  // Map page names to their corresponding nav items
  const pageNavMap = {
    dashboard: "index.php",
    clients: "clients.php",
    activity: "activity.php",
    comparison: "comparison.php",
    management: "management.php",
    profile: "profile.php",
  }

  // Find and activate the current page's nav item
  const targetHref = pageNavMap[currentPage]
  if (targetHref) {
    const activeNavItem = document.querySelector(`a.nav-item[href="${targetHref}"]`)
    if (activeNavItem) {
      activeNavItem.classList.add("active")
    }
  }
}

function initializeProfile() {
  // Load global profile data for header/navigation
  loadGlobalProfileData()

  // If we're on the profile page, also initialize profile-specific functionality
  const currentPage = getCurrentPage()
  if (currentPage === "profile") {
    initializeProfilePage()
  }
}

function initializePageSpecific() {
  const currentPage = getCurrentPage()

  switch (currentPage) {
    case "dashboard":
      initializeDashboard()
      break
    case "clients":
      initializeClients()
      break
    case "activity":
      initializeActivity()
      break
    case "management":
      initializeManagement()
      break
    case "profile":
      // Profile initialization is already handled in initializeProfile()
      console.log("Profile page initialized")
      break
    case "comparison":
      initializeComparison()
      break
  }
}

function getCurrentPage() {
  const path = window.location.pathname
  const page = path.split("/").pop().split(".")[0]
  return page || "dashboard"
}

function initializeDashboard() {
  // Dashboard specific functionality
  console.log("Dashboard initialized")

  // Animate statistics on load
  animateStatistics()
}

function initializeClients() {
  // Clients page functionality is handled in the HTML file
  console.log("Clients page initialized")
}

function initializeActivity() {
  // Activity page functionality is handled in the HTML file
  console.log("Activity page initialized")
}

function initializeManagement() {
  // Management page functionality is handled in the HTML file
  console.log("Management page initialized")
}

function initializeComparison() {
  // Comparison page functionality is handled in the HTML file
  console.log("Comparison page initialized")
}

function animateStatistics() {
  const statNumbers = document.querySelectorAll(".stat-number")

  statNumbers.forEach((stat) => {
    const finalValue = Number.parseInt(stat.textContent.replace(/[^\d]/g, ""))
    if (finalValue) {
      animateNumber(stat, 0, finalValue, 2000)
    }
  })
}

function animateNumber(element, start, end, duration) {
  const startTime = performance.now()
  const originalText = element.textContent
  const isShekels = originalText.includes("₪")

  function updateNumber(currentTime) {
    const elapsed = currentTime - startTime
    const progress = Math.min(elapsed / duration, 1)

    const currentValue = Math.floor(start + (end - start) * progress)

    if (isShekels) {
      element.textContent = `₪${currentValue.toLocaleString("he-IL")}`
    } else {
      element.textContent = currentValue.toLocaleString("he-IL")
    }

    if (progress < 1) {
      requestAnimationFrame(updateNumber)
    }
  }

  requestAnimationFrame(updateNumber)
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

  // Add to page
  document.body.appendChild(notification)

  // Show notification
  setTimeout(() => {
    notification.classList.add("notification-visible")
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
  notification.classList.add("notification-hiding")
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

// Utility functions
function formatCurrency(amount) {
  return new Intl.NumberFormat("he-IL", {
    style: "currency",
    currency: "ILS",
  }).format(amount)
}

function formatDate(date) {
  return new Intl.DateTimeFormat("he-IL").format(new Date(date))
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

function validatePhone(phone) {
  const re = /^0\d{1,2}-?\d{7}$/
  return re.test(phone.replace(/\s/g, ""))
}

// Global profile data loading (for header/navigation)
async function loadGlobalProfileData() {
  try {
    const profileResponse = await fetchProfileInfo()
    profileData = profileResponse

    // Update global UI elements (header, navigation)
    updateGlobalUserInterface()

    // Update user role check
    checkUserRole()
  } catch (error) {
    console.error("Error loading global profile data:", error)

    // If it's an authentication error, don't show notification
    // as the user will be redirected to login
    if (error.message.includes("401") || error.message.includes("Unauthorized")) {
      return
    }

    // For other errors, show a subtle notification
    if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
      window.InsurancePlatform.showNotification("שגיאה בטעינת נתוני המשתמש", "warning")
    }
  }
}

// Profile page specific initialization
function initializeProfilePage() {
  // Initialize profile page specific event listeners
  initializeProfileEventListeners()

  // Load additional profile page data if needed
  loadProfilePageData()
}

function initializeProfileEventListeners() {
  // Profile form submission
  const profileForm = document.getElementById("profile-form")
  if (profileForm) {
    profileForm.addEventListener("submit", handleProfileSubmit)
  }

  // WhatsApp form submission
  const whatsappForm = document.getElementById("whatsapp-form")
  if (whatsappForm) {
    whatsappForm.addEventListener("submit", handleWhatsAppSubmit)
  }

  // Test message button
  const testMessageBtn = document.getElementById("test-message")
  if (testMessageBtn) {
    testMessageBtn.addEventListener("click", sendTestMessage)
  }
}

// Load profile page specific data
async function loadProfilePageData() {
  const loadingState = document.getElementById("loading-state")
  const errorState = document.getElementById("error-state")
  const profileContainer = document.getElementById("profile-container")

  if (loadingState) {
    showLoadingState()
  }

  try {
    // Ensure we have profile data first
    if (!profileData || !profileData.id) {
      console.log("Profile data not loaded, fetching...")
      profileData = await fetchProfileInfo()
    }

    // Load additional profile page data (WhatsApp messages, templates, stats)
    const [messagesResponse, templatesResponse, statsResponse] = await Promise.all([
      fetchWhatsAppMessages(),
      fetchMessageTemplates(),
      fetchUserStats(),
    ])

    // Display profile page specific data
    displayWhatsAppMessages(messagesResponse)
    displayMessageTemplates(templatesResponse)
    displayUserStats(statsResponse)

    // Display profile form data (now we're sure profileData exists)
    displayProfileData()

    // Update profile header
    updateProfileHeader()

    // Show profile container
    if (loadingState && errorState && profileContainer) {
      hideAllStates()
      profileContainer.style.display = "block"
    }
  } catch (error) {
    console.error("Error loading profile page data:", error)
    if (errorState) {
      showErrorState()
    }

    // Show notification
    if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
      window.InsurancePlatform.showNotification("שגיאה בטעינת נתוני הפרופיל", "error")
    }
  }
}

// Fetch profile information
async function fetchProfileInfo() {
  const response = await fetch("api/UserProfile.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include",
  })

  if (!response.ok) {
    if (response.status === 401) {
      // Unauthorized - redirect to login
      window.location.href = "login.php"
      return
    }
    throw new Error(`HTTP error! status: ${response.status}`)
  }

  const responseData = await response.json()

  // Check if the response indicates success
  if (!responseData.success) {
    throw new Error(responseData.error || "Failed to fetch profile data")
  }

  // Return the user data from the new API structure
  return responseData.user
}

// Fetch WhatsApp messages
async function fetchWhatsAppMessages() {
  try {
    const response = await fetch("api/WhatsAppMessages.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const responseData = await response.json()

    // Handle both old and new response formats
    if (responseData.success !== undefined) {
      return responseData.success ? responseData.data || {} : {}
    }

    return responseData || {}
  } catch (error) {
    console.error("Error fetching WhatsApp messages:", error)
    return {}
  }
}

// Fetch message templates
async function fetchMessageTemplates() {
  try {
    const response = await fetch("api/MessageTemplates.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const responseData = await response.json()

    // Handle both old and new response formats
    if (responseData.success !== undefined) {
      return responseData.success ? responseData.data || [] : []
    }

    return Array.isArray(responseData) ? responseData : []
  } catch (error) {
    console.error("Error fetching message templates:", error)
    return []
  }
}

// Fetch user statistics
async function fetchUserStats() {
  try {
    const response = await fetch("api/UserStats.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const responseData = await response.json()

    // Handle both old and new response formats
    if (responseData.success !== undefined) {
      return responseData.success ? responseData.data || {} : {}
    }

    return responseData || {}
  } catch (error) {
    console.error("Error fetching user stats:", error)
    return {}
  }
}

// Fetch active sessions
async function fetchActiveSessions() {
  try {
    const response = await fetch("api/GetSessions.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const responseData = await response.json()

    if (responseData.success) {
      return responseData.sessions || []
    }

    return []
  } catch (error) {
    console.error("Error fetching active sessions:", error)
    return []
  }
}

// Display profile form data (for profile page)
function displayProfileData() {
  // Safety check - ensure profileData exists
  if (!profileData) {
    console.warn("Profile data not available for display")
    return
  }

  const fullNameInput = document.getElementById("full-name")
  const emailInput = document.getElementById("email")
  const phoneInput = document.getElementById("phone")
  const roleInput = document.getElementById("role")
  const usernameInput = document.getElementById("username")
  const bioInput = document.getElementById("bio")
  const addressInput = document.getElementById("address")

  if (fullNameInput) fullNameInput.value = profileData.name || ""
  if (emailInput) emailInput.value = profileData.email || ""
  if (phoneInput) phoneInput.value = profileData.phone || ""
  if (roleInput) roleInput.value = getRoleText(profileData.role) || ""
  if (usernameInput) usernameInput.value = profileData.username || ""
  if (bioInput) bioInput.value = profileData.bio || ""
  if (addressInput) addressInput.value = profileData.address || ""

  // Update avatar display
  const avatarDisplay = document.getElementById("profile-avatar")
  if (avatarDisplay) {
    if (profileData.avatar) {
      avatarDisplay.innerHTML = `<img src="${profileData.avatar}" alt="Profile Avatar" class="avatar-image">`
    } else {
      const initials = getInitials(profileData.name)
      avatarDisplay.innerHTML = `<div class="avatar-initials">${initials}</div>`
    }
  }
}

// Update profile header with user information
function updateProfileHeader() {
  // Safety check - ensure profileData exists
  if (!profileData) {
    console.warn("Profile data not available for header update")
    return
  }

  // Update profile name
  const profileNameElement = document.getElementById("profile-name")
  if (profileNameElement) {
    profileNameElement.textContent = profileData.name || "משתמש"
  }

  // Update profile role
  const profileRoleElement = document.getElementById("profile-role")
  if (profileRoleElement) {
    profileRoleElement.textContent = getRoleText(profileData.role) || ""
  }

  // Update joined date
  const profileJoinedElement = document.getElementById("profile-joined-date")
  if (profileJoinedElement && profileData.created_at) {
    // Convert timestamp to Date object (multiply by 1000 if it's in seconds)
    const timestamp =
        typeof profileData.created_at === "number" || !isNaN(Number.parseInt(profileData.created_at))
            ? new Date(Number.parseInt(profileData.created_at) * 1000)
            : new Date(profileData.created_at)

    profileJoinedElement.textContent = timestamp.toLocaleDateString("he-IL")
  }
}

// Update global user interface elements (header, navigation)
function updateGlobalUserInterface() {
  // Safety check - ensure profileData exists
  if (!profileData) {
    console.warn("Profile data not available for UI update")
    return
  }

  // Update user avatar
  const userAvatars = document.querySelectorAll("#user-avatar, .dropdown-user-avatar")
  if (profileData.name) {
    const initials = getInitials(profileData.name)
    userAvatars.forEach((avatar) => {
      avatar.textContent = initials
    })
  }

  // Update user info in header dropdowns
  const userNameElements = document.querySelectorAll(".dropdown-user-name")
  const userEmailElements = document.querySelectorAll(".dropdown-user-email")
  const userRoleElements = document.querySelectorAll(".dropdown-user-role")

  userNameElements.forEach((el) => (el.textContent = profileData.name || "משתמש"))
  userEmailElements.forEach((el) => (el.textContent = profileData.email || ""))
  userRoleElements.forEach((el) => (el.textContent = getRoleText(profileData.role) || ""))
}

// Display WhatsApp messages
function displayWhatsAppMessages(messagesData) {
  const outboundInput = document.getElementById("outbound-message")
  const returnInput = document.getElementById("return-message")
  const followupInput = document.getElementById("followup-message")

  if (outboundInput) outboundInput.value = messagesData.outbound_message || getDefaultMessage("outbound")
  if (returnInput) returnInput.value = messagesData.return_message || getDefaultMessage("return")
  if (followupInput) followupInput.value = messagesData.followup_message || getDefaultMessage("followup")
}

// Display message templates
function displayMessageTemplates(templatesData) {
  const templatesGrid = document.getElementById("templates-grid")
  if (!templatesGrid) return

  templatesGrid.innerHTML = ""

  // Default templates if none from API
  const defaultTemplates = [
    {
      id: "car",
      title: "הצעת ביטוח רכב",
      content: "שלום! הכנתי עבורך השוואת מחירים לביטוח הרכב. ההצעה הטובה ביותר חוסכת לך ₪500 בשנה!",
    },
    {
      id: "home",
      title: "הצעת ביטוח דירה",
      content: "מצאתי עבורך ביטוח דירה מעולה במחיר תחרותי. הכיסוי כולל נזקי מים, גניבה ואש.",
    },
    {
      id: "travel",
      title: "הצעת ביטוח נסיעות",
      content: "ביטוח נסיעות מקיף לחופשה שלך! כיסוי רפואי, ביטול טיסות ואיחור מזוודות.",
    },
  ]

  const templates = templatesData && templatesData.length > 0 ? templatesData : defaultTemplates

  templates.forEach((template) => {
    const templateCard = document.createElement("div")
    templateCard.className = "template-card"
    templateCard.innerHTML = `
      <div class="template-header">
        <h4>${escapeHtml(template.title)}</h4>
        <button class="btn-icon" onclick="useTemplate('${template.id}', '${escapeHtml(template.content)}')">
          <i class="fas fa-copy"></i>
        </button>
      </div>
      <div class="template-content">
        ${escapeHtml(template.content)}
      </div>
    `
    templatesGrid.appendChild(templateCard)
  })
}

// Display user statistics
function displayUserStats(statsData) {
  const statsGrid = document.getElementById("stats-grid")
  if (!statsGrid) return

  statsGrid.innerHTML = ""

  // Default stats if none from API
  const defaultStats = [
    {
      icon: "fas fa-handshake",
      title: "עסקאות החודש",
      value: "0",
      change: "החודש הנוכחי",
    },
    {
      icon: "fas fa-users",
      title: "לקוחות חדשים",
      value: "0",
      change: "החודש הנוכחי",
    },
    {
      icon: "fas fa-star",
      title: "דירוג שביעות רצון",
      value: "5.0",
      change: "מתוך 5 כוכבים",
    },
  ]

  const stats =
      statsData && Object.keys(statsData).length > 0
          ? [
            {
              icon: "fas fa-handshake",
              title: "עסקאות החודש",
              value: statsData.deals_this_month || "0",
              change: statsData.deals_change || "החודש הנוכחי",
            },
            {
              icon: "fas fa-users",
              title: "לקוחות חדשים",
              value: statsData.new_clients || "0",
              change: statsData.clients_change || "החודש הנוכחי",
            },
            {
              icon: "fas fa-star",
              title: "דירוג שביעות רצון",
              value: statsData.satisfaction_rating || "5.0",
              change: "מתוך 5 כוכבים",
            },
          ]
          : defaultStats

  stats.forEach((stat) => {
    const statCard = document.createElement("div")
    statCard.className = "stat-card"
    statCard.innerHTML = `
      <div class="stat-icon">
        <i class="${stat.icon}"></i>
      </div>
      <div class="stat-content">
        <h3>${escapeHtml(stat.title)}</h3>
        <div class="stat-number">${escapeHtml(stat.value)}</div>
        <div class="stat-change positive">${escapeHtml(stat.change)}</div>
      </div>
    `
    statsGrid.appendChild(statCard)
  })
}

// Handle profile form submission
async function handleProfileSubmit(e) {
  e.preventDefault()

  const fullNameInput = document.getElementById("full-name")
  const phoneInput = document.getElementById("phone")
  const usernameInput = document.getElementById("username")
  const bioInput = document.getElementById("bio")
  const addressInput = document.getElementById("address")

  if (!fullNameInput || !phoneInput) return

  const formData = {
    name: fullNameInput.value.trim(),
    phone: phoneInput.value.trim(),
    username: usernameInput ? usernameInput.value.trim() : "",
    bio: bioInput ? bioInput.value.trim() : "",
    address: addressInput ? addressInput.value.trim() : "",
  }

  // Validate form
  if (!formData.name || !formData.phone) {
    showNotification("אנא מלא את כל השדות הנדרשים", "error")
    return
  }

  // Validate phone
  if (!validatePhone(formData.phone)) {
    showNotification("מספר הטלפון אינו תקין", "error")
    return
  }

  // Show loading state on button
  const submitBtn = document.getElementById("save-profile-btn")
  if (!submitBtn) return

  const originalText = submitBtn.textContent
  submitBtn.disabled = true
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...'

  try {
    await updateProfile(formData)

    // Update local data
    Object.assign(profileData, formData)

    // Update global UI
    updateGlobalUserInterface()

    // Update profile form
    displayProfileData()

    // Update profile header
    updateProfileHeader()

    // Show success message
    showNotification("הפרטים האישיים נשמרו בהצלחה!", "success")
  } catch (error) {
    console.error("Error updating profile:", error)
    showNotification("שגיאה בשמירת הפרטים", "error")
  } finally {
    // Reset button
    submitBtn.disabled = false
    submitBtn.textContent = originalText
  }
}

// Handle WhatsApp form submission
async function handleWhatsAppSubmit(e) {
  e.preventDefault()

  const outboundInput = document.getElementById("outbound-message")
  const returnInput = document.getElementById("return-message")
  const followupInput = document.getElementById("followup-message")

  if (!outboundInput || !returnInput || !followupInput) return

  const formData = {
    outbound_message: outboundInput.value.trim(),
    return_message: returnInput.value.trim(),
    followup_message: followupInput.value.trim(),
  }

  // Show loading state on button
  const submitBtn = document.getElementById("save-messages-btn")
  if (!submitBtn) return

  const originalText = submitBtn.textContent
  submitBtn.disabled = true
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...'

  try {
    await updateWhatsAppMessages(formData)

    // Update local data
    window.messagesData = { ...window.messagesData, ...formData }

    // Show success message
    showNotification("הודעות הווטסאפ נשמרו בהצלחה!", "success")
  } catch (error) {
    console.error("Error updating WhatsApp messages:", error)
    showNotification("שגיאה בשמירת ההודעות", "error")
  } finally {
    // Reset button
    submitBtn.disabled = false
    submitBtn.textContent = originalText
  }
}

// Update profile via API
async function updateProfile(profileData) {
  const response = await fetch("api/UpdateProfile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include",
    body: JSON.stringify(profileData),
  })

  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`)
  }

  const responseData = await response.json()

  if (!responseData.success) {
    throw new Error(responseData.message || "Failed to update profile")
  }

  return responseData
}

// Update WhatsApp messages via API
async function updateWhatsAppMessages(messagesData) {
  const response = await fetch("api/UpdateWhatsAppMessages.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include",
    body: JSON.stringify(messagesData),
  })

  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`)
  }

  const responseData = await response.json()

  if (!responseData.success) {
    throw new Error(responseData.message || "Failed to update WhatsApp messages")
  }

  return responseData
}

// Send test message
function sendTestMessage() {
  const testMessage = "זוהי הודעת בדיקה מהמערכת. ההודעות שלך פועלות כראוי!"
  const encodedMessage = encodeURIComponent(testMessage)
  window.open(`https://wa.me/?text=${encodedMessage}`, "_blank")
}

// Use template
function useTemplate(templateId, content) {
  // Copy to clipboard
  navigator.clipboard
  .writeText(content)
  .then(() => {
    showNotification("התבנית הועתקה ללוח!", "success")
  })
  .catch(() => {
    // Fallback for older browsers
    const textArea = document.createElement("textarea")
    textArea.value = content
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand("copy")
    document.body.removeChild(textArea)
    showNotification("התבנית הועתקה ללוח!", "success")
  })
}

// Show/hide states (for profile page)
function showLoadingState() {
  hideAllStates()
  const loadingState = document.getElementById("loading-state")
  if (loadingState) {
    loadingState.style.display = "flex"
  }
}

function showErrorState() {
  hideAllStates()
  const errorState = document.getElementById("error-state")
  if (errorState) {
    errorState.style.display = "flex"
  }
}

function hideAllStates() {
  const loadingState = document.getElementById("loading-state")
  const errorState = document.getElementById("error-state")
  const profileContainer = document.getElementById("profile-container")

  if (loadingState) loadingState.style.display = "none"
  if (errorState) errorState.style.display = "none"
  if (profileContainer) profileContainer.style.display = "none"
}

// Utility functions
function getRoleText(role) {
  const roleMap = {
    agent: "סוכן ביטוח",
    manager: "מנהל",
    admin: "מנהל מערכת",
  }
  return roleMap[role] || role || "משתמש"
}

function getInitials(name) {
  if (!name) return "א.כ"

  const words = name.trim().split(" ")
  if (words.length >= 2) {
    return words[0].charAt(0) + "." + words[1].charAt(0)
  } else {
    return words[0].charAt(0) + "."
  }
}

function getDefaultMessage(type) {
  const defaultMessages = {
    outbound: "שלום! אני בדרך אליך להצגת הצעות הביטוח. אגיע בעוד כ-15 דקות. תודה!",
    return: "תודה על הפגישה הנעימה! אשלח לך את סיכום ההצעות בהקדם. לשאלות נוספות אני זמין בטלפון.",
    followup: "שלום! רציתי לבדוק איתך לגבי הצעות הביטוח שהצגתי. האם יש שאלות נוספות? אשמח לעזור!",
  }
  return defaultMessages[type] || ""
}

function escapeHtml(text) {
  if (!text) return ""
  const div = document.createElement("div")
  div.textContent = text
  return div.innerHTML
}

// Global variables for profile data
window.profileData = {}
window.messagesData = {}

// Export functions for use in other files
window.InsurancePlatform = {
  showNotification,
  formatCurrency,
  formatDate,
  validateEmail,
  validatePhone,
  initializeProfilePage,
  loadGlobalProfileData,
  displayProfileData,
}
