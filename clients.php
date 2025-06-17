<?php include 'htmls/header.php'; ?>

    <div class="content-area">
        <div class="page-header">
            <h1>ניהול לקוחות</h1>
            <p>נהל את רשימת הלקוחות שלך</p>
        </div>

        <!-- Loading State -->
        <div class="loading-state" id="loading-state" style="display: none;">
            <div class="loading-spinner">
                <i class="fas fa-sync-alt fa-spin"></i>
            </div>
            <p>טוען רשימת לקוחות...</p>
        </div>

        <!-- Error State -->
        <div class="error-state" id="error-state" style="display: none;">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>שגיאה בטעינת הנתונים</h3>
            <p>לא ניתן לטעון את רשימת הלקוחות. אנא נסה שוב.</p>
            <button class="btn btn-primary" onclick="loadClientsData()">נסה שוב</button>
        </div>

        <!-- Empty State -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>אין לקוחות במערכת</h3>
            <p>התחל על ידי הוספת הלקוח הראשון שלך</p>
            <button class="btn btn-primary" id="add-first-client-btn">
                <i class="fas fa-user-plus"></i>
                הוסף לקוח ראשון
            </button>
        </div>

        <!-- Clients Table -->
        <div class="table-container" id="clients-table-container" style="display: none;">
            <div class="table-header">
                <div class="table-info">
                    <span id="clients-count">0 לקוחות</span>
                </div>
                <div class="table-actions">
                    <button class="btn btn-outline btn-sm" onclick="refreshClientsData()">
                        <i class="fas fa-sync-alt"></i>
                        רענן
                    </button>
                    <button class="button primary-button" id="add-client-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>לקוח חדש</span>
                    </button>
                </div>
            </div>
            <table class="clients-table">
                <thead>
                <tr>
                    <th>שם הלקוח</th>
                    <th>תעודת זהות</th>
                    <th>טלפון נייד</th>
                    <th>אימייל</th>
                    <th>תאריך הוספה</th>
                    <th>פעולות</th>
                </tr>
                </thead>
                <tbody id="clients-tbody">
                <!-- Clients will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Client Modal -->
    <div class="modal" id="client-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">לקוח חדש</h3>
                <button class="modal-close" id="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="client-form">
                    <div class="form-group">
                        <label class="form-label">שם מלא *</label>
                        <input type="text" class="form-input" id="client-name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">תעודת זהות *</label>
                        <input type="text" class="form-input" id="client-id-number" maxlength="9" placeholder="9 ספרות" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">טלפון נייד *</label>
                        <input type="tel" class="form-input" id="client-phone" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">כתובת אימייל *</label>
                        <input type="email" class="form-input" id="client-email" required>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-btn">ביטול</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">שמור</button>
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
                    <p>האם אתה בטוח שברצונך למחוק את הלקוח <strong id="delete-client-name"></strong>?</p>
                    <p class="delete-note">פעולה זו אינה ניתנת לביטול.</p>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">ביטול</button>
                    <button type="button" class="btn btn-destructive" id="confirm-delete-btn">מחק לקוח</button>
                </div>
            </div>
        </div>
    </div>

    <script>
      // Global variables
      let clientsData = [];
      let currentEditingClient = null;
      let clientToDelete = null;

      // Initialize page
      document.addEventListener('DOMContentLoaded', function() {
        initializeClientsPage();
      });

      function initializeClientsPage() {
        // Load clients data
        loadClientsData();

        // Initialize event listeners
        initializeEventListeners();
      }

      function initializeEventListeners() {
        // Add client button
        document.getElementById('add-client-btn').addEventListener('click', openAddClientModal);
        document.getElementById('add-first-client-btn').addEventListener('click', openAddClientModal);

        // Modal close buttons
        document.getElementById('close-modal').addEventListener('click', closeClientModal);
        document.getElementById('cancel-btn').addEventListener('click', closeClientModal);

        // Form submission
        document.getElementById('client-form').addEventListener('submit', handleFormSubmit);

        // Search functionality
        const searchInput = document.querySelector('input[placeholder="חיפוש לקוחות..."]');
        if (searchInput) {
          searchInput.addEventListener('input', handleSearch);
        }

        // ID number validation
        document.getElementById('client-id-number').addEventListener('input', function(e) {
          // Only allow digits
          e.target.value = e.target.value.replace(/\D/g, '');
        });
      }

      // Load clients data from API
      async function loadClientsData() {
        showLoadingState();

        try {
          const response = await fetch('https://dev.comparisons/api/ClientsList.php', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
            },
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();

          // Handle the double-array response format: [[{...}, {...}]]
          let clientsArray = [];
          if (Array.isArray(data)) {
            if (data.length > 0 && Array.isArray(data[0])) {
              // Response format: [[{...}, {...}]]
              clientsArray = data[0];
            } else {
              // Response format: [{...}, {...}]
              clientsArray = data;
            }
          } else {
            throw new Error('Invalid data format received');
          }

          clientsData = clientsArray;
          displayClientsData(clientsArray);

        } catch (error) {
          console.error('Error loading clients:', error);
          showErrorState();

          // Show notification
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification('שגיאה בטעינת רשימת הלקוחות', 'error');
          }
        }
      }

      // Display clients data in table
      function displayClientsData(clients) {
        hideAllStates();

        if (clients.length === 0) {
          showEmptyState();
          return;
        }

        // Update clients count
        document.getElementById('clients-count').textContent = `${clients.length} לקוחות`;

        // Populate table
        const tbody = document.getElementById('clients-tbody');
        tbody.innerHTML = '';

        clients.forEach(client => {
          const row = createClientRow(client);
          tbody.appendChild(row);
        });

        // Show table
        document.getElementById('clients-table-container').style.display = 'block';
      }

      // Create a table row for a client
      function createClientRow(client) {
        const row = document.createElement('tr');
        row.dataset.clientId = client.id;

        // Format date - handle if created_at doesn't exist in the response
        const dateAdded = client.created_at ? formatDate(client.created_at) : 'לא זמין';

        row.innerHTML = `
        <td>
          <div class="client-info">
            <div class="client-avatar">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <div class="client-name">${escapeHtml(client.name)}</div>
            </div>
          </div>
        </td>
        <td>${escapeHtml(client.id_number)}</td>
        <td>${escapeHtml(client.phone)}</td>
        <td>${escapeHtml(client.email)}</td>
        <td>${dateAdded}</td>
        <td>
          <div class="action-buttons">
            <button class="btn-icon" title="השוואה חדשה" onclick="createComparison('${client.id}', '${escapeHtml(client.name)}')">
              <i class="fas fa-balance-scale"></i>
            </button>
            <button class="btn-icon" title="עריכה" onclick="editClient('${client.id}')">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon delete" title="מחיקה" onclick="deleteClient('${client.id}', '${escapeHtml(client.name)}')">
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
        document.getElementById('clients-table-container').style.display = 'none';
      }

      // Modal functions
      function openAddClientModal() {
        currentEditingClient = null;
        document.getElementById('modal-title').textContent = 'לקוח חדש';
        document.getElementById('submit-btn').textContent = 'הוסף לקוח';
        document.getElementById('client-form').reset();
        document.getElementById('client-modal').style.display = 'block';
      }

      function closeClientModal() {
        document.getElementById('client-modal').style.display = 'none';
        currentEditingClient = null;
      }

      // Form submission
      async function handleFormSubmit(e) {
        e.preventDefault();

        const formData = {
          name: document.getElementById('client-name').value.trim(),
          id_number: document.getElementById('client-id-number').value.trim(),
          phone: document.getElementById('client-phone').value.trim(),
          email: document.getElementById('client-email').value.trim(),
        };

        // Validate form
        if (!validateClientForm(formData)) {
          return;
        }

        // Show loading state on button
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...';

        try {
          if (currentEditingClient) {
            await updateClient(currentEditingClient.id, formData);
          } else {
            await addNewClient(formData);
          }

          // Close modal and refresh data
          closeClientModal();
          await loadClientsData();

          // Show success message
          const message = currentEditingClient ? 'הלקוח עודכן בהצלחה!' : 'הלקוח נוסף בהצלחה!';
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification(message, 'success');
          }

        } catch (error) {
          console.error('Error saving client:', error);

          // Show error message
          const message = currentEditingClient ? 'שגיאה בעדכון הלקוח' : 'שגיאה בהוספת הלקוח';
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification(message, 'error');
          }
        } finally {
          // Reset button
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      }

      // Add new client
      async function addNewClient(clientData) {
        const response = await fetch('https://dev.comparisons/api/AddClient.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
          },
          body: JSON.stringify(clientData),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
      }

      // Update existing client (if you have an update API)
      async function updateClient(clientId, clientData) {
        const response = await fetch(`https://dev.comparisons/api/UpdateClient.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
          },
          body: JSON.stringify({
            id: clientId,
            ...clientData,
          }),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
      }

      // Edit client
      function editClient(clientId) {
        const client = clientsData.find(c => c.id == clientId);
        if (!client) {
          alert('לקוח לא נמצא');
          return;
        }

        currentEditingClient = client;
        document.getElementById('modal-title').textContent = 'עריכת לקוח';
        document.getElementById('submit-btn').textContent = 'עדכן לקוח';

        // Populate form
        document.getElementById('client-name').value = client.name;
        document.getElementById('client-id-number').value = client.id_number;
        document.getElementById('client-phone').value = client.phone;
        document.getElementById('client-email').value = client.email;

        document.getElementById('client-modal').style.display = 'block';
      }

      // Delete client
      function deleteClient(clientId, clientName) {
        clientToDelete = {id: clientId, name: clientName};
        document.getElementById('delete-client-name').textContent = clientName;
        document.getElementById('delete-modal').style.display = 'block';

        // Set up delete confirmation
        document.getElementById('confirm-delete-btn').onclick = async function() {
          await confirmDeleteClient();
        };
      }

      async function confirmDeleteClient() {
        if (!clientToDelete) return;

        const deleteBtn = document.getElementById('confirm-delete-btn');
        const originalText = deleteBtn.textContent;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מוחק...';

        try {
          const response = await fetch('https://dev.comparisons/api/DeleteClient.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
            },
            body: JSON.stringify({id: clientToDelete.id}),
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          // Close modal and refresh data
          closeDeleteModal();
          await loadClientsData();

          // Show success message
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification('הלקוח נמחק בהצלחה!', 'success');
          }

        } catch (error) {
          console.error('Error deleting client:', error);

          // Show error message
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification('שגיאה במחיקת הלקוח', 'error');
          }
        } finally {
          // Reset button
          deleteBtn.disabled = false;
          deleteBtn.textContent = originalText;
        }
      }

      function closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
        clientToDelete = null;
      }

      // Create comparison for client
      function createComparison(clientId, clientName) {
        window.location.href = `comparison.php?client=${encodeURIComponent(clientId)}`;
      }

      // Search functionality
      function handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase().trim();

        if (searchTerm === '') {
          displayClientsData(clientsData);
          return;
        }

        const filteredClients = clientsData.filter(client => {
          return client.name.toLowerCase().includes(searchTerm) ||
              client.phone.includes(searchTerm) ||
              client.email.toLowerCase().includes(searchTerm) ||
              client.id_number.includes(searchTerm);
        });

        displayClientsData(filteredClients);
      }

      // Refresh data
      function refreshClientsData() {
        loadClientsData();
      }

      // Form validation
      function validateClientForm(formData) {
        // Check required fields
        if (!formData.name || !formData.id_number || !formData.phone || !formData.email) {
          alert('אנא מלא את כל השדות הנדרשים');
          return false;
        }

        // Validate ID number (9 digits)
        if (!/^\d{9}$/.test(formData.id_number)) {
          alert('תעודת זהות חייבת להכיל 9 ספרות');
          return false;
        }

        // Check for duplicate ID number (excluding current editing client)
        const existingClient = clientsData.find(client =>
            client.id_number === formData.id_number &&
            (!currentEditingClient || client.id !== currentEditingClient.id),
        );

        if (existingClient) {
          alert('תעודת זהות זו כבר קיימת במערכת');
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

      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // Close modals when clicking outside
      window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
          if (e.target.id === 'client-modal') {
            closeClientModal();
          } else if (e.target.id === 'delete-modal') {
            closeDeleteModal();
          }
        }
      });
    </script>

    <style>
        /* Additional styles for the clients page */
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
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
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

        /* Responsive improvements */
        @media (max-width: 768px) {
            .table-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .clients-table {
                font-size: 0.875rem;
            }

            .clients-table th,
            .clients-table td {
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
        }

        @media (max-width: 640px) {
            .clients-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
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

        /* Table improvements */
        .clients-table tbody tr:hover {
            background-color: #2d3748;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .client-info .client-avatar {
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

        .client-name {
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
    </style>

<?php include 'htmls/footer.php'; ?>