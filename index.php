<?php
require_once 'classes/init.php';
require_once 'classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

include 'htmls/header.php';
?>

<div class="content-area">
    <!-- Loading State -->
    <div class="loading-state" id="loading-state" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
        </div>
        <p>טוען נתוני דשבורד...</p>
    </div>

    <!-- Error State -->
    <div class="error-state" id="error-state" style="display: none;">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>שגיאה בטעינת הנתונים</h3>
        <p>לא ניתן לטעון את נתוני הדשבורד. אנא נסה שוב.</p>
        <button class="btn btn-primary" onclick="loadDashboardData()">נסה שוב</button>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content" id="dashboard-content" style="display: none;">
        <div class="dashboard-header">
            <h1>דשבורד</h1>
            <p id="welcome-message"></p>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="comparison.php" class="action-card primary">
                <div class="action-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="action-content">
                    <h3>השוואה חדשה</h3>
                    <p>צור השוואת פוליסות חדשה</p>
                </div>
            </a>

            <a href="clients.php?action=new" class="action-card secondary">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="action-content">
                    <h3>לקוח חדש</h3>
                    <p>הוסף לקוח חדש למערכת</p>
                </div>
            </a>
        </div>

        <!-- Statistics -->
        <div class="stats-grid" id="stats-grid">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>הצעות אחרונות</h2>
                <a href="activity.php" class="view-all">
                    צפה בהכל
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <div class="proposals-list" id="proposals-list">
                <!-- Recent proposals will be loaded here -->
            </div>
        </div>

        <!-- Recent Clients -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>לקוחות אחרונים</h2>
                <a href="clients.php" class="view-all">
                    צפה בהכל
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <div class="clients-grid" id="clients-grid">
                <!-- Recent clients will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
  // Global variables
  let dashboardData = {};
  let userProfile = {};

  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
  });

  function initializeDashboard() {
    // Load dashboard data
    loadDashboardData();
  }

  // Load all dashboard data from APIs
  async function loadDashboardData() {
    showLoadingState();

    try {
      // Load all data in parallel
      const [profileResponse, statsResponse, proposalsResponse, clientsResponse] = await Promise.all([
        fetchUserProfile(),
        fetchDashboardStats(),
        fetchRecentProposals(),
        fetchRecentClients(),
      ]);

      userProfile = profileResponse;
      dashboardData = {
        stats: statsResponse,
        proposals: proposalsResponse,
        clients: clientsResponse,
      };

      // Display all data
      displayUserProfile();
      displayDashboardStats();
      displayRecentProposals();
      displayRecentClients();

      // Show dashboard content
      hideAllStates();
      document.getElementById('dashboard-content').style.display = 'block';

      // Animate statistics
      setTimeout(() => {
        animateStatistics();
      }, 300);

    } catch (error) {
      console.error('Error loading dashboard data:', error);
      showErrorState();

      // Show notification
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בטעינת נתוני הדשבורד', 'error');
      }
    }
  }

  // Fetch user profile
  async function fetchUserProfile() {
    const response = await fetch('https://dev.comparisons/api/UserProfile.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    const decodedText = responseText.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

    return JSON.parse(decodedText);
  }

  // Fetch dashboard statistics
  async function fetchDashboardStats() {
    const response = await fetch('https://dev.comparisons/api/DashboardStats.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    const decodedText = responseText.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

    return JSON.parse(decodedText);
  }

  // Fetch recent proposals
  async function fetchRecentProposals() {
    const response = await fetch('https://dev.comparisons/api/RecentProposals.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    const decodedText = responseText.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

    const data = JSON.parse(decodedText);

    // Handle array response format
    return Array.isArray(data) ? (Array.isArray(data[0]) ? data[0] : data) : [];
  }

  // Fetch recent clients
  async function fetchRecentClients() {
    const response = await fetch('https://dev.comparisons/api/RecentClients.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    const decodedText = responseText.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

    const data = JSON.parse(decodedText);

    // Handle array response format
    return Array.isArray(data) ? (Array.isArray(data[0]) ? data[0] : data) : [];
  }

  // Display user profile
  function displayUserProfile() {
    if (userProfile.name) {
      // Update welcome message
      document.getElementById('welcome-message').textContent = `ברוך הבא, ${userProfile.name}`;

      // Update user avatars and info
      const initials = getInitials(userProfile.name);
      const userAvatars = document.querySelectorAll('#user-avatar, .dropdown-user-avatar');
      userAvatars.forEach(avatar => {
        avatar.textContent = initials;
      });

      const userNameElements = document.querySelectorAll('.dropdown-user-name');
      const userEmailElements = document.querySelectorAll('.dropdown-user-email');

      userNameElements.forEach(el => el.textContent = userProfile.name);
      if (userProfile.email) {
        userEmailElements.forEach(el => el.textContent = userProfile.email);
      }
    }
  }

  // Display dashboard statistics
  function displayDashboardStats() {
    const statsGrid = document.getElementById('stats-grid');
    statsGrid.innerHTML = '';

    // Default stats structure
    const defaultStats = [
      {
        icon: 'fas fa-users',
        title: 'סה"כ לקוחות',
        value: 0,
        change: 'החודש',
        changeType: 'neutral',
      },
      {
        icon: 'fas fa-file-contract',
        title: 'השוואות החודש',
        value: 0,
        change: 'החודש הנוכחי',
        changeType: 'neutral',
      },
      {
        icon: 'fas fa-handshake',
        title: 'עסקאות שנסגרו',
        value: 0,
        change: 'החודש הנוכחי',
        changeType: 'neutral',
      },
      {
        icon: 'fas fa-shekel-sign',
        title: 'חיסכון ללקוחות',
        value: '₪0',
        change: 'החודש',
        changeType: 'neutral',
      },
    ];

    // Use API data if available, otherwise use defaults
    const stats = dashboardData.stats ? [
      {
        icon: 'fas fa-users',
        title: 'סה"כ לקוחות',
        value: dashboardData.stats.total_clients || 0,
        change: dashboardData.stats.clients_change || 'החודש',
        changeType: getChangeType(dashboardData.stats.clients_change_value),
      },
      {
        icon: 'fas fa-file-contract',
        title: 'השוואות החודש',
        value: dashboardData.stats.comparisons_this_month || 0,
        change: dashboardData.stats.comparisons_change || 'החודש הנוכחי',
        changeType: getChangeType(dashboardData.stats.comparisons_change_value),
      },
      {
        icon: 'fas fa-handshake',
        title: 'עסקאות שנסגרו',
        value: dashboardData.stats.deals_closed || 0,
        change: dashboardData.stats.deals_change || 'החודש הנוכחי',
        changeType: getChangeType(dashboardData.stats.deals_change_value),
      },
      {
        icon: 'fas fa-shekel-sign',
        title: 'חיסכון ללקוחות',
        value: formatCurrency(dashboardData.stats.total_savings || 0),
        change: dashboardData.stats.savings_change || 'החודש',
        changeType: getChangeType(dashboardData.stats.savings_change_value),
      },
    ] : defaultStats;

    stats.forEach(stat => {
      const statCard = document.createElement('div');
      statCard.className = 'stat-card';
      statCard.innerHTML = `
                <div class="stat-icon">
                  <i class="${stat.icon}"></i>
                </div>
                <div class="stat-content">
                  <h3>${escapeHtml(stat.title)}</h3>
                  <div class="stat-number" data-value="${typeof stat.value === 'string'
          ? stat.value.replace(/[^\d]/g, '')
          : stat.value}">${escapeHtml(stat.value.toString())}</div>
                  <div class="stat-change ${stat.changeType}">${escapeHtml(stat.change)}</div>
                </div>
              `;
      statsGrid.appendChild(statCard);
    });
  }

  // Display recent proposals
  function displayRecentProposals() {
    const proposalsList = document.getElementById('proposals-list');
    proposalsList.innerHTML = '';

    if (!dashboardData.proposals || dashboardData.proposals.length === 0) {
      proposalsList.innerHTML = `
                <div class="empty-proposals">
                  <i class="fas fa-file-contract"></i>
                  <p>אין הצעות אחרונות</p>
                  <a href="comparison.php" class="btn btn-primary btn-sm">צור השוואה ראשונה</a>
                </div>
              `;
      return;
    }

    // Create proposals list container
    const proposalsContainer = document.createElement('div');
    proposalsContainer.className = 'proposals-container';

    dashboardData.proposals.slice(0, 5).forEach(proposal => {
      const proposalItem = document.createElement('div');
      proposalItem.className = 'proposal-item';

      const statusClass = getProposalStatusClass(proposal.status);
      const statusText = getProposalStatusText(proposal.status);
      const formattedDate = formatDate(proposal.created_at);

      proposalItem.innerHTML = `
                <div class="proposal-info">
                  <div class="client-name">${escapeHtml(proposal.client_name || 'לקוח לא ידוע')}</div>
                  <div class="proposal-type">${escapeHtml(proposal.insurance_type || 'ביטוח')}</div>
                  <div class="proposal-date">${formattedDate}</div>
                </div>
                <div class="proposal-status">
                  <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
                <div class="proposal-actions">
                  <button class="btn-icon" title="צפה בפרטים" onclick="viewProposalDetails('${proposal.id}')">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn-icon" title="שלח בווטסאפ" onclick="sendProposalWhatsApp('${proposal.id}', '${escapeHtml(
          proposal.client_name)}')">
                    <i class="fab fa-whatsapp"></i>
                  </button>
                </div>
              `;

      proposalsContainer.appendChild(proposalItem);
    });

    proposalsList.appendChild(proposalsContainer);
  }

  // Display recent clients
  function displayRecentClients() {
    const clientsGrid = document.getElementById('clients-grid');
    clientsGrid.innerHTML = '';

    if (!dashboardData.clients || dashboardData.clients.length === 0) {
      clientsGrid.innerHTML = `
                <div class="empty-clients">
                  <i class="fas fa-users"></i>
                  <p>אין לקוחות במערכת</p>
                  <a href="clients.php" class="btn btn-primary btn-sm">הוסף לקוח ראשון</a>
                </div>
              `;
      return;
    }

    dashboardData.clients.slice(0, 6).forEach(client => {
      const clientCard = document.createElement('div');
      clientCard.className = 'client-card';

      // Generate initials for avatar
      const initials = getClientInitials(client.name);

      // Determine client status and activity
      const isActive = client.status === 'active' || !client.status;
      const lastActivity = client.last_activity ? formatRelativeDate(client.last_activity) : null;
      const totalComparisons = client.total_comparisons || 0;
      const totalPolicies = client.total_policies || 0;

      clientCard.innerHTML = `
    <div class="client-card-header">
      <div class="client-avatar-container">
        <div class="client-avatar ${isActive ? 'active' : 'inactive'}">
          ${client.avatar
          ? `<img src="${client.avatar}" alt="${escapeHtml(client.name)}" />`
          : `<span class="avatar-initials">${initials}</span>`}
        </div>
      </div>
    </div>

    <div class="client-info">
      <h4 class="client-name">${escapeHtml(client.name)}</h4>
      <div class="client-details">
        ${client.phone ? `
          <div class="detail-row">
            <i class="fas fa-phone detail-icon"></i>
            <span class="detail-text">${escapeHtml(client.phone)}</span>
          </div>
        ` : ''}
        ${client.email ? `
          <div class="detail-row">
            <i class="fas fa-envelope detail-icon"></i>
            <span class="detail-text">${escapeHtml(client.email)}</span>
          </div>
        ` : ''}
        ${lastActivity ? `
          <div class="detail-row last-activity">
            <i class="fas fa-clock detail-icon"></i>
            <span class="detail-text">פעילות: ${lastActivity}</span>
          </div>
        ` : ''}
      </div>

      <div class="client-stats">
        <div class="stat-item">
          <div class="stat-number">${totalComparisons}</div>
          <div class="stat-label">השוואות</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-number">${totalPolicies}</div>
          <div class="stat-label">פוליסות</div>
        </div>
      </div>
    </div>

    <div class="client-actions">
      <button class="btn btn-primary btn-sm client-action-btn" onclick="createComparisonForClient('${client.id}', '${escapeHtml(
          client.name)}')">
        <i class="fas fa-balance-scale"></i>
        <span>השוואה חדשה</span>
      </button>
      ${client.phone ? `
        <button class="btn btn-success btn-sm client-action-btn" onclick="callClient('${escapeHtml(client.phone)}', '${escapeHtml(
          client.name)}')">
          <i class="fas fa-phone"></i>
        </button>
        <button class="btn btn-whatsapp btn-sm client-action-btn" onclick="whatsappClient('${escapeHtml(
          client.phone)}', '${escapeHtml(client.name)}')">
          <i class="fab fa-whatsapp"></i>
        </button>
      ` : ''}
    </div>
  `;

      // Add click event for card navigation
      clientCard.addEventListener('click', (e) => {
        if (!e.target.closest('.client-actions') && !e.target.closest('.client-menu')) {
          window.location.href = `clients.php?id=${client.id}`;
        }
      });

      clientsGrid.appendChild(clientCard);
    });
  }

  // Show/hide states
  function showLoadingState() {
    hideAllStates();
    document.getElementById('loading-state').style.display = 'flex';
  }

  function showErrorState() {
    hideAllStates();
    document.getElementById('error-state').style.display = 'flex';
  }

  function hideAllStates() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('error-state').style.display = 'none';
    document.getElementById('dashboard-content').style.display = 'none';
  }

  // Animate statistics
  function animateStatistics() {
    const statNumbers = document.querySelectorAll('.stat-number');

    statNumbers.forEach((stat) => {
      const dataValue = stat.getAttribute('data-value');
      const finalValue = parseInt(dataValue) || 0;

      if (finalValue > 0) {
        animateNumber(stat, 0, finalValue, 2000);
      }
    });
  }

  function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    const originalText = element.textContent;
    const isShekels = originalText.includes('₪');

    function updateNumber(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);

      const currentValue = Math.floor(start + (end - start) * progress);

      if (isShekels) {
        element.textContent = `₪${currentValue.toLocaleString('he-IL')}`;
      } else {
        element.textContent = currentValue.toLocaleString('he-IL');
      }

      if (progress < 1) {
        requestAnimationFrame(updateNumber);
      }
    }

    requestAnimationFrame(updateNumber);
  }

  // Action handlers
  function viewProposalDetails(proposalId) {
    window.location.href = `activity.php?proposal=${proposalId}`;
  }

  function sendProposalWhatsApp(proposalId, clientName) {
    const message = encodeURIComponent(`שלום ${clientName}! הנה ההשוואה שביקשת עבור הביטוח שלך. לפרטים נוספים אשמח לעזור.`);
    window.open(`https://wa.me/?text=${message}`, '_blank');
  }

  function createComparisonForClient(clientId, clientName) {
    window.location.href = `comparison.php?client=${encodeURIComponent(clientId)}`;
  }

  // Utility functions
  function getInitials(name) {
    if (!name) return 'א.כ';

    const words = name.trim().split(' ');
    if (words.length >= 2) {
      return words[0].charAt(0) + '.' + words[1].charAt(0);
    } else {
      return words[0].charAt(0) + '.';
    }
  }

  function getChangeType(value) {
    if (!value) return 'neutral';
    const numValue = parseFloat(value);
    if (numValue > 0) return 'positive';
    if (numValue < 0) return 'negative';
    return 'neutral';
  }

  function getProposalStatusClass(status) {
    const statusMap = {
      'pending': 'pending',
      'approved': 'approved',
      'rejected': 'rejected',
      'completed': 'approved',
    };
    return statusMap[status] || 'pending';
  }

  function getProposalStatusText(status) {
    const statusMap = {
      'pending': 'ממתין',
      'approved': 'אושר',
      'rejected': 'נדחה',
      'completed': 'הושלם',
    };
    return statusMap[status] || 'ממתין';
  }

  function formatCurrency(amount) {
    if (!amount) return '₪0';
    return new Intl.NumberFormat('he-IL', {
      style: 'currency',
      currency: 'ILS',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount);
  }

  function formatDate(dateString) {
    try {
      if (!dateString) return 'לא זמין';

      const timestampValue = typeof dateString === 'string' ?
          isNaN(Number(dateString)) ? Date.parse(dateString) : Number(dateString) :
          dateString;

      const date = new Date(timestampValue * 1000);

      if (isNaN(date.getTime())) {
        return 'לא זמין';
      }

      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays === 1) {
        return 'אתמול';
      } else if (diffDays < 7) {
        return `לפני ${diffDays} ימים`;
      } else {
        return date.toLocaleDateString('he-IL', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        });
      }
    } catch (error) {
      console.error('Error formatting date:', error);
      return 'לא זמין';
    }
  }

  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Get client initials for avatar
  function getClientInitials(name) {
    if (!name) return 'לק';

    const words = name.trim().split(' ');
    if (words.length >= 2) {
      return words[0].charAt(0) + words[1].charAt(0);
    } else {
      return words[0].charAt(0) + (words[0].charAt(1) || '');
    }
  }

  // Format relative date for last activity
  function formatRelativeDate(dateString) {
    try {
      if (!dateString) return null;

      const timestampValue = typeof dateString === 'string' ?
          isNaN(Number(dateString)) ? Date.parse(dateString) : Number(dateString) :
          dateString;

      const date = new Date(timestampValue * 1000);

      if (isNaN(date.getTime())) {
        return null;
      }

      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));

      if (diffHours < 1) {
        return 'עכשיו';
      } else if (diffHours < 24) {
        return `לפני ${diffHours} שעות`;
      } else if (diffDays === 1) {
        return 'אתמול';
      } else if (diffDays < 7) {
        return `לפני ${diffDays} ימים`;
      } else {
        return date.toLocaleDateString('he-IL', {
          day: '2-digit',
          month: '2-digit',
        });
      }
    } catch (error) {
      console.error('Error formatting relative date:', error);
      return null;
    }
  }

  // Toggle client menu
  function toggleClientMenu(event, clientId) {
    event.stopPropagation();
    // Add menu functionality here if needed
    console.log('Toggle menu for client:', clientId);
  }

  // Call client
  function callClient(phone, name) {
    window.location.href = `tel:${phone}`;
  }

  // WhatsApp client
  function whatsappClient(phone, name) {
    const message = encodeURIComponent(`שלום ${name}! אשמח לעזור לך עם הביטוח שלך.`);
    const cleanPhone = phone.replace(/[^\d]/g, '');
    const formattedPhone = cleanPhone.startsWith('0') ? cleanPhone.substring(1) : cleanPhone;
    window.open(`https://wa.me/972${formattedPhone}?text=${message}`, '_blank');
  }
