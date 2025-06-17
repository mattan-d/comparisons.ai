<?php

$header_action='<button class="button primary-button" id="add-user-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>משתמש חדש</span>
                </button>';

include __DIR__ . '/htmls/header.php';
?>

<div class="content-area">
    <div class="page-header">
        <h1>ניהול משתמשים</h1>
        <p>הוספה, עריכה ומחיקה של משתמשים במערכת</p>
    </div>

    <!-- Loading State -->
    <div class="loading-state" id="loading-state" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
        </div>
        <p>טוען רשימת משתמשים...</p>
    </div>

    <!-- Error State -->
    <div class="error-state" id="error-state" style="display: none;">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>שגיאה בטעינת הנתונים</h3>
        <p>לא ניתן לטעון את רשימת המשתמשים. אנא נסה שוב.</p>
        <button class="btn btn-primary" onclick="loadUsersData()">נסה שוב</button>
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="empty-state" style="display: none;">
        <div class="empty-icon">
            <i class="fas fa-user-cog"></i>
        </div>
        <h3>אין משתמשים במערכת</h3>
        <p>התחל על ידי הוספת המשתמש הראשון</p>
        <button class="btn btn-primary" id="add-first-user-btn">
            <i class="fas fa-user-plus"></i>
            הוסף משתמש ראשון
        </button>
    </div>

    <!-- Users Table -->
    <div class="table-container" id="users-table-container" style="display: none;">
        <div class="table-header">
            <div class="table-info">
                <span id="users-count">0 משתמשים</span>
                <button class="btn btn-primary btn-sm table-add-user-btn" id="table-add-user-btn">
                    <i class="fas fa-user-plus"></i>
                    הוסף משתמש חדש
                </button>
            </div>
            <div class="table-actions">
                <div class="filter-buttons">
                    <button class="filter-button active" data-filter="all">הכל</button>
                    <button class="filter-button" data-filter="active">פעילים</button>
                    <button class="filter-button" data-filter="inactive">לא פעילים</button>
                </div>
                <button class="btn btn-outline btn-sm" onclick="refreshUsersData()">
                    <i class="fas fa-sync-alt"></i>
                    רענן
                </button>
            </div>
        </div>
        <table class="users-table">
            <thead>
            <tr>
                <th>שם המשתמש</th>
                <th>אימייל</th>
                <th>טלפון</th>
                <th>תפקיד</th>
                <th>סטטוס</th>
                <th>תאריך הוספה</th>
                <th>פעולות</th>
            </tr>
            </thead>
            <tbody id="users-tbody">
            <!-- Users will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal" id="user-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">משתמש חדש</h3>
            <button class="modal-close" id="close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="user-form">
                <input type="hidden" id="user-id">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">שם מלא *</label>
                        <input type="text" class="form-input" id="user-name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">כתובת אימייל *</label>
                        <input type="email" class="form-input" id="user-email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">מספר טלפון *</label>
                        <input type="tel" class="form-input" id="user-phone" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">סיסמה</label>
                        <input type="password" class="form-input" id="user-password" placeholder="השאר ריק לשמירת הסיסמה הקיימת">
                        <div class="form-hint" id="password-hint">סיסמה חדשה תישלח למשתמש באימייל</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">תפקיד *</label>
                        <select class="form-select" id="user-role" required>
                            <option value="">בחר תפקיד</option>
                            <option value="agent">סוכן</option>
                            <option value="manager">מנהל</option>
                            <option value="admin">מנהל מערכת</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">סטטוס *</label>
                        <select class="form-select" id="user-status" required>
                            <option value="active">פעיל</option>
                            <option value="inactive">לא פעיל</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">הערות</label>
                    <textarea class="form-textarea" id="user-notes" rows="3" placeholder="הערות פנימיות על המשתמש"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancel-btn">ביטול</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">הוסף משתמש</button>
                </div>
            </form>
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
                <p>האם אתה בטוח שברצונך למחוק את המשתמש <strong id="delete-user-name"></strong>?</p>
                <p class="delete-note">פעולה זו אינה ניתנת לביטול.</p>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">ביטול</button>
                <button type="button" class="btn btn-destructive" id="confirm-delete-btn">מחק משתמש</button>
            </div>
        </div>
    </div>
