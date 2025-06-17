<?php
include __DIR__ . '/htmls/header.php';
?>

    <div class="content-area">
        <div class="page-header">
            <h1>השוואת פוליסות</h1>
            <p>צור השוואה חדשה עבור לקוח</p>
        </div>

        <div class="comparison-wizard full-width">
            <!-- Step 1: Client Selection -->
            <div class="wizard-step active" id="step-1">
                <div class="step-header">
                    <h2>שלב 1: בחירת לקוח</h2>
                    <div class="step-indicator">1 מתוך 2</div>
                </div>

                <div class="client-selection">
                    <div class="selection-options">
                        <label class="radio-option">
                            <input type="radio" name="client-type" value="existing" checked>
                            <span class="radio-label">לקוח קיים</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="client-type" value="new">
                            <span class="radio-label">לקוח חדש</span>
                        </label>
                    </div>

                    <div class="client-form existing-client">
                        <div class="form-group">
                            <label class="form-label">בחר לקוח</label>
                            <select class="form-select" id="existing-client-select">
                                <option value="">בחר לקוח מהרשימה</option>
                            </select>
                        </div>
                    </div>

                    <div class="client-form new-client" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">שם מלא *</label>
                                <input type="text" class="form-input" id="new-client-name">
                            </div>
                            <div class="form-group">
                                <label class="form-label">תעודת זהות *</label>
                                <input type="text" class="form-input" id="new-client-id" maxlength="9" placeholder="9 ספרות">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">טלפון *</label>
                                <input type="tel" class="form-input" id="new-client-phone">
                            </div>
                            <div class="form-group">
                                <label class="form-label">אימייל *</label>
                                <input type="email" class="form-input" id="new-client-email">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-actions">
                    <button class="btn btn-primary" onclick="nextStep(2)">המשך</button>
                </div>
            </div>

            <!-- Step 2: Policy Details (previously Step 3) -->
            <div class="wizard-step" id="step-2">
                <div class="step-header">
                    <h2>שלב 2: פרטי הפוליסה</h2>
                    <div class="step-indicator">2 מתוך 2</div>
                </div>

                <!-- Travel Insurance Form -->
                <div class="policy-form travel-form">
                    <div class="form-section">
                        <h3>פרטי הנסיעה</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">יעד הנסיעה *</label>
                                <div class="autocomplete-container">
                                    <input type="text" class="form-input" id="travel-destination" placeholder="הקלד שם המדינה..."
                                           required autocomplete="off">
                                    <div class="autocomplete-dropdown" id="destination-dropdown"></div>
                                </div>
                                <div class="destination-info" id="destination-info" style="display: none;"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">מספר נוסעים *</label>
                                <select class="form-select" id="travelers-count" required>
                                    <option value="">בחר מספר</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5+</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">תאריך יציאה *</label>
                                <input type="date" class="form-input" id="departure-date" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">תאריך חזרה *</label>
                                <input type="date" class="form-input" id="return-date" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">גיל הנוסע הבוגר ביותר *</label>
                            <input type="number" class="form-input" id="oldest-traveler-age" placeholder="גיל" min="0" max="120"
                                   required>
                            <div class="age-restrictions">
                                <div class="age-restrictions-title">מגבלות גיל:</div>
                                <table class="age-restrictions-table">
                                    <tr>
                                        <td>עד גיל 65</td>
                                        <td>כיסוי מלא</td>
                                    </tr>
                                    <tr>
                                        <td>66-85</td>
                                        <td>נדרשת הצהרת בריאות</td>
                                    </tr>
                                    <tr>
                                        <td>85+</td>
                                        <td>נדרש אישור מיוחד</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Essential Coverage -->
                    <div class="form-section">
                        <h3>כיסויים הכרחיים</h3>
                        <p class="coverage-description">כיסויים אלה נכללים בכל פוליסה</p>

                        <div class="coverage-grid">
                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage"
                                           value="medical-expenses" checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">הוצאות רפואיות</span>
                                </label>
                            </div>

                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage"
                                           value="third-party-liability" checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">חבות כלפי צד ג'</span>
                                </label>
                            </div>

                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage"
                                           value="trip-cancellation" checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">ביטול נסיעה</span>
                                </label>
                            </div>

                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage"
                                           value="trip-shortening" checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">קיצור נסיעה</span>
                                </label>
                            </div>

                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage" value="luggage"
                                           checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">כבודה</span>
                                </label>
                            </div>

                            <div class="coverage-item essential">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="essential-coverage" value="search-rescue"
                                           checked disabled>
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">איתור, חיפוש וחילוץ</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Coverage -->
                    <div class="form-section">
                        <h3>כיסויים נוספים</h3>
                        <p class="coverage-description">בחר כיסויים נוספים בהתאם לצורך הלקוח</p>

                        <div class="coverage-grid">
                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage"
                                           value="dental-emergency">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">טיפול חירום בשיניים</span>
                                </label>
                            </div>

                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage"
                                           value="medical-israel">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">הוצאות רפואיות בישראל</span>
                                </label>
                            </div>

                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="pregnancy">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">הריון לא בסיכון</span>
                                </label>
                            </div>

                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="rental-car">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">ביטול השתתפות עצמית לרכב שכור</span>
                                </label>
                            </div>

                            <div class="coverage-item optional with-days">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="extreme-sports"
                                           onchange="toggleDaysInput(this, 'extreme-days')">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">פעילות אתגרית</span>
                                </label>
                                <div class="days-input" id="extreme-days" style="display: none;">
                                    <label class="form-label">מספר ימי פעילות:</label>
                                    <input type="number" class="form-input" name="extreme-sports-days" min="1" placeholder="ימים">
                                </div>
                            </div>

                            <div class="coverage-item optional with-days">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="winter-sports"
                                           onchange="toggleDaysInput(this, 'winter-days')">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">ספורט חורף</span>
                                </label>
                                <div class="days-input" id="winter-days" style="display: none;">
                                    <label class="form-label">מספר ימי פעילות:</label>
                                    <input type="number" class="form-input" name="winter-sports-days" min="1" placeholder="ימים">
                                </div>
                            </div>

                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="electronics">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">מחשב נייד / טלפון / טאבלט</span>
                                </label>
                            </div>

                            <div class="coverage-item optional">
                                <label class="checkbox-label">
                                    <input type="checkbox" class="coverage-checkbox" name="optional-coverage" value="camera">
                                    <span class="checkmark"></span>
                                    <span class="coverage-name">מצלמה</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Companies Selection -->
                    <div class="form-section">
                        <h3>בחירת חברות ביטוח</h3>
                        <p class="coverage-description">בחר את חברות הביטוח להשוואה</p>

                        <div class="companies-section">
                            <h4>חברות ביטוח רגילות</h4>
                            <div class="companies-grid">
                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="regular-companies" value="clal">
                                        <span class="checkmark"></span>
                                        <span class="company-name">כלל</span>
                                    </label>
                                </div>

                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="regular-companies" value="phoenix">
                                        <span class="checkmark"></span>
                                        <span class="company-name">הפניקס</span>
                                    </label>
                                </div>

                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="regular-companies" value="harel">
                                        <span class="checkmark"></span>
                                        <span class="company-name">הראל</span>
                                    </label>
                                </div>

                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="regular-companies"
                                               value="passportcard">
                                        <span class="checkmark"></span>
                                        <span class="company-name">פספורטכארד</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="companies-section">
                            <h4>חברות ביטוח ישירות</h4>
                            <div class="companies-grid">
                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="direct-companies" value="aig">
                                        <span class="checkmark"></span>
                                        <span class="company-name">AIG</span>
                                    </label>
                                </div>

                                <div class="company-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" class="company-checkbox" name="direct-companies" value="maccabi">
                                        <span class="checkmark"></span>
                                        <span class="company-name">מכבי</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-actions">
                    <button class="btn btn-secondary" onclick="prevStep(1)">חזור</button>
                    <button class="btn btn-primary" onclick="generateComparison()">צור השוואה</button>
                </div>
            </div>
        </div>
        <!-- Results Section -->
        <div class="comparison-results" id="comparison-results" style="display: none;">
            <div class="results-header">
                <h2>תוצאות השוואה</h2>
                <button class="btn btn-secondary" onclick="backToForm()">
                    <i class="fas fa-arrow-right ml-2"></i>
                    חזרה לטופס
                </button>
            </div>

            <div class="recommendation-box">
                <div class="recommendation-header">
                    <i class="fas fa-lightbulb"></i>
                    <h3>המלצת המערכת</h3>
                </div>
                <p id="recommendation-text"></p>
            </div>

            <div class="companies-comparison">
                <div class="companies-tabs" id="companies-tabs">
                    <!-- Company tabs will be generated here -->
                </div>

                <div class="comparison-tables" id="comparison-tables">
                    <!-- Comparison tables will be generated here -->
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loading-overlay">
            <div class="loading-content">
                <div class="loading-spinner">
                    <i class="fas fa-sync-alt fa-spin"></i>
                </div>
                <h3>מעבד את הנתונים...</h3>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progress-bar"></div>
                </div>
                <p id="loading-status">מתחבר לשרת...</p>
            </div>
        </div>
    </div>

    <script>
      let selectedInsuranceType = 'travel'; // Always travel insurance

      // Client type selection
      document.querySelectorAll('input[name="client-type"]').forEach(radio => {
        radio.addEventListener('change', function() {
          const existingForm = document.querySelector('.existing-client');
          const newForm = document.querySelector('.new-client');

          if (this.value === 'existing') {
            existingForm.style.display = 'block';
            newForm.style.display = 'none';
          } else {
            existingForm.style.display = 'none';
            newForm.style.display = 'block';
          }
        });
      });

      function toggleDaysInput(checkbox, targetId) {
        const daysInput = document.getElementById(targetId);
        if (checkbox.checked) {
          daysInput.style.display = 'block';
          daysInput.querySelector('input').required = true;
        } else {
          daysInput.style.display = 'none';
          daysInput.querySelector('input').required = false;
          daysInput.querySelector('input').value = '';
        }
      }

      function nextStep(stepNumber) {
        // Hide current step
        document.querySelector('.wizard-step.active').classList.remove('active');
        // Show next step
        document.getElementById(`step-${stepNumber}`).classList.add('active');

        // Always show travel form in step 2
        if (stepNumber === 2) {
          document.querySelector('.travel-form').style.display = 'block';
        }
      }

      function prevStep(stepNumber) {
        // Hide current step
        document.querySelector('.wizard-step.active').classList.remove('active');
        // Show previous step
        document.getElementById(`step-${stepNumber}`).classList.add('active');
      }

      async function sendComparisonData(formData) {
        try {
          // Show loading overlay
          const loadingOverlay = document.getElementById('loading-overlay');
          const progressBar = document.getElementById('progress-bar');
          const loadingStatus = document.getElementById('loading-status');

          loadingOverlay.classList.add('active');

          // Simulate progress
          let progress = 0;
          const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.style.width = `${progress}%`;

            if (progress < 30) {
              loadingStatus.textContent = 'מתחבר לשרת...';
            } else if (progress < 60) {
              loadingStatus.textContent = 'מעבד נתונים...';
            } else {
              loadingStatus.textContent = 'מקבל תוצאות השוואה...';
            }
          }, 500);

          // API endpoint - replace with your actual API URL
          const apiUrl = 'https://dev.comparisons/api/comparisons.php';

          const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
            },
            body: JSON.stringify(formData),
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          // Get the actual API response
          const result = await response.json();

          // Complete the progress bar
          clearInterval(progressInterval);
          progressBar.style.width = '100%';
          loadingStatus.textContent = 'הושלם!';

          // Wait a moment before showing results
          setTimeout(() => {
            // Hide loading overlay
            loadingOverlay.classList.remove('active');

            // Display the results
            displayComparisonResults(result);

            // Hide the wizard and show the results
            document.querySelector('.comparison-wizard').style.display = 'none';
            document.getElementById('comparison-results').style.display = 'block';

            // Scroll to top of results
            window.scrollTo({top: 0, behavior: 'smooth'});
          }, 1000);

        } catch (error) {
          console.error('Error sending comparison data:', error);

          // Hide loading overlay
          document.getElementById('loading-overlay').classList.remove('active');

          // Show error message
          InsurancePlatform.showNotification(
              'שגיאה ביצירת ההשוואה. אנא נסה שוב.',
              'error',
          );
        }
      }

      // Find the section where comparison results are displayed and update the date formatting
      // This would be in the displayComparisonResults function or similar

      // Add this formatDate function to handle timestamps properly
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

      // Update the displayComparisonResults function to use the formatDate function
      // Look for any place where dates are displayed and update them to use formatDate
      // For example, if there's a date field in the comparison data:

      // In the displayComparisonResults function, add formatting for any date fields
      function displayComparisonResults(data) {
        // Set recommendation text
        document.getElementById('recommendation-text').textContent = data.recommendation;

        // Format creation date if it exists
        if (data.created_at) {
          const formattedDate = formatDate(data.created_at);
          // If there's a place to display the creation date, update it
          if (document.getElementById('comparison-date')) {
            document.getElementById('comparison-date').textContent = formattedDate;
          }
        }

        // Group comparisons by company
        const companiesData = {};
        data.comparisons.forEach(item => {
          // Format any dates in the comparison item
          if (item.date) {
            item.formattedDate = formatDate(item.date);
          }

          if (!companiesData[item.company]) {
            companiesData[item.company] = [];
          }
          companiesData[item.company].push(item);
        });

        // Generate company tabs
        const companiesTabsContainer = document.getElementById('companies-tabs');
        companiesTabsContainer.innerHTML = '';

        // Generate comparison tables
        const tablesContainer = document.getElementById('comparison-tables');
        tablesContainer.innerHTML = '';

        // Create tabs and tables for each company
        Object.keys(companiesData).forEach((company, index) => {
          // Create tab
          const tab = document.createElement('div');
          tab.className = `company-tab ${index === 0 ? 'active' : ''}`;
          tab.dataset.company = company;
          tab.innerHTML = `
      <i class="fas fa-building"></i>
      <span>${company}</span>
    `;
          tab.addEventListener('click', () => switchCompanyTab(company));
          companiesTabsContainer.appendChild(tab);

          // Create table
          const table = document.createElement('table');
          table.className = `comparison-table ${index === 0 ? 'active' : ''}`;
          table.id = `table-${company}`;

          // Create table header
          const thead = document.createElement('thead');
          thead.innerHTML = `
      <tr>
        <th>סוג כיסוי</th>
        <th>גבול אחריות</th>
        <th>השתתפות עצמית</th>
        <th>הערות</th>
      </tr>
    `;
          table.appendChild(thead);

          // Create table body
          const tbody = document.createElement('tbody');
          companiesData[company].forEach(coverage => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${coverage.coverage_name}</td>
        <td>${coverage.liability_limit}</td>
        <td>${coverage.deductible}</td>
        <td>${coverage.notes}</td>
      `;
            tbody.appendChild(row);
          });
          table.appendChild(tbody);

          tablesContainer.appendChild(table);
        });
      }

      function switchCompanyTab(company) {
        // Update active tab
        document.querySelectorAll('.company-tab').forEach(tab => {
          tab.classList.remove('active');
        });
        document.querySelector(`.company-tab[data-company="${company}"]`).classList.add('active');

        // Update active table
        document.querySelectorAll('.comparison-table').forEach(table => {
          table.classList.remove('active');
        });
        document.getElementById(`table-${company}`).classList.add('active');
      }

      function backToForm() {
        document.querySelector('.comparison-wizard').style.display = 'block';
        document.getElementById('comparison-results').style.display = 'none';
      }

      function generateComparison() {
        // Collect all form data
        const formData = collectFormData();

        // Validate required fields
        if (!validateFormData(formData)) {
          return;
        }

        // Send data via REST API
        sendComparisonData(formData);
      }

      function collectFormData() {
        const destinationInput = document.getElementById('travel-destination');
        const formData = {
          client: getClientData(),
          travel: {
            destination: destinationInput.getAttribute('data-country-code') || selectedDestination,
            destinationName: destinationInput.value,
            travelersCount: document.getElementById('travelers-count').value,
            departureDate: document.getElementById('departure-date').value,
            returnDate: document.getElementById('return-date').value,
            oldestTravelerAge: document.getElementById('oldest-traveler-age').value,
          },
          coverage: {
            essential: [
              'medical-expenses',
              'third-party-liability',
              'trip-cancellation',
              'trip-shortening',
              'luggage',
              'search-rescue',
            ],
            optional: getSelectedOptionalCoverage(),
          },
          companies: {
            regular: getSelectedCompanies('regular-companies'),
            direct: getSelectedCompanies('direct-companies'),
          },
          timestamp: new Date().toISOString(),
        };

        // Generate prompt field
        formData.prompt = generatePrompt(formData);

        return formData;
      }

      function getClientData() {
        const clientType = document.querySelector('input[name="client-type"]:checked').value;

        if (clientType === 'existing') {
          const selectElement = document.getElementById('existing-client-select');
          const selectedOption = selectElement.options[selectElement.selectedIndex];

          // Try to get client data from dataset
          let clientData = {};
          try {
            if (selectedOption && selectedOption.dataset.clientData) {
              clientData = JSON.parse(selectedOption.dataset.clientData);
            }
          } catch (e) {
            console.error('Error parsing client data:', e);
          }

          return {
            type: 'existing',
            id: selectElement.value,
            name: clientData.name || '',
            id_number: clientData.id_number || '',
            phone: clientData.phone || '',
            email: clientData.email || '',
          };
        } else {
          return {
            type: 'new',
            name: document.getElementById('new-client-name').value,
            id_number: document.getElementById('new-client-id').value,
            phone: document.getElementById('new-client-phone').value,
            email: document.getElementById('new-client-email').value,
          };
        }
      }

      function getSelectedOptionalCoverage() {
        const optionalCoverage = [];
        const checkboxes = document.querySelectorAll('input[name="optional-coverage"]:checked');

        checkboxes.forEach(checkbox => {
          const coverage = {
            type: checkbox.value,
            name: checkbox.closest('.coverage-item').querySelector('.coverage-name').textContent,
          };

          // Add days for sports activities
          if (checkbox.value === 'extreme-sports') {
            const daysInput = document.querySelector('input[name="extreme-sports-days"]');
            if (daysInput && daysInput.value) {
              coverage.days = parseInt(daysInput.value);
            }
          } else if (checkbox.value === 'winter-sports') {
            const daysInput = document.querySelector('input[name="winter-sports-days"]');
            if (daysInput && daysInput.value) {
              coverage.days = parseInt(daysInput.value);
            }
          }

          optionalCoverage.push(coverage);
        });

        return optionalCoverage;
      }

      function getSelectedCompanies(name) {
        const companies = [];
        const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);

        checkboxes.forEach(checkbox => {
          companies.push({
            id: checkbox.value,
            name: checkbox.closest('.company-item').querySelector('.company-name').textContent,
          });
        });

        return companies;
      }

      function generatePrompt(formData) {
        // Get all selected companies
        const allCompanies = [...formData.companies.regular, ...formData.companies.direct];
        const companiesText = allCompanies.map(company => company.name).join(', ');

        // Calculate trip duration
        const departureDate = new Date(formData.travel.departureDate);
        const returnDate = new Date(formData.travel.returnDate);
        const tripDuration = Math.ceil((returnDate - departureDate) / (1000 * 60 * 60 * 24));

        // Get destination name in Hebrew from the input value
        const destinationInput = document.getElementById('travel-destination');
        const destinationHebrew = destinationInput.value || formData.travel.destination;

        // Build coverage list
        let coverageList = [];

        // Essential coverage names in Hebrew
        const essentialCoverageMap = {
          'medical-expenses': 'הוצאות רפואיות',
          'third-party-liability': 'חבות כלפי צד ג\'',
          'trip-cancellation': 'ביטול נסיעה',
          'trip-shortening': 'קיצור נסיעה',
          'luggage': 'כבודה',
          'search-rescue': 'איתור, חיפוש וחילוץ',
        };

        // Optional coverage names in Hebrew
        const optionalCoverageMap = {
          'dental-emergency': 'טיפול חירום בשיניים',
          'medical-israel': 'הוצאות רפואיות בישראל',
          'pregnancy': 'הריון לא בסיכון',
          'rental-car': 'ביטול השתתפות עצמית לרכב שכור',
          'extreme-sports': 'פעילות אתגרית',
          'winter-sports': 'ספורט חורף',
          'electronics': 'מחשב נייד / טלפון / טאבלט',
          'camera': 'מצלמה',
        };

        // Add essential coverage
        formData.coverage.essential.forEach(coverage => {
          if (essentialCoverageMap[coverage]) {
            coverageList.push(essentialCoverageMap[coverage]);
          }
        });

        // Add optional coverage
        formData.coverage.optional.forEach(coverage => {
          let coverageName = optionalCoverageMap[coverage.type];
          if (coverageName) {
            if (coverage.days && (coverage.type === 'extreme-sports' || coverage.type === 'winter-sports')) {
              coverageName += ` למשך ${coverage.days} ימים`;
            }
            coverageList.push(coverageName);
          }
        });

        // Build the prompt
        let prompt = `החברות להשוואה הן: ${companiesText}.\n\n`;
        prompt += `להלן פרטי הנוסע:\n`;
        prompt += `גיל: ${formData.travel.oldestTravelerAge}\n`;
        prompt += `יעד: ${destinationHebrew}\n`;
        prompt += `משך השהות: ${tripDuration} ימים\n`;
        prompt += `כיסויים נחוצים:\n`;

        coverageList.forEach(coverage => {
          prompt += `${coverage}\n`;
        });

        return prompt;
      }

      function validateFormData(formData) {
        // Validate travel details
        if (!formData.travel.destination || !formData.travel.travelersCount ||
            !formData.travel.departureDate || !formData.travel.returnDate ||
            !formData.travel.oldestTravelerAge) {
          alert('אנא מלא את כל השדות הנדרשים בפרטי הנסיעה');
          return false;
        }

        // Validate dates
        const departureDate = new Date(formData.travel.departureDate);
        const returnDate = new Date(formData.travel.returnDate);
        const today = new Date();

        if (departureDate <= today) {
          alert('תאריך היציאה חייב להיות בעתיד');
          return false;
        }

        if (returnDate <= departureDate) {
          alert('תאריך החזרה חייב להיות אחרי תאריך היציאה');
          return false;
        }

        // Validate at least one company is selected
        if (formData.companies.regular.length === 0 && formData.companies.direct.length === 0) {
          alert('אנא בחר לפחות חברת ביטוח אחת להשוואה');
          return false;
        }

        // Validate client data
        if (formData.client.type === 'new') {
          if (!formData.client.name || !formData.client.id_number || !formData.client.phone || !formData.client.email) {
            alert('אנא מלא את כל פרטי הלקוח החדש');
            return false;
          }

          if (!/^\d{9}$/.test(formData.client.id_number)) {
            alert('תעודת זהות חייבת להכיל 9 ספרות');
            return false;
          }

          if (!InsurancePlatform.validateEmail(formData.client.email)) {
            alert('כתובת האימייל אינה תקינה');
            return false;
          }

          if (!InsurancePlatform.validatePhone(formData.client.phone)) {
            alert('מספר הטלפון אינו תקין');
            return false;
          }
        } else if (!formData.client.id) {
          alert('אנא בחר לקוח מהרשימה');
          return false;
        }

        return true;
      }

      // Countries data with Hebrew names and restrictions
      const countriesData = {
        // Europe
        'albania': {he: 'אלבניה', restriction: null},
        'andorra': {he: 'אנדורה', restriction: null},
        'armenia': {he: 'ארמניה', restriction: null},
        'austria': {he: 'אוסטריה', restriction: null},
        'azerbaijan': {he: 'אזרבייג\'ן', restriction: null},
        'belarus': {he: 'בלרוס', restriction: 'דורש אישור מיוחד'},
        'belgium': {he: 'בלגיה', restriction: null},
        'bosnia': {he: 'בוסניה והרצגובינה', restriction: null},
        'bulgaria': {he: 'בולגריה', restriction: null},
        'croatia': {he: 'קרואטיה', restriction: null},
        'cyprus': {he: 'קפריסין', restriction: null},
        'czech': {he: 'צ\'כיה', restriction: null},
        'denmark': {he: 'דנמרק', restriction: null},
        'estonia': {he: 'אסטוניה', restriction: null},
        'finland': {he: 'פינלנד', restriction: null},
        'france': {he: 'צרפת', restriction: null},
        'georgia': {he: 'גאורגיה', restriction: null},
        'germany': {he: 'גרמניה', restriction: null},
        'greece': {he: 'יוון', restriction: null},
        'hungary': {he: 'הונגריה', restriction: null},
        'iceland': {he: 'איסלנד', restriction: null},
        'ireland': {he: 'אירלנד', restriction: null},
        'italy': {he: 'איטליה', restriction: null},
        'kosovo': {he: 'קוסובו', restriction: null},
        'latvia': {he: 'לטביה', restriction: null},
        'liechtenstein': {he: 'ליכטנשטיין', restriction: null},
        'lithuania': {he: 'ליטא', restriction: null},
        'luxembourg': {he: 'לוקסמבורג', restriction: null},
        'malta': {he: 'מלטה', restriction: null},
        'moldova': {he: 'מולדובה', restriction: null},
        'monaco': {he: 'מונקו', restriction: null},
        'montenegro': {he: 'מונטנגרו', restriction: null},
        'netherlands': {he: 'הולנד', restriction: null},
        'north-macedonia': {he: 'צפון מקדוניה', restriction: null},
        'norway': {he: 'נורווגיה', restriction: null},
        'poland': {he: 'פולין', restriction: null},
        'portugal': {he: 'פורטוגל', restriction: null},
        'romania': {he: 'רומניה', restriction: null},
        'russia': {he: 'רוסיה', restriction: 'דורש אישור מיוחד'},
        'san-marino': {he: 'סן מרינו', restriction: null},
        'serbia': {he: 'סרביה', restriction: null},
        'slovakia': {he: 'סלובקיה', restriction: null},
        'slovenia': {he: 'סלובניה', restriction: null},
        'spain': {he: 'ספרד', restriction: null},
        'sweden': {he: 'שוודיה', restriction: null},
        'switzerland': {he: 'שוויץ', restriction: 'תוספת מחיר'},
        'turkey': {he: 'טורקיה', restriction: null},
        'ukraine': {he: 'אוקראינה', restriction: 'דורש אישור מיוחד'},
        'united-kingdom': {he: 'בריטניה', restriction: null},
        'vatican': {he: 'ותיקן', restriction: null},

        // Asia
        'afghanistan': {he: 'אפגניסטן', restriction: 'דורש אישור מיוחד'},
        'bahrain': {he: 'בחריין', restriction: null},
        'bangladesh': {he: 'בנגלדש', restriction: null},
        'bhutan': {he: 'בהוטן', restriction: null},
        'brunei': {he: 'ברוניי', restriction: null},
        'cambodia': {he: 'קמבודיה', restriction: null},
        'china': {he: 'סין', restriction: null},
        'india': {he: 'הודו', restriction: null},
        'indonesia': {he: 'אינדונזיה', restriction: null},
        'iran': {he: 'איראן', restriction: 'אין כיסוי – מוחרג לחלוטין'},
        'iraq': {he: 'עיראק', restriction: 'אין כיסוי – מוחרג לחלוטין'},
        'israel': {he: 'ישראל', restriction: null},
        'japan': {he: 'יפן', restriction: 'תוספת מחיר'},
        'jordan': {he: 'ירדן', restriction: null},
        'kazakhstan': {he: 'קזחסטן', restriction: null},
        'kuwait': {he: 'כווית', restriction: null},
        'kyrgyzstan': {he: 'קירגיזסטן', restriction: null},
        'laos': {he: 'לאוס', restriction: null},
        'lebanon': {he: 'לבנון', restriction: 'דורש אישור מיוחד'},
        'malaysia': {he: 'מלזיה', restriction: null},
        'maldives': {he: 'מלדיביים', restriction: null},
        'mongolia': {he: 'מונגוליה', restriction: null},
        'myanmar': {he: 'מיאנמר', restriction: null},
        'nepal': {he: 'נפאל', restriction: null},
        'north-korea': {he: 'קוריאה הצפונית', restriction: 'אין כיסוי – מוחרג לחלוטין'},
        'oman': {he: 'עומאן', restriction: null},
        'pakistan': {he: 'פקיסטן', restriction: 'דורש אישור מיוחד'},
        'palestine': {he: 'פלסטין', restriction: null},
        'philippines': {he: 'פיליפינים', restriction: null},
        'qatar': {he: 'קטר', restriction: null},
        'saudi-arabia': {he: 'ערב הסעודית', restriction: null},
        'singapore': {he: 'סינגפור', restriction: 'תוספת מחיר'},
        'south-korea': {he: 'קוריאה הדרומית', restriction: null},
        'sri-lanka': {he: 'סרי לנקה', restriction: null},
        'syria': {he: 'סוריה', restriction: 'אין כיסוי – מוחרג לחלוטין'},
        'taiwan': {he: 'טייוואן', restriction: null},
        'tajikistan': {he: 'טג\'יקיסטן', restriction: null},
        'thailand': {he: 'תאילנד', restriction: null},
        'timor-leste': {he: 'מזרח טימור', restriction: null},
        'turkmenistan': {he: 'טורקמניסטן', restriction: null},
        'uae': {he: 'איחוד האמירויות', restriction: null},
        'uzbekistan': {he: 'אוזבקיסטן', restriction: null},
        'vietnam': {he: 'וייטנאם', restriction: null},
        'yemen': {he: 'תימן', restriction: 'אין כיסוי – מוחרג לחלוטין'},

        // Africa
        'algeria': {he: 'אלג\'יריה', restriction: 'דורש אישור מיוחד'},
        'angola': {he: 'אנגולה', restriction: null},
        'benin': {he: 'בנין', restriction: null},
        'botswana': {he: 'בוטסואנה', restriction: null},
        'burkina-faso': {he: 'בורקינה פאסו', restriction: 'דורש אישור מיוחד'},
        'burundi': {he: 'בורונדי', restriction: null},
        'cameroon': {he: 'קמרון', restriction: null},
        'cape-verde': {he: 'כף ורדה', restriction: null},
        'central-african-republic': {he: 'הרפובליקה המרכז אפריקאית', restriction: 'דורש אישור מיוחד'},
        'chad': {he: 'צ\'אד', restriction: null},
        'comoros': {he: 'קומורו', restriction: null},
        'congo': {he: 'קונגו', restriction: 'דורש אישור מיוחד'},
        'drc': {he: 'קונגו הדמוקרטית', restriction: null},
        'djibouti': {he: 'ג\'יבוטי', restriction: null},
        'egypt': {he: 'מצרים', restriction: 'דורש אישור מיוחד'},
        'equatorial-guinea': {he: 'גינאה המשוונית', restriction: null},
        'eritrea': {he: 'אריתריאה', restriction: null},
        'eswatini': {he: 'אסווטיני', restriction: null},
        'ethiopia': {he: 'אתיופיה', restriction: null},
        'gabon': {he: 'גבון', restriction: null},
        'gambia': {he: 'גמביה', restriction: null},
        'ghana': {he: 'גאנה', restriction: null},
        'guinea': {he: 'גינאה', restriction: null},
        'guinea-bissau': {he: 'גינאה ביסאו', restriction: null},
        'ivory-coast': {he: 'חוף השנהב', restriction: null},
        'kenya': {he: 'קניה', restriction: null},
        'lesotho': {he: 'לסוטו', restriction: null},
        'liberia': {he: 'ליבריה', restriction: null},
        'libya': {he: 'לוב', restriction: 'אין כיסוי – מוחרג לחלוטין'},
        'madagascar': {he: 'מדגסקר', restriction: null},
        'malawi': {he: 'מלאווי', restriction: null},
        'mali': {he: 'מאלי', restriction: 'דורש אישור מיוחד'},
        'mauritania': {he: 'מאוריטניה', restriction: null},
        'mauritius': {he: 'מאוריציוס', restriction: null},
        'morocco': {he: 'מרוקו', restriction: 'דורש אישור מיוחד'},
        'mozambique': {he: 'מוזמביק', restriction: null},
        'namibia': {he: 'נמיביה', restriction: null},
        'niger': {he: 'ניז\'ר', restriction: null},
        'nigeria': {he: 'ניגריה', restriction: 'דורש אישור מיוחד במידה ומדובר בצפון ניגריה'},
        'rwanda': {he: 'רואנדה', restriction: null},
        'sao-tome': {he: 'סאו טומה ופרינסיפה', restriction: null},
        'senegal': {he: 'סנגל', restriction: null},
        'seychelles': {he: 'סיישל', restriction: null},
        'sierra-leone': {he: 'סיירה לאונה', restriction: null},
        'somalia': {he: 'סומליה', restriction: null},
        'south-africa': {he: 'דרום אפריקה', restriction: null},
        'south-sudan': {he: 'דרום סודן', restriction: null},
        'sudan': {he: 'סודן', restriction: 'דורש אישור מיוחד'},
        'tanzania': {he: 'טנזניה', restriction: null},
        'togo': {he: 'טוגו', restriction: null},
        'tunisia': {he: 'תוניסיה', restriction: null},
        'uganda': {he: 'אוגנדה', restriction: null},
        'zambia': {he: 'זמביה', restriction: null},
        'zimbabwe': {he: 'זימבבואה', restriction: null},

        // North America
        'canada': {he: 'קנדה', restriction: 'תוספת מחיר'},
        'usa': {he: 'ארה״ב', restriction: 'תוספת מחיר'},
        'mexico': {he: 'מקסיקו', restriction: null},

        // Central & South America
        'antigua': {he: 'אנטיגואה וברבודה', restriction: null},
        'argentina': {he: 'ארגנטינה', restriction: null},
        'bahamas': {he: 'בהאמה', restriction: null},
        'barbados': {he: 'ברבדוס', restriction: null},
        'belize': {he: 'בליז', restriction: null},
        'bolivia': {he: 'בוליביה', restriction: null},
        'brazil': {he: 'ברזיל', restriction: null},
        'chile': {he: 'צ\'ילה', restriction: null},
        'colombia': {he: 'קולומביה', restriction: null},
        'costa-rica': {he: 'קוסטה ריקה', restriction: null},
        'cuba': {he: 'קובה', restriction: null},
        'dominica': {he: 'דומיניקה', restriction: null},
        'dominican-republic': {he: 'הרפובליקה הדומיניקנית', restriction: null},
        'ecuador': {he: 'אקוודור', restriction: null},
        'el-salvador': {he: 'אל סלבדור', restriction: null},
        'grenada': {he: 'גרנדה', restriction: null},
        'guatemala': {he: 'גואטמלה', restriction: null},
        'guyana': {he: 'גיאנה', restriction: null},
        'haiti': {he: 'האיטי', restriction: null},
        'honduras': {he: 'הונדורס', restriction: null},
        'jamaica': {he: 'ג\'מייקה', restriction: null},
        'nicaragua': {he: 'ניקרגואה', restriction: null},
        'panama': {he: 'פנמה', restriction: null},
        'paraguay': {he: 'פרגוואי', restriction: null},
        'peru': {he: 'פרו', restriction: null},
        'saint-kitts': {he: 'סנט קיטס ונוויס', restriction: null},
        'saint-lucia': {he: 'סנט לוסיה', restriction: null},
        'saint-vincent': {he: 'סנט וינסנט והגרנדינים', restriction: null},
        'suriname': {he: 'סורינאם', restriction: null},
        'trinidad': {he: 'טרינידד וטובגו', restriction: null},
        'uruguay': {he: 'אורוגוואי', restriction: null},
        'venezuela': {he: 'ונצואלה', restriction: 'דורש אישור מיוחד'},

        // Oceania
        'australia': {he: 'אוסטרליה', restriction: 'תוספת מחיר'},
        'fiji': {he: 'פיג\'י', restriction: null},
        'kiribati': {he: 'קיריבטי', restriction: null},
        'marshall-islands': {he: 'איי מרשל', restriction: null},
        'micronesia': {he: 'מיקרונזיה', restriction: null},
        'nauru': {he: 'נאורו', restriction: null},
        'new-zealand': {he: 'ניו זילנד', restriction: 'תוספת מחיר'},
        'palau': {he: 'פלאו', restriction: null},
        'papua-new-guinea': {he: 'פפואה גינאה החדשה', restriction: null},
        'samoa': {he: 'סמואה', restriction: null},
        'solomon-islands': {he: 'איי שלמה', restriction: null},
        'tonga': {he: 'טונגה', restriction: null},
        'tuvalu': {he: 'טובאלו', restriction: null},
        'vanuatu': {he: 'ונואטו', restriction: null},
      };

      // Initialize autocomplete
      let selectedDestination = null;

      document.addEventListener('DOMContentLoaded', function() {
        initializeDestinationAutocomplete();
      });

      function initializeDestinationAutocomplete() {
        const input = document.getElementById('travel-destination');
        const dropdown = document.getElementById('destination-dropdown');
        const infoDiv = document.getElementById('destination-info');

        input.addEventListener('input', function() {
          const query = this.value.trim();

          if (query.length < 1) {
            dropdown.style.display = 'none';
            infoDiv.style.display = 'none';
            selectedDestination = null;
            return;
          }

          const matches = searchCountries(query);
          displayMatches(matches, dropdown, input, infoDiv);
        });

        input.addEventListener('blur', function() {
          // Delay hiding to allow click on dropdown items
          setTimeout(() => {
            dropdown.style.display = 'none';
          }, 200);
        });

        input.addEventListener('focus', function() {
          if (this.value.trim().length > 0) {
            const matches = searchCountries(this.value.trim());
            displayMatches(matches, dropdown, input, infoDiv);
          }
        });
      }

      function searchCountries(query) {
        const matches = [];
        const queryLower = query.toLowerCase();

        for (const [code, data] of Object.entries(countriesData)) {
          // Search in Hebrew name
          if (data.he.includes(query) || data.he.toLowerCase().includes(queryLower)) {
            matches.push({code, ...data, score: 1});
          }
          // Search in English code (transliterated)
          else if (code.includes(queryLower)) {
            matches.push({code, ...data, score: 0.5});
          }
        }

        // Sort by score and then alphabetically
        return matches.sort((a, b) => {
          if (a.score !== b.score) return b.score - a.score;
          return a.he.localeCompare(b.he, 'he');
        }).slice(0, 8); // Limit to 8 results
      }

      function displayMatches(matches, dropdown, input, infoDiv) {
        if (matches.length === 0) {
          dropdown.style.display = 'none';
          return;
        }

        dropdown.innerHTML = '';

        matches.forEach(match => {
          const item = document.createElement('div');
          item.className = 'autocomplete-item';

          const nameSpan = document.createElement('span');
          nameSpan.className = 'country-name';
          nameSpan.textContent = match.he;

          item.appendChild(nameSpan);

          if (match.restriction) {
            const restrictionSpan = document.createElement('span');
            restrictionSpan.className = 'country-restriction';
            restrictionSpan.textContent = match.restriction;

            if (match.restriction.includes('מוחרג לחלוטין')) {
              restrictionSpan.classList.add('restriction-blocked');
            } else if (match.restriction.includes('תוספת מחיר')) {
              restrictionSpan.classList.add('restriction-price');
            } else {
              restrictionSpan.classList.add('restriction-approval');
            }

            item.appendChild(restrictionSpan);
          }

          item.addEventListener('click', function() {
            selectCountry(match, input, dropdown, infoDiv);
          });

          dropdown.appendChild(item);
        });

        dropdown.style.display = 'block';
      }

      function selectCountry(country, input, dropdown, infoDiv) {
        selectedDestination = country.code;
        input.value = country.he;
        dropdown.style.display = 'none';

        // Show country info
        if (country.restriction) {
          infoDiv.innerHTML = `
      <div class="destination-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span>${country.restriction}</span>
      </div>
    `;
          infoDiv.style.display = 'block';

          if (country.restriction.includes('מוחרג לחלוטין')) {
            infoDiv.querySelector('.destination-warning').classList.add('warning-blocked');
          } else if (country.restriction.includes('תוספת מחיר')) {
            infoDiv.querySelector('.destination-warning').classList.add('warning-price');
          } else {
            infoDiv.querySelector('.destination-warning').classList.add('warning-approval');
          }
        } else {
          infoDiv.style.display = 'none';
        }

        // Update the form data collection to use selectedDestination
        input.setAttribute('data-country-code', country.code);
      }

      // Initialize the form when page loads
      document.addEventListener('DOMContentLoaded', function() {
        // Set travel insurance as selected by default
        selectedInsuranceType = 'travel';
      });

      // Client API functions
      document.addEventListener('DOMContentLoaded', function() {
        // Load clients list when page loads
        loadClientsList();

        // Add event listener for client form submission
        document.getElementById('client-form') && document.getElementById('client-form').addEventListener('submit', function(e) {
          e.preventDefault();
          addNewClient();
        });
      });

      // Function to load clients from API with improved JSON handling
      function loadClientsList() {
        const clientSelect = document.getElementById('existing-client-select');
        if (!clientSelect) return;

        // Show loading state
        clientSelect.innerHTML = '<option value="">טוען רשימת לקוחות...</option>';
        clientSelect.disabled = true;

        // Fetch clients from API
        fetch('https://dev.comparisons/api/ClientsList.php').then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.text(); // Get as text first to handle HTML entities
        }).then(responseText => {
          // Decode HTML entities
          const decodedText = responseText.replace(/&quot;/g, '"').
              replace(/&amp;/g, '&').
              replace(/&lt;/g, '<').
              replace(/&gt;/g, '>');

          // Parse the JSON
          let data;
          try {
            data = JSON.parse(decodedText);
          } catch (parseError) {
            console.error('JSON parse error:', parseError);
            throw new Error('Invalid JSON response');
          }

          // Clear loading option
          clientSelect.innerHTML = '<option value="">בחר לקוח מהרשימה</option>';

          // Handle the response - it should be an array of client objects
          let clientsArray = [];
          if (Array.isArray(data)) {
            clientsArray = data;
          } else {
            console.error('Unexpected data format:', data);
            throw new Error('Invalid data format received');
          }

          // Add clients to dropdown
          if (clientsArray.length > 0) {
            clientsArray = clientsArray[0];
            clientsArray.forEach(client => {
              const option = document.createElement('option');
              option.value = client.id;
              option.textContent = `${client.name} - ${client.phone} (${client.id_number})`;
              option.dataset.clientData = JSON.stringify(client);
              clientSelect.appendChild(option);
            });
          } else {
            // No clients found
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'אין לקוחות במערכת';
            clientSelect.appendChild(option);
          }

          // Enable select
          clientSelect.disabled = false;
        }).catch(error => {
          console.error('Error fetching clients:', error);
          clientSelect.innerHTML = '<option value="">שגיאה בטעינת לקוחות</option>';
          clientSelect.disabled = false;

          // Show error notification
          if (window.InsurancePlatform && window.InsurancePlatform.showNotification) {
            window.InsurancePlatform.showNotification('שגיאה בטעינת רשימת הלקוחות', 'error');
          }
        });
      }

      // Function to add a new client
      function addNewClient() {
        const nameInput = document.getElementById('new-client-name');
        const idInput = document.getElementById('new-client-id');
        const phoneInput = document.getElementById('new-client-phone');
        const emailInput = document.getElementById('new-client-email');

        // Validate inputs
        if (!nameInput.value || !idInput.value || !phoneInput.value || !emailInput.value) {
          alert('אנא מלא את כל השדות הנדרשים');
          return;
        }

        // Validate ID number (9 digits)
        if (!/^\d{9}$/.test(idInput.value)) {
          alert('תעודת זהות חייבת להכיל 9 ספרות');
          return;
        }

        // Validate email
        if (!InsurancePlatform.validateEmail(emailInput.value)) {
          alert('כתובת האימייל אינה תקינה');
          return;
        }

        // Validate phone
        if (!InsurancePlatform.validatePhone(phoneInput.value)) {
          alert('מספר הטלפון אינו תקין');
          return;
        }

        // Prepare client data
        const clientData = {
          name: nameInput.value,
          id_number: idInput.value,
          phone: phoneInput.value,
          email: emailInput.value,
        };

        // Show loading state
        const submitBtn = document.querySelector('#client-form button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מוסיף לקוח...';
        }

        // Send data to API
        fetch('https://dev.comparisons/api/AddClient.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`, // If you use auth tokens
          },
          body: JSON.stringify(clientData),
        }).then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        }).then(data => {
          // Show success message
          InsurancePlatform.showNotification('הלקוח נוסף בהצלחה!', 'success');

          // Reset form
          document.getElementById('client-form').reset();

          // Reload clients list
          loadClientsList();

          // Switch to existing client tab
          document.querySelector('input[name="client-type"][value="existing"]').checked = true;
          document.querySelector('.existing-client').style.display = 'block';
          document.querySelector('.new-client').style.display = 'none';

          // Reset button state
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }).catch(error => {
          console.error('Error adding client:', error);

          // Show error message
          InsurancePlatform.showNotification('שגיאה בהוספת הלקוח', 'error');

          // Reset button state
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        });
      }

      // Update the getClientData function to include the ID number
      function getClientData() {
        const clientType = document.querySelector('input[name="client-type"]:checked').value;

        if (clientType === 'existing') {
          const selectElement = document.getElementById('existing-client-select');
          const selectedOption = selectElement.options[selectElement.selectedIndex];

          // Try to get client data from dataset
          let clientData = {};
          try {
            if (selectedOption && selectedOption.dataset.clientData) {
              clientData = JSON.parse(selectedOption.dataset.clientData);
            }
          } catch (e) {
            console.error('Error parsing client data:', e);
          }

          return {
            type: 'existing',
            id: selectElement.value,
            name: clientData.name || '',
            id_number: clientData.id_number || '',
            phone: clientData.phone || '',
            email: clientData.email || '',
          };
        } else {
          return {
            type: 'new',
            name: document.getElementById('new-client-name').value,
            id_number: document.getElementById('new-client-id').value,
            phone: document.getElementById('new-client-phone').value,
            email: document.getElementById('new-client-email').value,
          };
        }
      }
    </script>

    <style>
        /* Additional CSS for the new form elements */
        .comparison-wizard.full-width {
            width: 100%;
            max-width: 100%;
        }

        .coverage-description {
            color: #9ca3af;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .coverage-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .coverage-item {
            background-color: #2d3748;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #374151;
            transition: all 0.2s ease;
        }

        .coverage-item.essential {
            border-color: rgba(16, 185, 129, 0.3);
            background-color: rgba(16, 185, 129, 0.05);
        }

        .coverage-item:hover {
            border-color: rgb(255, 23, 68);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .coverage-checkbox,
        .company-checkbox {
            margin-left: 0.75rem;
            transform: scale(1.2);
        }

        .coverage-name,
        .company-name {
            flex: 1;
        }

        .days-input {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #374151;
        }

        .days-input .form-label {
            font-size: 0.8125rem;
            margin-bottom: 0.5rem;
        }

        .days-input .form-input {
            max-width: 120px;
        }

        .companies-section {
            margin-bottom: 2rem;
        }

        .companies-section h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(255, 23, 68);
        }

        .companies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .company-item {
            background-color: #2d3748;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #374151;
            transition: all 0.2s ease;
        }

        .company-item:hover {
            border-color: rgb(255, 23, 68);
            transform: translateY(-2px);
        }

        .with-days {
            grid-column: span 2;
        }

        @media (max-width: 768px) {
            .coverage-grid {
                grid-template-columns: 1fr;
            }

            .companies-grid {
                grid-template-columns: 1fr;
            }

            .with-days {
                grid-column: span 1;
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-content {
            background-color: #1e293b;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            max-width: 90%;
            width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .loading-spinner {
            font-size: 3rem;
            color: rgb(255, 23, 68);
            margin-bottom: 1rem;
        }

        .loading-content h3 {
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
        }

        .progress-bar-container {
            height: 8px;
            background-color: #374151;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-bar {
            height: 100%;
            background-color: rgb(255, 23, 68);
            width: 0%;
            transition: width 0.3s ease;
        }

        #loading-status {
            color: #9ca3af;
            font-size: 0.875rem;
        }

        /* Results Section */
        .comparison-results {
            margin-top: 2rem;
            animation: fadeIn 0.5s ease;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .recommendation-box {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .recommendation-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: rgb(16, 185, 129);
        }

        .recommendation-header i {
            font-size: 1.5rem;
            margin-left: 0.75rem;
        }

        .recommendation-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        #recommendation-text {
            line-height: 1.6;
        }

        .companies-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .company-tab {
            background-color: #2d3748;
            border: 1px solid #374151;
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .company-tab:hover {
            background-color: #374151;
        }

        .company-tab.active {
            background-color: rgb(255, 23, 68);
            border-color: rgb(255, 23, 68);
            color: white;
        }

        .company-tab i {
            margin-left: 0.5rem;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            display: none;
        }

        .comparison-table.active {
            display: table;
            animation: fadeIn 0.3s ease;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #374151;
        }

        .comparison-table th {
            background-color: #1e293b;
            font-weight: 600;
        }

        .comparison-table tr:nth-child(even) {
            background-color: rgba(45, 55, 72, 0.5);
        }

        .comparison-table tr:hover {
            background-color: rgba(45, 55, 72, 0.8);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Autocomplete Styles */
        .autocomplete-container {
            position: relative;
        }

        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #1f2937;
            border: 1px solid #374151;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #374151;
            transition: background-color 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background-color: #2d3748;
        }

        .country-name {
            font-weight: 500;
            color: #f9fafb;
        }

        .country-restriction {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .country-restriction.restriction-blocked {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .country-restriction.restriction-price {
            background-color: rgba(251, 191, 36, 0.2);
            color: #f59e0b;
        }

        .country-restriction.restriction-approval {
            background-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .destination-info {
            margin-top: 0.5rem;
        }

        .destination-warning {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .destination-warning.warning-blocked {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .destination-warning.warning-price {
            background-color: rgba(251, 191, 36, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .destination-warning.warning-approval {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .destination-warning i {
            font-size: 1rem;
        }

        /* Responsive autocomplete */
        @media (max-width: 768px) {
            .autocomplete-dropdown {
                max-height: 200px;
            }

            .autocomplete-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .country-restriction {
                align-self: flex-end;
            }
        }

        /* Age restrictions table */
        .age-restrictions {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .age-restrictions-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #3b82f6;
        }

        .age-restrictions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .age-restrictions-table td {
            padding: 0.25rem 0.5rem;
            border: none;
        }

        .age-restrictions-table td:first-child {
            font-weight: 500;
            width: 30%;
        }

        .age-restrictions-table tr:nth-child(1) td:last-child {
            color: #10b981; /* Green for full coverage */
        }

        .age-restrictions-table tr:nth-child(2) td:last-child {
            color: #f59e0b; /* Yellow for health declaration */
        }

        .age-restrictions-table tr:nth-child(3) td:last-child {
            color: #ef4444; /* Red for special approval */
        }
    </style>

<?php
include __DIR__ . '/htmls/footer.php';
?>