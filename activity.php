<?php
include __DIR__ . '/htmls/header.php';
?>

<div class="content-area">
    <div class="page-header">
        <h1>פעילות אחרונה</h1>
        <p>כל ההשוואות שבוצעו לאחרונה</p>
    </div>

    <!-- Loading State -->
    <div class="loading-state" id="loading-state" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
        </div>
        <p>טוען רשימת פעילויות...</p>
    </div>

    <!-- Error State -->
    <div class="error-state" id="error-state" style="display: none;">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>שגיאה בטעינת הנתונים</h3>
        <p>לא ניתן לטעון את רשימת הפעילויות. אנא נסה שוב.</p>
        <button class="btn btn-primary" onclick="loadActivityData()">נסה שוב</button>
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="empty-state" style="display: none;">
        <div class="empty-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3>אין פעילויות במערכת</h3>
        <p>התחל על ידי יצירת השוואה ראשונה</p>
        <a href="comparison.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            צור השוואה ראשונה
        </a>
    </div>

    <!-- Activity Content -->
    <div class="activity-content" id="activity-content" style="display: none;">
        <!-- Filter Options -->
        <div class="filter-section">
            <div class="filter-buttons">
                <button class="filter-button active" data-filter="all">הכל</button>
                <button class="filter-button" data-filter="pending">ממתין</button>
                <button class="filter-button" data-filter="approved">אושר</button>
                <button class="filter-button" data-filter="rejected">נדחה</button>
                <button class="filter-button" data-filter="completed">הושלם</button>
            </div>
            <div class="date-filter">
                <select class="form-select" id="date-filter">
                    <option value="week">השבוע האחרון</option>
                    <option value="month">החודש האחרון</option>
                    <option value="quarter">3 חודשים אחרונים</option>
                    <option value="year">השנה האחרונה</option>
                </select>
                <button class="btn btn-outline btn-sm" onclick="refreshActivityData()">
                    <i class="fas fa-sync-alt"></i>
                    רענן
                </button>
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="activity-stats" id="activity-stats">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Activity List -->
        <div class="activity-list" id="activity-list">
            <!-- Activities will be loaded here -->
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="pagination-container" style="display: none;">
            <div class="pagination-info">
                <span id="pagination-info-text">מציג 1-10 מתוך 25 תוצאות</span>
            </div>
            <div class="pagination-controls" id="pagination-controls">
                <!-- Pagination buttons will be generated here -->
            </div>
        </div>
    </div>
</div>


<!-- Activity Details Modal -->
<div class="modal" id="details-modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3>פרטי ההשוואה</h3>
            <button class="modal-close" id="close-details-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="activity-details-content">
                <!-- Activity details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="delete-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>אישור מחיקה</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>האם אתה בטוח שברצונך למחוק השוואה זו?</p>
                <p class="delete-note">פעולה זו אינה ניתנת לביטול.</p>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">ביטול</button>
                <button type="button" class="btn btn-destructive" id="confirm-delete-btn">מחק השוואה</button>
            </div>
        </div>
    </div>
</div>