</script>

<style>
    /* Additional styles for the dashboard page */
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

    .proposals-container {
        background-color: #1f2937;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #374151;
    }

    .empty-proposals,
    .empty-clients {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        text-align: center;
        background-color: #1f2937;
        border-radius: 0.75rem;
        border: 1px solid #374151;
    }

    .empty-proposals i,
    .empty-clients i {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .empty-proposals p,
    .empty-clients p {
        color: #9ca3af;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .loading-state,
        .error-state {
            padding: 2rem 1rem;
            min-height: 300px;
        }

        .error-icon {
            font-size: 3rem;
        }

        .empty-proposals,
        .empty-clients {
            padding: 2rem 1rem;
        }

        .empty-proposals i,
        .empty-clients i {
            font-size: 2.5rem;
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

    /* Enhanced stat cards */
    .stat-change.positive {
        color: #10b981;
    }

    .stat-change.negative {
        color: #ef4444;
    }

    .stat-change.neutral {
        color: #9ca3af;
    }

    /* Proposal item improvements */
    .proposal-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #374151;
        transition: background-color 0.2s;
    }

    .proposal-item:last-child {
        border-bottom: none;
    }

    .proposal-item:hover {
        background-color: #2d3748;
    }

    .proposal-info {
        flex: 1;
    }

    .client-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .proposal-type {
        color: #9ca3af;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .proposal-date {
        color: #9ca3af;
        font-size: 0.75rem;
    }

    .proposal-status {
        margin: 0 1rem;
    }

    .proposal-actions {
        display: flex;
        gap: 0.5rem;
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

    /* Enhanced Client Cards */
    .client-card {
        background: linear-gradient(135deg, #1f2937 0%, #2d3748 100%);
        border: 1px solid #374151;
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .client-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, rgb(255, 23, 68), #ff6b6b);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .client-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 23, 68, 0.3);
    }

    .client-card:hover::before {
        transform: scaleX(1);
    }

    .client-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .client-avatar-container {
        position: relative;
    }

    .client-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: white;
        background: linear-gradient(135deg, rgb(255, 23, 68), #ff6b6b);
        border: 2px solid #374151;
        transition: all 0.3s ease;
        position: relative;
    }

    .client-avatar.active {
        border-color: #10b981;
        background: linear-gradient(135deg, rgb(255, 23, 68), #ff6b6b);
    }

    .client-avatar.inactive {
        border-color: #6b7280;
        background: linear-gradient(135deg, #6b7280, #9ca3af);
    }

    .client-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-initials {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        z-index: 1;
        position: relative;
    }

    .status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #1f2937;
    }

    .status-dot.active {
        background-color: #10b981;
    }

    .status-dot.inactive {
        background-color: #6b7280;
    }

    .client-menu {
        position: relative;
    }

    .client-menu-btn {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.375rem;
        border: 1px solid #374151;
        background-color: transparent;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .client-menu-btn:hover {
        background-color: #374151;
        color: #f9fafb;
    }

    .client-info {
        flex-direction: column;
        margin-bottom: 1rem;
    }

    .client-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #f9fafb;
        line-height: 1.2;
    }

    .client-details {
        margin-bottom: 1rem;
    }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
        font-size: 0.8rem;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }

    .detail-icon {
        width: 0.875rem;
        text-align: center;
        color: rgb(255, 23, 68);
        flex-shrink: 0;
    }

    .detail-text {
        color: #9ca3af;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .detail-row.last-activity .detail-text {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .client-stats {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        padding: 1rem 0.75rem;
        background-color: rgba(55, 65, 81, 0.5);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(75, 85, 99, 0.3);
        min-height: 3.5rem;
        width: 100%;
        box-sizing: border-box;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        text-align: center;
        min-width: 0;
        padding: 0 0.25rem;
    }

    .stat-number {
        font-size: 1.4rem;
        font-weight: 700;
        color: rgb(255, 23, 68);
        margin-bottom: 0.25rem;
        line-height: 1.2;
        white-space: nowrap;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #9ca3af;
        text-align: center;
        line-height: 1.2;
        white-space: nowrap;
        font-weight: 500;
    }

    .stat-divider {
        width: 1px;
        height: 2.5rem;
        background-color: #4b5563;
        margin: 0 0.5rem;
        flex-shrink: 0;
        align-self: center;
    }

    .client-actions {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .client-action-btn {
        flex: 1;
        min-width: auto;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .client-action-btn span {
        margin-left: 0.25rem;
    }

    .btn-whatsapp {
        background-color: #25d366;
        border-color: #25d366;
        color: white;
        flex: 0 0 auto;
        padding: 0.5rem;
    }

    .btn-whatsapp:hover {
        background-color: #128c7e;
        border-color: #128c7e;
    }

    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
        flex: 0 0 auto;
        padding: 0.5rem;
    }

    .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .client-card {
            padding: 1rem;
        }

        .client-avatar {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.9rem;
        }

        .avatar-initials {
            font-size: 0.9rem;
        }

        .client-name {
            font-size: 0.9rem;
        }

        .detail-row {
            font-size: 0.75rem;
        }

        .client-stats {
            padding: 0.75rem 0.5rem;
            min-height: 3rem;
        }

        .stat-item {
            padding: 0 0.125rem;
        }

        .stat-number {
            font-size: 1.2rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }

        .stat-divider {
            height: 2rem;
            margin: 0 0.25rem;
        }

        .client-action-btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
        }
    }

    /* Animation for card interactions */
    @keyframes cardPulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.01);
        }
        100% {
            transform: scale(1);
        }
    }

    .client-card:active {
        animation: cardPulse 0.2s ease;
    }
</style>

<?php
include 'htmls/footer.php';
?>