</div>

<script src="js/main.js"></script>
<script>
  // Global variables
  let usersData = [];
  let currentEditingUser = null;
  let userToDelete = null;

  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    initializeUsersPage();
  });

  function initializeUsersPage() {
    // Load users data
    loadUsersData();

    // Initialize event listeners
    initializeEventListeners();
  }

  function initializeEventListeners() {
    // Add user button
    document.getElementById('add-user-btn').addEventListener('click', openAddUserModal);
    document.getElementById('add-first-user-btn').addEventListener('click', openAddUserModal);

    // Table add user button
    document.getElementById('table-add-user-btn').addEventListener('click', openAddUserModal);

    // Modal close buttons
    document.getElementById('close-modal').addEventListener('click', closeUserModal);
    document.getElementById('cancel-btn').addEventListener('click', closeUserModal);

    // Form submission
    document.getElementById('user-form').addEventListener('submit', handleFormSubmit);

    // Search functionality
    document.getElementById('user-search').addEventListener('input', handleSearch);

    // Filter buttons
    document.querySelectorAll('.filter-button').forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');

        // Apply filter
        const filter = this.dataset.filter;
        filterUsers(filter);
      });
    });
  }

  // Load users data from API
  async function loadUsersData() {
    showLoadingState();

    try {
      const response = await fetch('../..//api/UsersList.php', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const responseText = await response.text();
      const decodedText = responseText.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

      const data = JSON.parse(decodedText);

      // Handle the response format
      let usersArray = [];
      if (Array.isArray(data)) {
        if (data.length > 0 && Array.isArray(data[0])) {
          // Response format: [[{...}, {...}]]
          usersArray = data[0];
        } else {
          // Response format: [{...}, {...}]
          usersArray = data;
        }
      } else {
        throw new Error('Invalid data format received');
      }

      usersData = usersArray;
      displayUsersData(usersArray);

    } catch (error) {
      console.error('Error loading users:', error);
      showErrorState();

      // Show notification
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה בטעינת רשימת המשתמשים', 'error');
      }
    }
  }

  // Display users data in table
  function displayUsersData(users) {
    hideAllStates();

    if (users.length === 0) {
      showEmptyState();
      return;
    }

    // Update users count
    document.getElementById('users-count').textContent = `${users.length} משתמשים`;

    // Populate table
    const tbody = document.getElementById('users-tbody');
    tbody.innerHTML = '';

    users.forEach(user => {
      const row = createUserRow(user);
      tbody.appendChild(row);
    });

    // Show table
    document.getElementById('users-table-container').style.display = 'block';
  }

  // Create a table row for a user
  function createUserRow(user) {
    const row = document.createElement('tr');
    row.dataset.userId = user.id;
    row.dataset.status = user.status || 'active';

    // Format date
    const dateAdded = user.created_at ? formatDate(user.created_at) : 'לא זמין';

    // Get role text
    const roleText = getRoleText(user.role);
    const roleClass = getRoleClass(user.role);

    // Get status text
    const statusText = getStatusText(user.status);
    const statusClass = getStatusClass(user.status);

    row.innerHTML = `
        <td>
          <div class="user-info">
            <div class="user-avatar">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <div class="user-name">${escapeHtml(user.name)}</div>
            </div>
          </div>
        </td>
        <td>${escapeHtml(user.email)}</td>
        <td>${escapeHtml(user.phone)}</td>
        <td>
          <span class="role-badge ${roleClass}">${roleText}</span>
        </td>
        <td>
          <span class="status-badge ${statusClass}">${statusText}</span>
        </td>
        <td>${dateAdded}</td>
        <td>
          <div class="action-buttons">
            <button class="btn-icon" title="עריכה" onclick="editUser('${user.id}')">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon" title="שינוי סטטוס" onclick="toggleUserStatus('${user.id}')">
              <i class="fas fa-toggle-${user.status === 'inactive' ? 'off' : 'on'}"></i>
            </button>
            <button class="btn-icon delete" title="מחיקה" onclick="deleteUser('${user.id}', '${escapeHtml(user.name)}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      `;

    return row;
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
    document.getElementById('users-table-container').style.display = 'none';
  }

  // Modal functions
  function openAddUserModal() {
    currentEditingUser = null;
    document.getElementById('modal-title').textContent = 'משתמש חדש';
    document.getElementById('submit-btn').textContent = 'הוסף משתמש';
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-hint').textContent = 'סיסמה חדשה תישלח למשתמש באימייל';
    document.getElementById('user-password').placeholder = 'הזן סיסמה ראשונית';
    document.getElementById('user-password').required = true;
    document.getElementById('user-modal').style.display = 'block';
  }

  function closeUserModal() {
    document.getElementById('user-modal').style.display = 'none';
    currentEditingUser = null;
  }

  // Form submission
  async function handleFormSubmit(e) {
    e.preventDefault();

    const formData = {
      name: document.getElementById('user-name').value.trim(),
      email: document.getElementById('user-email').value.trim(),
      phone: document.getElementById('user-phone').value.trim(),
      role: document.getElementById('user-role').value,
      status: document.getElementById('user-status').value,
      notes: document.getElementById('user-notes').value.trim(),
    };

    // Add password only if provided
    const password = document.getElementById('user-password').value.trim();
    if (password) {
      formData.password = password;
    }

    // Add ID if editing
    if (currentEditingUser) {
      formData.id = document.getElementById('user-id').value;
    }

    // Validate form
    if (!validateUserForm(formData)) {
      return;
    }

    // Show loading state on button
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...';

    try {
      if (currentEditingUser) {
        await updateUser(formData);
      } else {
        await addNewUser(formData);
      }

      // Close modal and refresh data
      closeUserModal();
      await loadUsersData();

      // Show success message
      const message = currentEditingUser ? 'המשתמש עודכן בהצלחה!' : 'המשתמש נוסף בהצלחה!';
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification(message, 'success');
      }

    } catch (error) {
      console.error('Error saving user:', error);

      // Show error message
      const message = currentEditingUser ? 'שגיאה בעדכון המשתמש' : 'שגיאה בהוספת המשתמש';
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification(message, 'error');
      }
    } finally {
      // Reset button
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  }

  // Add new user
  async function addNewUser(userData) {
    const response = await fetch('../..//api/AddUser.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
      body: JSON.stringify(userData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  // Update existing user
  async function updateUser(userData) {
    const response = await fetch('../..//api/UpdateUser.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
      body: JSON.stringify(userData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  // Edit user
  function editUser(userId) {
    const user = usersData.find(u => u.id == userId);
    if (!user) {
      alert('משתמש לא נמצא');
      return;
    }

    currentEditingUser = user;
    document.getElementById('modal-title').textContent = 'עריכת משתמש';
    document.getElementById('submit-btn').textContent = 'עדכן משתמש';

    // Populate form
    document.getElementById('user-id').value = user.id;
    document.getElementById('user-name').value = user.name || '';
    document.getElementById('user-email').value = user.email || '';
    document.getElementById('user-phone').value = user.phone || '';
    document.getElementById('user-role').value = user.role || '';
    document.getElementById('user-status').value = user.status || 'active';
    document.getElementById('user-notes').value = user.notes || '';

    // Password field is optional when editing
    document.getElementById('user-password').required = false;
    document.getElementById('user-password').value = '';
    document.getElementById('user-password').placeholder = 'השאר ריק לשמירת הסיסמה הקיימת';
    document.getElementById('password-hint').textContent = 'מלא רק אם ברצונך לשנות את הסיסמה';

    document.getElementById('user-modal').style.display = 'block';
  }

  // Toggle user status
  async function toggleUserStatus(userId) {
    const user = usersData.find(u => u.id == userId);
    if (!user) {
      alert('משתמש לא נמצא');
      return;
    }

    const newStatus = user.status === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'פעיל' : 'לא פעיל';

    if (confirm(`האם אתה בטוח שברצונך לשנות את סטטוס המשתמש ${user.name} ל${statusText}?`)) {
      try {
        await updateUser({
          id: userId,
          status: newStatus,
        });

        // Update local data
        user.status = newStatus;

        // Update UI
        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
        if (row) {
          row.dataset.status = newStatus;
          const statusBadge = row.querySelector('.status-badge');
          statusBadge.textContent = getStatusText(newStatus);
          statusBadge.className = `status-badge ${getStatusClass(newStatus)}`;

          // Update toggle button
          const toggleBtn = row.querySelector('[title="שינוי סטטוס"] i');
          toggleBtn.className = `fas fa-toggle-${newStatus === 'inactive' ? 'off' : 'on'}`;
        }

        // Show success message
        if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
          window.InsurancePlatform.showNotification(`סטטוס המשתמש שונה ל${statusText} בהצלחה!`, 'success');
        }

      } catch (error) {
        console.error('Error updating user status:', error);

        // Show error message
        if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
          window.InsurancePlatform.showNotification('שגיאה בעדכון סטטוס המשתמש', 'error');
        }
      }
    }
  }

  // Delete user
  function deleteUser(userId, userName) {
    userToDelete = {id: userId, name: userName};
    document.getElementById('delete-user-name').textContent = userName;
    document.getElementById('delete-modal').style.display = 'block';

    // Set up delete confirmation
    document.getElementById('confirm-delete-btn').onclick = async function() {
      await confirmDeleteUser();
    };
  }

  async function confirmDeleteUser() {
    if (!userToDelete) return;

    const deleteBtn = document.getElementById('confirm-delete-btn');
    const originalText = deleteBtn.textContent;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מוחק...';

    try {
      const response = await fetch('../..//api/DeleteUser.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
        body: JSON.stringify({id: userToDelete.id}),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Close modal and refresh data
      closeDeleteModal();
      await loadUsersData();

      // Show success message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('המשתמש נמחק בהצלחה!', 'success');
      }

    } catch (error) {
      console.error('Error deleting user:', error);

      // Show error message
      if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
        window.InsurancePlatform.showNotification('שגיאה במחיקת המשתמש', 'error');
      }
    } finally {
      // Reset button
      deleteBtn.disabled = false;
      deleteBtn.textContent = originalText;
    }
  }

  function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
    userToDelete = null;
  }

  // Search functionality
  function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase().trim();

    if (searchTerm === '') {
      // Apply current filter
      const activeFilter = document.querySelector('.filter-button.active').dataset.filter;
      filterUsers(activeFilter);
      return;
    }

    const filteredUsers = usersData.filter(user => {
      return user.name.toLowerCase().includes(searchTerm) ||
          user.email.toLowerCase().includes(searchTerm) ||
          user.phone.includes(searchTerm);
    });

    displayUsersData(filteredUsers);
  }

  // Filter users
  function filterUsers(filter) {
    if (filter === 'all') {
      displayUsersData(usersData);
      return;
    }

    const filteredUsers = usersData.filter(user => user.status === filter);
    displayUsersData(filteredUsers);
  }

  // Refresh data
  function refreshUsersData() {
    loadUsersData();
  }

  // Form validation
  function validateUserForm(formData) {
    // Check required fields
    if (!formData.name || !formData.email || !formData.phone || !formData.role || !formData.status) {
      alert('אנא מלא את כל השדות הנדרשים');
      return false;
    }

    // Check if new user and password is required
    if (!currentEditingUser && !formData.password) {
      alert('אנא הזן סיסמה ראשונית למשתמש החדש');
      return false;
    }

    // Validate email
    if (!window.InsurancePlatform || !window.InsurancePlatform.validateEmail(formData.email)) {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        alert('כתובת האימייל אינה תקינה');
        return false;
      }
    }

    // Validate phone
    if (!window.InsurancePlatform || !window.InsurancePlatform.validatePhone(formData.phone)) {
      if (!/^0\d{1,2}-?\d{7}$/.test(formData.phone.replace(/\s/g, ''))) {
        alert('מספר הטלפון אינו תקין');
        return false;
      }
    }

    // Check for duplicate email (excluding current editing user)
    const existingUser = usersData.find(user =>
        user.email === formData.email &&
        (!currentEditingUser || user.id !== currentEditingUser.id),
    );

    if (existingUser) {
      alert('כתובת האימייל כבר קיימת במערכת');
      return false;
    }

    return true;
  }

  // Utility functions
  function formatDate(dateString) {
    try {
      // Handle both string and numeric timestamps
      if (!dateString) return 'לא זמין';

      // Convert string timestamp to number if needed
      const timestampValue = typeof dateString === 'string' ?
          isNaN(Number(dateString)) ? Date.parse(dateString) : Number(dateString) :
          dateString;

      // Create date from timestamp
      const date = new Date(timestampValue * 1000); // Multiply by 1000 if it's a Unix timestamp (seconds)

      // Check if date is valid
      if (isNaN(date.getTime())) {
        return 'לא זמין';
      }

      // Format date according to Israeli locale
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

  function getRoleText(role) {
    const roleMap = {
      'agent': 'סוכן',
      'manager': 'מנהל',
      'admin': 'מנהל מערכת',
    };
    return roleMap[role] || role || 'משתמש';
  }

  function getRoleClass(role) {
    const roleClassMap = {
      'agent': 'role-agent',
      'manager': 'role-manager',
      'admin': 'role-admin',
    };
    return roleClassMap[role] || '';
  }

  function getStatusText(status) {
    return status === 'inactive' ? 'לא פעיל' : 'פעיל';
  }

  function getStatusClass(status) {
    return status === 'inactive' ? 'inactive' : 'active';
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
      if (e.target.id === 'user-modal') {
        closeUserModal();
      } else if (e.target.id === 'delete-modal') {
        closeDeleteModal();
      }
    }
  });