<script>
  // Global variables
  let activitiesData = [];
  let filteredActivities = [];
  let currentFilter = 'all';
  let currentDateFilter = 'week';
  let currentPage = 1;
  let itemsPerPage = 10;
  let activityToDelete = null;

  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    initializeActivityPage();
  });


  function initializeActivityPage() {
    // Load activity data
    loadActivityData();

    // Initialize event listeners
    initializeEventListeners();
  }

  function initializeEventListeners() {
    // Filter buttons
    document.querySelectorAll('.filter-button').forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');

        currentFilter = this.dataset.filter;
        currentPage = 1;
        applyFilters();
      });
    });

    // Date filter
    document.getElementById('date-filter').addEventListener('change', function() {
      currentDateFilter = this.value;
      currentPage = 1;
      loadActivityData();
    });

    // Modal close
    document.getElementById('close-details-modal').addEventListener('click', function() {
      document.getElementById('details-modal').style.display = 'none';
    });
  }

  // Load activity data from API
  async function loadActivityData() {
    showLoadingState();

    try {
      const [activitiesResponse, statsResponse] = await Promise.all([
        fetchActivities(),
        fetchActivityStats(),
      ]);

      activitiesData = activitiesResponse;

      // Display data
      displayActivityStats(statsResponse);
      applyFilters();

      // Show content
      hideAllStates();
      document.getElementById('activity-content').style.display = 'block';

    } catch (error) {
      console.error('Error loading activity data:', error);
      showErrorState();

      // Show notification
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בטעינת רשימת הפעילויות', 'error');
      }
    }
  }

  // Fetch activities from API
  async function fetchActivities() {
    const response = await fetch(`https://dev.comparisons/api/ActivityList.php?period=${currentDateFilter}`, {
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

  // Fetch activity statistics
  async function fetchActivityStats() {
    const response = await fetch(`https://dev.comparisons/api/ActivityStats.php?period=${currentDateFilter}`, {
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

  // Display activity statistics
  function displayActivityStats(stats) {
    const statsContainer = document.getElementById('activity-stats');
    statsContainer.innerHTML = '';

    // Default stats if none from API
    const defaultStats = [
      {title: 'סה"כ השוואות', value: 0, icon: 'fas fa-balance-scale', color: 'blue'},
      {title: 'ממתינות לאישור', value: 0, icon: 'fas fa-clock', color: 'yellow'},
      {title: 'אושרו', value: 0, icon: 'fas fa-check-circle', color: 'green'},
      {title: 'נדחו', value: 0, icon: 'fas fa-times-circle', color: 'red'},
    ];

    const statsData = stats ? [
      {title: 'סה"כ השוואות', value: stats.total || 0, icon: 'fas fa-balance-scale', color: 'blue'},
      {title: 'ממתינות לאישור', value: stats.pending || 0, icon: 'fas fa-clock', color: 'yellow'},
      {title: 'אושרו', value: stats.approved || 0, icon: 'fas fa-check-circle', color: 'green'},
      {title: 'נדחו', value: stats.rejected || 0, icon: 'fas fa-times-circle', color: 'red'},
    ] : defaultStats;

    const statsGrid = document.createElement('div');
    statsGrid.className = 'stats-grid';

    statsData.forEach(stat => {
      const statCard = document.createElement('div');
      statCard.className = 'stat-card';
      statCard.innerHTML = `
          <div class="stat-icon ${stat.color}">
            <i class="${stat.icon}"></i>
          </div>
          <div class="stat-content">
            <h3>${escapeHtml(stat.title)}</h3>
            <div class="stat-number">${stat.value}</div>
          </div>
        `;
      statsGrid.appendChild(statCard);
    });

    statsContainer.appendChild(statsGrid);
  }

  // Apply filters and search
  function applyFilters(searchTerm = '') {
    let filtered = [...activitiesData];

    // Apply status filter
    if (currentFilter !== 'all') {
      filtered = filtered.filter(activity => activity.status === currentFilter);
    }

    // Apply search filter
    if (searchTerm) {
      filtered = filtered.filter(activity => {
        return activity.client_name.toLowerCase().includes(searchTerm) ||
            activity.insurance_type.toLowerCase().includes(searchTerm) ||
            (activity.notes && activity.notes.toLowerCase().includes(searchTerm));
      });
    }

    filteredActivities = filtered;

    if (filteredActivities.length === 0) {
      showEmptyState();
    } else {
      hideAllStates();
      document.getElementById('activity-content').style.display = 'block';
      displayActivities();
      updatePagination();
    }
  }

  // Display activities
  function displayActivities() {
    const activityList = document.getElementById('activity-list');
    activityList.innerHTML = '';

    // Calculate pagination
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedActivities = filteredActivities.slice(startIndex, endIndex);

    paginatedActivities.forEach(activity => {
      const activityItem = createActivityItem(activity);
      activityList.appendChild(activityItem);
    });
  }

  // Create activity item element
  function createActivityItem(activity) {
    const activityItem = document.createElement('div');
    activityItem.className = 'activity-item';
    activityItem.dataset.status = activity.status;

    const statusClass = getStatusClass(activity.status);
    const statusText = getStatusText(activity.status);
    const formattedDate = formatDate(activity.created_at);
    const insuranceTypeText = getInsuranceTypeText(activity.insurance_type);

    activityItem.innerHTML = `
        <div class="activity-header">
          <div class="activity-info">
            <h3>${insuranceTypeText}</h3>
            <div class="activity-meta">
              <span class="client-name">${escapeHtml(activity.client_name)}</span>
              <span class="activity-date">${formattedDate}</span>
            </div>
          </div>
          <div class="activity-status">
            <span class="status-badge ${statusClass}">${statusText}</span>
          </div>
        </div>

        <div class="activity-details">
          <div class="policy-summary">
            ${generatePolicySummary(activity)}
          </div>
        </div>

        <div class="activity-actions">
          <button class="btn btn-outline btn-sm" onclick="viewActivityDetails('${activity.id}')">
            <i class="fas fa-eye"></i>
            צפה בפרטים
          </button>
          <button class="btn btn-primary btn-sm" onclick="sendActivityWhatsApp('${activity.id}', '${escapeHtml(
        activity.client_name)}')">
            <i class="fab fa-whatsapp"></i>
            שלח בווטסאפ
          </button>
          <button class="btn btn-destructive btn-sm" onclick="deleteActivity('${activity.id}')">
            <i class="fas fa-trash"></i>
            מחק
          </button>
        </div>
      `;

    return activityItem;
  }


  // Generate policy summary based on activity data
  function generatePolicySummary(activity) {
    let summary = '';

    if (activity.insurance_type === 'travel') {
      summary = `
          <div class="policy-item">
            <strong>יעד:</strong> ${escapeHtml(activity.destination || 'לא צוין')}
          </div>
          <div class="policy-item">
            <strong>תאריכים:</strong> ${formatDateRange(activity.departure_date, activity.return_date)}
          </div>
          <div class="policy-item">
            <strong>נוסעים:</strong> ${activity.travelers_count || 1}
          </div>
        `;
    } else if (activity.insurance_type === 'car') {
      summary = `
          <div class="policy-item">
            <strong>רכב:</strong> ${escapeHtml(activity.car_details || 'לא צוין')}
          </div>
          <div class="policy-item">
            <strong>שנת ייצור:</strong> ${activity.car_year || 'לא צוין'}
          </div>
        `;
    } else if (activity.insurance_type === 'home') {
      summary = `
          <div class="policy-item">
            <strong>נכס:</strong> ${escapeHtml(activity.property_details || 'לא צוין')}
          </div>
          <div class="policy-item">
            <strong>כתובת:</strong> ${escapeHtml(activity.property_address || 'לא צוין')}
          </div>
        `;
    }

    // Add companies compared
    if (activity.companies_compared) {
      summary += `
          <div class="policy-item">
            <strong>חברות שהושוו:</strong> ${escapeHtml(activity.companies_compared)}
          </div>
        `;
    }

    // Add recommended offer
    if (activity.recommended_offer) {
      summary += `
          <div class="policy-item">
            <strong>הצעה מומלצת:</strong> ${escapeHtml(activity.recommended_offer)}
          </div>
        `;
    }

    return summary;
  }

  // Update pagination
  function updatePagination() {
    const totalItems = filteredActivities.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    if (totalPages <= 1) {
      document.getElementById('pagination-container').style.display = 'none';
      return;
    }

    // Update pagination info
    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);
    document.getElementById('pagination-info-text').textContent =
        `מציג ${startItem}-${endItem} מתוך ${totalItems} תוצאות`;

    // Generate pagination controls
    const paginationControls = document.getElementById('pagination-controls');
    paginationControls.innerHTML = '';

    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.className = `pagination-btn ${currentPage === 1 ? 'disabled' : ''}`;
    prevBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
    prevBtn.onclick = () => currentPage > 1 && changePage(currentPage - 1);
    paginationControls.appendChild(prevBtn);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
        pageBtn.textContent = i;
        pageBtn.onclick = () => changePage(i);
        paginationControls.appendChild(pageBtn);
      } else if (i === currentPage - 3 || i === currentPage + 3) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'pagination-ellipsis';
        ellipsis.textContent = '...';
        paginationControls.appendChild(ellipsis);
      }
    }

    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.className = `pagination-btn ${currentPage === totalPages ? 'disabled' : ''}`;
    nextBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
    nextBtn.onclick = () => currentPage < totalPages && changePage(currentPage + 1);
    paginationControls.appendChild(nextBtn);

    document.getElementById('pagination-container').style.display = 'flex';
  }

  // Change page
  function changePage(page) {
    currentPage = page;
    displayActivities();
    updatePagination();

    // Scroll to top of activity list
    document.getElementById('activity-list').scrollIntoView({behavior: 'smooth'});
  }

  // View activity details
  async function viewActivityDetails(activityId) {
    try {
      const response = await fetch(`https://dev.comparisons/api/ActivityDetails.php?id=${activityId}`, {
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
      const decodedText = responseText.replace(/&quot;/g, '"').
          replace(/&amp;/g, '&').
          replace(/&lt;/g, '<').
          replace(/&gt;/g, '>');
      const details = JSON.parse(decodedText);

      // Display details in modal
      displayActivityDetails(details);
      document.getElementById('details-modal').style.display = 'block';

    } catch (error) {
      console.error('Error loading activity details:', error);

      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בטעינת פרטי ההשוואה', 'error');
      }
    }
  }

  // Display activity details in modal
  function displayActivityDetails(details) {
    const content = document.getElementById('activity-details-content');
    content.innerHTML = `
    <div class="activity-details-full">
      <div class="details-header">
        <div class="details-title">
          <h4>פרטי ההשוואה</h4>
        </div>
        <div class="details-actions">
          <button class="btn btn-outline btn-sm" onclick="toggleEditMode()">
            <i class="fas fa-edit"></i>
            <span id="edit-mode-text">ערוך</span>
          </button>
          <button class="btn btn-primary btn-sm" onclick="saveActivityChanges('${details.id}')" id="save-changes-btn" style="display: none;">
            <i class="fas fa-save"></i>
            שמור שינויים
          </button>
        </div>
      </div>

      <div class="details-grid">
        <div class="detail-item">
          <strong>מספר השוואה:</strong> ${details.id}
        </div>
        <div class="detail-item">
          <strong>תאריך יצירה:</strong> ${formatDate(details.created_at)}
        </div>
        <div class="detail-item">
          <strong>לקוח:</strong> ${escapeHtml(details.client_name)}
        </div>
        <div class="detail-item status-edit-container">
          <strong>סטטוס:</strong>
          <span class="status-display" id="status-display">${getStatusText(details.status)}</span>
          <select class="form-select status-edit" id="status-edit" style="display: none;">
            <option value="pending" ${details.status === 'pending' ? 'selected' : ''}>ממתין</option>
            <option value="approved" ${details.status === 'approved' ? 'selected' : ''}>אושר</option>
            <option value="rejected" ${details.status === 'rejected' ? 'selected' : ''}>נדחה</option>
            <option value="completed" ${details.status === 'completed' ? 'selected' : ''}>הושלם</option>
          </select>
        </div>
      </div>

      ${details.recommendation ? `
        <div class="recommendation-box">
          <div class="recommendation-header">
            <i class="fas fa-star"></i>
            <h4>המלצה</h4>
          </div>
          <div class="recommendation-content">
            <div class="recommendation-display" id="recommendation-display">${escapeHtml(details.recommendation)}</div>
            <textarea class="form-textarea recommendation-edit" id="recommendation-edit" style="display: none;" rows="3">${escapeHtml(
        details.recommendation)}</textarea>
          </div>
        </div>
      ` : `
        <div class="recommendation-box" id="recommendation-box" style="display: none;">
          <div class="recommendation-header">
            <i class="fas fa-star"></i>
            <h4>המלצה</h4>
          </div>
          <div class="recommendation-content">
            <textarea class="form-textarea recommendation-edit" id="recommendation-edit" rows="3" placeholder="הוסף המלצה..."></textarea>
          </div>
        </div>
      `}

      ${details.comparisons && details.comparisons.length > 0 ? `
  <div class="comparison-section">
    <div class="comparison-header" onclick="toggleComparisonTable()">
      <h4>השוואת הצעות</h4>
      <button class="toggle-btn" id="comparison-toggle">
        <i class="fas fa-chevron-down"></i>
      </button>
    </div>
    <div class="comparison-table-container" id="comparison-table-container" style="display: none;">
      <table class="comparison-table">
        <thead>
          <tr>
            <th>חברת ביטוח</th>
            <th>סוג כיסוי</th>
            <th>גבול אחריות</th>
            <th>השתתפות עצמית</th>
          </tr>
        </thead>
        <tbody>
          ${details.comparisons.map((comp, index) => `
            <tr class="${index === 0 ? 'recommended' : ''}">
              <td>${escapeHtml(comp.company)}</td>
              <td>${escapeHtml(comp.coverage_name || '')}</td>
              <td>${escapeHtml(comp.liability_limit || '')}</td>
              <td>${escapeHtml(comp.deductible || '')}</td>
            </tr>
            ${comp.notes ? `
            <tr class="notes-row ${index === 0 ? 'recommended' : ''}">
              <td colspan="4" class="comparison-notes">
                <i class="fas fa-info-circle"></i> ${escapeHtml(comp.notes)}
              </td>
            </tr>
            ` : ''}
          `).join('')}
        </tbody>
      </table>
    </div>
  </div>
` : ''}

      <div class="notes-section">
        <h4>הערות כלליות</h4>
        <div class="notes-display" id="notes-display" ${!details.notes ? 'style="display: none;"' : ''}>
          ${escapeHtml(details.notes || '')}
        </div>
        <div class="notes-edit-container" id="notes-edit-container" style="display: none;">
          <textarea class="form-textarea notes-edit" id="notes-edit" rows="4" placeholder="הוסף הערות...">${escapeHtml(
        details.notes || '')}</textarea>
        </div>
        <div class="notes-placeholder" id="notes-placeholder" ${details.notes ? 'style="display: none;"' : ''}>
          <span class="placeholder-text">אין הערות</span>
          <button class="btn btn-link btn-sm" onclick="addNotes()">הוסף הערות</button>
        </div>
      </div>

      <div class="activity-log" id="activity-log">
        <h4>יומן פעילות</h4>
        <div class="log-entries" id="log-entries">
          <!-- Activity log will be loaded here -->
        </div>
      </div>
    </div>
  `;

    // Load activity log
    loadActivityLog(details.id);
  }

  let isEditMode = false;
  let currentActivityId = null;

  function toggleEditMode() {
    isEditMode = !isEditMode;
    const editModeText = document.getElementById('edit-mode-text');
    const saveBtn = document.getElementById('save-changes-btn');

    // Toggle status editing
    const statusDisplay = document.getElementById('status-display');
    const statusEdit = document.getElementById('status-edit');

    // Toggle recommendation editing
    const recommendationDisplay = document.getElementById('recommendation-display');
    const recommendationEdit = document.getElementById('recommendation-edit');
    const recommendationBox = document.getElementById('recommendation-box');

    // Toggle notes editing
    const notesDisplay = document.getElementById('notes-display');
    const notesEditContainer = document.getElementById('notes-edit-container');
    const notesPlaceholder = document.getElementById('notes-placeholder');

    if (isEditMode) {
      editModeText.textContent = 'ביטול';
      saveBtn.style.display = 'inline-flex';

      // Show edit controls
      if (statusDisplay) statusDisplay.style.display = 'none';
      if (statusEdit) statusEdit.style.display = 'inline-block';

      if (recommendationDisplay) recommendationDisplay.style.display = 'none';
      if (recommendationEdit) {
        recommendationEdit.style.display = 'block';
        if (recommendationBox) recommendationBox.style.display = 'block';
      }

      if (notesDisplay && notesDisplay.textContent.trim()) {
        notesDisplay.style.display = 'none';
      }
      if (notesPlaceholder) notesPlaceholder.style.display = 'none';
      if (notesEditContainer) notesEditContainer.style.display = 'block';

    } else {
      editModeText.textContent = 'ערוך';
      saveBtn.style.display = 'none';

      // Show display controls
      if (statusDisplay) statusDisplay.style.display = 'inline';
      if (statusEdit) statusEdit.style.display = 'none';

      if (recommendationDisplay) recommendationDisplay.style.display = 'block';
      if (recommendationEdit) {
        recommendationEdit.style.display = 'none';
        if (!recommendationEdit.value.trim() && recommendationBox) {
          recommendationBox.style.display = 'none';
        }
      }

      const notesEditTextarea = document.getElementById('notes-edit');
      if (notesEditTextarea && notesEditTextarea.value.trim()) {
        if (notesDisplay) {
          notesDisplay.textContent = notesEditTextarea.value;
          notesDisplay.style.display = 'block';
        }
      } else {
        if (notesDisplay) notesDisplay.style.display = 'none';
        if (notesPlaceholder) notesPlaceholder.style.display = 'block';
      }
      if (notesEditContainer) notesEditContainer.style.display = 'none';
    }
  }

  function addNotes() {
    const notesPlaceholder = document.getElementById('notes-placeholder');
    const notesEditContainer = document.getElementById('notes-edit-container');

    if (notesPlaceholder) notesPlaceholder.style.display = 'none';
    if (notesEditContainer) notesEditContainer.style.display = 'block';

    if (!isEditMode) {
      toggleEditMode();
    }

    // Focus on textarea
    const notesEdit = document.getElementById('notes-edit');
    if (notesEdit) notesEdit.focus();
  }

  async function saveActivityChanges(activityId) {
    const saveBtn = document.getElementById('save-changes-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...';

    try {
      const statusEdit = document.getElementById('status-edit');
      const recommendationEdit = document.getElementById('recommendation-edit');
      const notesEdit = document.getElementById('notes-edit');

      const updateData = {
        id: activityId,
        status: statusEdit ? statusEdit.value : null,
        recommendation: recommendationEdit ? recommendationEdit.value : null,
        notes: notesEdit ? notesEdit.value : null,
      };

      const response = await fetch('https://dev.comparisons/api/UpdateActivity.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
        body: JSON.stringify(updateData),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      if (result.success) {
        // Update display values
        const statusDisplay = document.getElementById('status-display');
        if (statusDisplay && statusEdit) {
          statusDisplay.textContent = getStatusText(statusEdit.value);
        }

        const recommendationDisplay = document.getElementById('recommendation-display');
        if (recommendationDisplay && recommendationEdit) {
          recommendationDisplay.textContent = recommendationEdit.value;
        }

        // Exit edit mode
        toggleEditMode();

        // Refresh activity list
        await loadActivityData();

        // Add to activity log
        addLogEntry(activityId, 'עודכן', 'הפרטים עודכנו בהצלחה');

        // Show success message
        if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
          window.InsurancePlatform.showNotification('השינויים נשמרו בהצלחה!', 'success');
        }
      } else {
        throw new Error(result.message || 'שגיאה בשמירת השינויים');
      }

    } catch (error) {
      console.error('Error saving activity changes:', error);

      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בשמירת השינויים', 'error');
      }
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalText;
    }
  }

  async function loadActivityLog(activityId) {
    try {
      const response = await fetch(`https://dev.comparisons/api/ActivityLog.php?id=${activityId}`, {
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
      const decodedText = responseText.replace(/&quot;/g, '"').
          replace(/&amp;/g, '&').
          replace(/&lt;/g, '<').
          replace(/&gt;/g, '>');
      const logEntries = JSON.parse(decodedText);

      displayActivityLog(logEntries);

    } catch (error) {
      console.error('Error loading activity log:', error);
      const logContainer = document.getElementById('log-entries');
      if (logContainer) {
        logContainer.innerHTML = '<p class="no-log-entries">לא ניתן לטעון יומן פעילות</p>';
      }
    }
  }

  function displayActivityLog(logEntries) {
    const logContainer = document.getElementById('log-entries');
    if (!logContainer) return;

    if (!logEntries || logEntries.length === 0) {
      logContainer.innerHTML = '<p class="no-log-entries">אין רשומות ביומן</p>';
      return;
    }

    logContainer.innerHTML = logEntries.map(entry => `
    <div class="log-entry">
      <div class="log-entry-header">
        <span class="log-action">${escapeHtml(entry.action)}</span>
        <span class="log-date">${formatDate(entry.created_at)}</span>
      </div>
      <div class="log-entry-content">
        ${escapeHtml(entry.description)}
      </div>
      ${entry.user_name ? `
        <div class="log-entry-user">
          <i class="fas fa-user"></i>
          ${escapeHtml(entry.user_name)}
        </div>
      ` : ''}
    </div>
  `).join('');
  }

  function addLogEntry(activityId, action, description) {
    // Add a new entry to the log display
    const logContainer = document.getElementById('log-entries');
    if (!logContainer) return;

    const newEntry = document.createElement('div');
    newEntry.className = 'log-entry new-entry';
    newEntry.innerHTML = `
    <div class="log-entry-header">
      <span class="log-action">${escapeHtml(action)}</span>
      <span class="log-date">${formatDate(Date.now() / 1000)}</span>
    </div>
    <div class="log-entry-content">
      ${escapeHtml(description)}
    </div>
    <div class="log-entry-user">
      <i class="fas fa-user"></i>
    </div>
  `;

    // Remove "no entries" message if exists
    const noEntries = logContainer.querySelector('.no-log-entries');
    if (noEntries) {
      noEntries.remove();
    }

    // Add new entry at the top
    logContainer.insertBefore(newEntry, logContainer.firstChild);

    // Highlight the new entry
    setTimeout(() => {
      newEntry.classList.remove('new-entry');
    }, 2000);
  }

  function toggleComparisonTable() {
    const container = document.getElementById('comparison-table-container');
    const toggleBtn = document.getElementById('comparison-toggle');
    const icon = toggleBtn.querySelector('i');

    if (container.style.display === 'none') {
      container.style.display = 'block';
      icon.className = 'fas fa-chevron-up';
      toggleBtn.setAttribute('aria-expanded', 'true');
    } else {
      container.style.display = 'none';
      icon.className = 'fas fa-chevron-down';
      toggleBtn.setAttribute('aria-expanded', 'false');
    }
  }

  // Send activity via WhatsApp
  function sendActivityWhatsApp(activityId, clientName) {
    const message = encodeURIComponent(`שלום ${clientName}! הנה ההשוואה שביקשת עבור הביטוח שלך. לפרטים נוספים אשמח לעזור.`);
    window.open(`https://wa.me/?text=${message}`, '_blank');
  }

  // Download activity PDF
  async function downloadActivityPDF(activityId, clientName) {
    try {
      // Show loading state
      const loadingToast = document.createElement('div');
      loadingToast.className = 'toast loading';
      loadingToast.innerHTML = `
      <i class="fas fa-spinner fa-spin"></i>
      <span>מכין PDF...</span>
    `;
      document.body.appendChild(loadingToast);

      const response = await fetch(`api/GeneratePDF.php?id=${activityId}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Get the PDF blob
      const blob = await response.blob();

      // Create download link
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `השוואת_ביטוח_${clientName}_${new Date().toISOString().split('T')[0]}.pdf`;
      document.body.appendChild(a);
      a.click();

      // Cleanup
      window.URL.revokeObjectURL(url);
      document.body.removeChild(a);
      document.body.removeChild(loadingToast);

      // Show success message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('PDF הורד בהצלחה!', 'success');
      }

    } catch (error) {
      console.error('Error downloading PDF:', error);

      // Remove loading toast if it exists
      const loadingToast = document.querySelector('.toast.loading');
      if (loadingToast) {
        document.body.removeChild(loadingToast);
      }

      // Show error message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בהורדת PDF', 'error');
      }
    }
  }

  // Delete activity
  function deleteActivity(activityId) {
    activityToDelete = activityId;
    document.getElementById('delete-modal').style.display = 'block';

    // Set up delete confirmation
    document.getElementById('confirm-delete-btn').onclick = async function() {
      await confirmDeleteActivity();
    };
  }

  async function confirmDeleteActivity() {
    if (!activityToDelete) return;

    const deleteBtn = document.getElementById('confirm-delete-btn');
    const originalText = deleteBtn.textContent;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מוחק...';

    try {
      const response = await fetch('https://dev.comparisons/api/DeleteActivity.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
        body: JSON.stringify({id: activityToDelete}),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Close modal and refresh data
      closeDeleteModal();
      await loadActivityData();

      // Show success message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('ההשוואה נמחקה בהצלחה!', 'success');
      }

    } catch (error) {
      console.error('Error deleting activity:', error);

      // Show error message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה במחיקת ההשוואה', 'error');
      }
    } finally {
      // Reset button
      deleteBtn.disabled = false;
      deleteBtn.textContent = originalText;
    }
  }

  function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
    activityToDelete = null;
  }

  // Refresh data
  function refreshActivityData() {
    loadActivityData();
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

  function showEmptyState() {
    hideAllStates();
    document.getElementById('empty-state').style.display = 'flex';
  }

  function hideAllStates() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('error-state').style.display = 'none';
    document.getElementById('empty-state').style.display = 'none';
    document.getElementById('activity-content').style.display = 'none';
  }

  // Utility functions
  function getStatusClass(status) {
    const statusMap = {
      'pending': 'pending',
      'approved': 'approved',
      'rejected': 'rejected',
      'completed': 'approved',
    };
    return statusMap[status] || 'pending';
  }

  function getStatusText(status) {
    const statusMap = {
      'pending': 'ממתין',
      'approved': 'אושר',
      'rejected': 'נדחה',
      'completed': 'הושלם',
    };
    return statusMap[status] || 'ממתין';
  }

  function getInsuranceTypeText(type) {
    const typeMap = {
      'travel': 'השוואת ביטוח נסיעות',
      'car': 'השוואת ביטוח רכב',
      'home': 'השוואת ביטוח דירה',
      'health': 'השוואת ביטוח בריאות',
    };
    return typeMap[type] || 'השוואת ביטוח';
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

      return date.toLocaleDateString('he-IL', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      });
    } catch (error) {
      console.error('Error formatting date:', error);
      return 'לא זמין';
    }
  }

  function formatDateRange(startDate, endDate) {
    if (!startDate || !endDate) return 'לא צוין';

    const start = new Date(startDate);
    const end = new Date(endDate);

    if (isNaN(start.getTime()) || isNaN(end.getTime())) {
      return 'לא צוין';
    }

    const startFormatted = start.toLocaleDateString('he-IL');
    const endFormatted = end.toLocaleDateString('he-IL');

    return `${startFormatted} - ${endFormatted}`;
  }

  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Close modals when clicking outside
  window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      if (e.target.id === 'details-modal') {
        document.getElementById('details-modal').style.display = 'none';
      } else if (e.target.id === 'delete-modal') {
        closeDeleteModal();
      }
    }
  });
</script>

<style>
    /* Additional styles for the activity page */
    .loading-state,
    .error-state,
    .empty-state {
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

    .error-icon,
    .empty-icon {
        font-size: 4rem;
        color: #9ca3af;
        margin-bottom: 1.5rem;
    }

    .error-state h3,
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #f9fafb;
    }

    .error-state p,
    .empty-state p {
        color: #9ca3af;
        margin-bottom: 2rem;
        max-width: 400px;
    }

    .activity-stats {
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background-color: #1f2937;
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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .stat-icon.yellow {
        background-color: rgba(251, 191, 36, 0.1);
        color: #f59e0b;
    }

    .stat-icon.green {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stat-icon.red {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .stat-content h3 {
        font-size: 0.875rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding: 1rem 0;
    }

    .pagination-info {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .pagination-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid #374151;
        background-color: #2d3748;
        color: #f9fafb;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .pagination-btn:hover:not(.disabled) {
        background-color: #374151;
        border-color: rgb(255, 23, 68);
    }

    .pagination-btn.active {
        background-color: rgb(255, 23, 68);
        border-color: rgb(255, 23, 68);
        color: white;
    }

    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-ellipsis {
        color: #9ca3af;
        padding: 0 0.5rem;
    }

    .delete-warning {
        text-align: center;
        padding: 1rem;
    }

    .delete-warning i {
        font-size: 3rem;
        color: #ef4444;
        margin-bottom: 1rem;
    }

    .delete-warning p {
        margin-bottom: 0.5rem;
    }

    .delete-note {
        color: #9ca3af;
        font-size: 0.875rem;
        margin-bottom: 2rem !important;
    }

    .activity-details-full {
        padding: 1rem 0;
    }

    .activity-details-full h4 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: rgb(255, 23, 68);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .detail-item {
        background-color: #2d3748;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .detail-item strong {
        color: #f9fafb;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0 2rem 0;
    }

    .comparison-table th,
    .comparison-table td {
        padding: 1rem;
        text-align: right;
        border-bottom: 1px solid #374151;
    }

    .comparison-table th {
        background-color: #2d3748;
        color: #9ca3af;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .comparison-table tr.recommended {
        background-color: rgba(255, 23, 68, 0.1);
    }

    .comparison-table tr.recommended td {
        color: rgb(255, 23, 68);
        font-weight: 500;
    }

    .notes-content {
        background-color: #2d3748;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid rgb(255, 23, 68);
        font-size: 0.875rem;
        line-height: 1.5;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .filter-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .filter-buttons {
            flex-wrap: wrap;
        }

        .date-filter {
            width: 100%;
            justify-content: space-between;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .activity-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .pagination-container {
            flex-direction: column;
            gap: 1rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .loading-state,
        .error-state,
        .empty-state {
            padding: 2rem 1rem;
            min-height: 300px;
        }

        .error-icon,
        .empty-icon {
            font-size: 3rem;
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

    .recommendation-box {
        background-color: rgba(255, 23, 68, 0.1);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(255, 23, 68, 0.3);
    }

    .recommendation-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .recommendation-header i {
        color: rgb(255, 215, 0);
        font-size: 1.25rem;
        margin-left: 0.75rem;
    }

    .recommendation-header h4 {
        color: #f9fafb;
        margin: 0;
    }

    .recommendation-content {
        color: #f9fafb;
        line-height: 1.6;
    }

    .comparison-table-container {
        overflow-x: auto;
        margin-bottom: 2rem;
    }

    .comparison-table tr.notes-row {
        background-color: transparent;
    }

    .comparison-table tr.notes-row.recommended {
        background-color: rgba(255, 23, 68, 0.05);
    }

    .comparison-notes {
        padding: 0.5rem 1rem 1.5rem 1rem !important;
        font-size: 0.875rem;
        color: #9ca3af;
        font-style: italic;
    }

    .comparison-notes i {
        margin-left: 0.5rem;
        color: #9ca3af;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #374151;
    }

    .details-title h4 {
        margin: 0;
    }

    .details-actions {
        display: flex;
        gap: 0.5rem;
    }

    .status-edit-container {
        position: relative;
    }

    .status-edit {
        width: auto;
        min-width: 120px;
        margin-right: 0.5rem;
    }

    .recommendation-edit,
    .notes-edit {
        width: 100%;
        background-color: #2d3748;
        border: 1px solid #4a5568;
        border-radius: 0.375rem;
        padding: 0.75rem;
        color: #f9fafb;
        font-size: 0.875rem;
        line-height: 1.5;
        resize: vertical;
    }

    .recommendation-edit:focus,
    .notes-edit:focus {
        outline: none;
        border-color: rgb(255, 23, 68);
        box-shadow: 0 0 0 3px rgba(255, 23, 68, 0.1);
    }

    .notes-section {
        margin-top: 2rem;
    }

    .notes-section h4 {
        margin-bottom: 1rem;
    }

    .notes-display {
        background-color: #2d3748;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid rgb(255, 23, 68);
        font-size: 0.875rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .notes-placeholder {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background-color: #2d3748;
        border-radius: 0.5rem;
        border: 2px dashed #4a5568;
    }

    .placeholder-text {
        color: #9ca3af;
        font-style: italic;
    }

    .activity-log {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #374151;
    }

    .activity-log h4 {
        margin-bottom: 1rem;
    }

    .log-entries {
        max-height: 300px;
        overflow-y: auto;
    }

    .log-entry {
        background-color: #2d3748;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #4a5568;
        transition: all 0.3s ease;
    }

    .log-entry.new-entry {
        border-left-color: rgb(255, 23, 68);
        background-color: rgba(255, 23, 68, 0.1);
    }

    .log-entry-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .log-action {
        font-weight: 600;
        color: rgb(255, 23, 68);
        font-size: 0.875rem;
    }

    .log-date {
        color: #9ca3af;
        font-size: 0.75rem;
    }

    .log-entry-content {
        color: #f9fafb;
        font-size: 0.875rem;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .log-entry-user {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #9ca3af;
        font-size: 0.75rem;
    }

    .log-entry-user i {
        font-size: 0.625rem;
    }

    .no-log-entries {
        text-align: center;
        color: #9ca3af;
        font-style: italic;
        padding: 2rem;
    }

    .btn-link {
        background: none;
        border: none;
        color: rgb(255, 23, 68);
        text-decoration: underline;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0;
    }

    .btn-link:hover {
        color: rgb(255, 50, 90);
    }

    .comparison-section {
        margin-bottom: 2rem;
    }

    .comparison-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 1rem;
        background-color: #2d3748;
        border-radius: 0.5rem;
        border: 1px solid #4a5568;
        transition: all 0.2s ease;
        user-select: none;
    }

    .comparison-header:hover {
        background-color: #374151;
        border-color: rgb(255, 23, 68);
    }

    .comparison-header h4 {
        margin: 0;
        color: rgb(255, 23, 68);
    }

    .toggle-btn {
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 1rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
    }

    .toggle-btn:hover {
        color: rgb(255, 23, 68);
        background-color: rgba(255, 23, 68, 0.1);
    }

    .toggle-btn i {
        transition: transform 0.2s ease;
    }

    .comparison-table-container {
        margin-top: 1rem;
        overflow-x: auto;
        border-radius: 0.5rem;
        border: 1px solid #4a5568;
    }

    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #2d3748;
        color: #f9fafb;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        border: 1px solid #4a5568;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease;
    }

    .toast.loading {
        border-color: rgb(255, 23, 68);
    }

    .toast i {
        font-size: 1rem;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<?php
include __DIR__ . '/htmls/footer.php';
?>