</script>

<style>
    /* Additional styles for the admin users page */
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

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background-color: #2d3748;
        border-bottom: 1px solid #374151;
    }

    .table-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .table-add-user-btn {
        background-color: rgb(255, 23, 68);
        color: white;
        border: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .table-add-user-btn:hover {
        background-color: rgba(255, 23, 68, 0.8);
    }

    .table-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .filter-button {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #1f2937;
        border: 1px solid #374151;
        color: #9ca3af;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-button:hover {
        background-color: #374151;
        color: #f9fafb;
    }

    .filter-button.active {
        background-color: rgb(255, 23, 68);
        color: white;
        border-color: rgb(255, 23, 68);
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

    /* Role badges */
    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .role-agent {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .role-manager {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }

    .role-admin {
        background-color: rgba(255, 23, 68, 0.1);
        color: rgb(255, 23, 68);
    }

    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-badge.inactive {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    /* Table improvements */
    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th,
    .users-table td {
        padding: 1rem 1.5rem;
        text-align: right;
        border-bottom: 1px solid #374151;
    }

    .users-table th {
        background-color: #2d3748;
        color: #9ca3af;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .users-table tr:hover {
        background-color: #2d3748;
    }

    .users-table tr:last-child td {
        border-bottom: none;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-info .user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background-color: rgba(255, 23, 68, 0.1);
        color: rgb(255, 23, 68);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 500;
        color: #f9fafb;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
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
        font-size: 0.875rem;
    }

    .btn-icon:hover {
        background-color: #374151;
        color: #f9fafb;
    }

    .btn-icon.delete:hover {
        background-color: #ef4444;
        color: white;
        border-color: #ef4444;
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
    .form-textarea,
    .form-select {
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
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: rgb(255, 23, 68);
        box-shadow: 0 0 0 3px rgba(255, 23, 68, 0.2);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
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

    /* Responsive design */
    @media (max-width: 1024px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .form-row .form-group {
            margin-bottom: 1.5rem;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .table-actions {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .users-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .users-table th,
        .users-table td {
            padding: 0.75rem 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn-icon {
            width: 1.75rem;
            height: 1.75rem;
        }

        .filter-buttons {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 640px) {
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
</style>

<?php
include __DIR__ . '/htmls/footer.php';
?>
