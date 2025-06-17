Based on your Hebrew insurance comparison platform, here's a comprehensive MySQL database schema:

## Database Schema for Insurance Comparison Platform




### 4. **Insurance Types Table** (סוגי ביטוח)

```sql
CREATE TABLE insurance_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    name_hebrew VARCHAR(50) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_is_active (is_active)
);
```

### 5. **Coverage Types Table** (סוגי כיסוי)

```sql
CREATE TABLE coverage_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    insurance_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_hebrew VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    category ENUM('essential', 'optional') DEFAULT 'optional',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (insurance_type_id) REFERENCES insurance_types(id) ON DELETE CASCADE,
    INDEX idx_insurance_type_id (insurance_type_id),
    INDEX idx_code (code),
    INDEX idx_category (category)
);
```

### 6. **Countries Table** (מדינות)

```sql
CREATE TABLE countries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_hebrew VARCHAR(100) NOT NULL,
    country_code VARCHAR(3) UNIQUE NOT NULL,
    continent VARCHAR(50),
    restriction_type ENUM('none', 'approval_required', 'price_supplement', 'excluded') DEFAULT 'none',
    restriction_note VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_country_code (country_code),
    INDEX idx_continent (continent),
    INDEX idx_restriction_type (restriction_type)
);
```

### 7. **Comparisons Table** (השוואות)

```sql
CREATE TABLE comparisons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    client_id INT NOT NULL,
    insurance_type_id INT NOT NULL,
    comparison_number VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'completed', 'sent', 'approved', 'rejected') DEFAULT 'pending',
    prompt TEXT,
    recommendation TEXT,
    total_companies INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (insurance_type_id) REFERENCES insurance_types(id),
    INDEX idx_user_id (user_id),
    INDEX idx_client_id (client_id),
    INDEX idx_comparison_number (comparison_number),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```


### 9. **Comparison Coverage Table** (כיסויים בהשוואה)

```sql
CREATE TABLE comparison_coverage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comparison_id INT NOT NULL,
    coverage_type_id INT NOT NULL,
    is_selected BOOLEAN DEFAULT FALSE,
    days_count INT NULL, -- For sports activities
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (comparison_id) REFERENCES comparisons(id) ON DELETE CASCADE,
    FOREIGN KEY (coverage_type_id) REFERENCES coverage_types(id),
    INDEX idx_comparison_id (comparison_id),
    INDEX idx_coverage_type_id (coverage_type_id),
    UNIQUE KEY unique_comparison_coverage (comparison_id, coverage_type_id)
);
```

### 10. **Comparison Companies Table** (חברות בהשוואה)

```sql
CREATE TABLE comparison_companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comparison_id INT NOT NULL,
    insurance_company_id INT NOT NULL,
    is_selected BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (comparison_id) REFERENCES comparisons(id) ON DELETE CASCADE,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_companies(id),
    INDEX idx_comparison_id (comparison_id),
    INDEX idx_insurance_company_id (insurance_company_id),
    UNIQUE KEY unique_comparison_company (comparison_id, insurance_company_id)
);
```

### 11. **Comparison Results Table** (תוצאות השוואה)

```sql
CREATE TABLE comparison_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comparison_id INT NOT NULL,
    insurance_company_id INT NOT NULL,
    coverage_type_id INT NOT NULL,
    coverage_name VARCHAR(100) NOT NULL,
    liability_limit VARCHAR(100),
    deductible VARCHAR(100),
    annual_premium DECIMAL(10,2),
    notes TEXT,
    is_recommended BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (comparison_id) REFERENCES comparisons(id) ON DELETE CASCADE,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_companies(id),
    FOREIGN KEY (coverage_type_id) REFERENCES coverage_types(id),
    INDEX idx_comparison_id (comparison_id),
    INDEX idx_insurance_company_id (insurance_company_id),
    INDEX idx_coverage_type_id (coverage_type_id)
);
```

### 12. **WhatsApp Messages Table** (הודעות ווטסאפ)

```sql
CREATE TABLE whatsapp_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    comparison_id INT,
    client_id INT NOT NULL,
    message_type ENUM('outbound', 'return', 'followup', 'custom') NOT NULL,
    message_content TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    status ENUM('pending', 'sent', 'delivered', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (comparison_id) REFERENCES comparisons(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_comparison_id (comparison_id),
    INDEX idx_client_id (client_id),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
);
```

### 13. **User Settings Table** (הגדרות משתמש)

```sql
CREATE TABLE user_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_setting_key (setting_key),
    UNIQUE KEY unique_user_setting (user_id, setting_key)
);
```

### 14. **Activity Log Table** (יומן פעילות)

```sql
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL, -- 'client', 'comparison', 'user', etc.
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity_type (entity_type),
    INDEX idx_created_at (created_at)
);
```

### 15. **System Settings Table** (הגדרות מערכת)

```sql
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_public (is_public)
);
```

## Initial Data Inserts

### Insurance Types

```sql
INSERT INTO insurance_types (name, name_hebrew, code) VALUES
('Travel Insurance', 'ביטוח נסיעות', 'travel'),
('Car Insurance', 'ביטוח רכב', 'car'),
('Home Insurance', 'ביטוח דירה', 'home'),
('Health Insurance', 'ביטוח בריאות', 'health');
```

### Insurance Companies

```sql
INSERT INTO insurance_companies (name, name_hebrew, company_code, type) VALUES
('Clal Insurance', 'כלל ביטוח', 'CLAL', 'regular'),
('Harel Insurance', 'הראל ביטוח', 'HAREL', 'regular'),
('Phoenix Insurance', 'הפניקס ביטוח', 'PHOENIX', 'regular'),
('Migdal Insurance', 'מגדל ביטוח', 'MIGDAL', 'regular'),
('PassportCard', 'פספורטכארד', 'PASSPORT', 'regular'),
('AIG', 'AIG', 'AIG', 'direct'),
('Maccabi Healthcare', 'מכבי שירותי בריאות', 'MACCABI', 'direct');
```

### Coverage Types for Travel Insurance

```sql
INSERT INTO coverage_types (insurance_type_id, name, name_hebrew, code, category) VALUES
(1, 'Medical Expenses', 'הוצאות רפואיות', 'medical_expenses', 'essential'),
(1, 'Third Party Liability', 'חבות כלפי צד ג\'', 'third_party_liability', 'essential'),
(1, 'Trip Cancellation', 'ביטול נסיעה', 'trip_cancellation', 'essential'),
(1, 'Trip Shortening', 'קיצור נסיעה', 'trip_shortening', 'essential'),
(1, 'Luggage', 'כבודה', 'luggage', 'essential'),
(1, 'Search and Rescue', 'איתור, חיפוש וחילוץ', 'search_rescue', 'essential'),
(1, 'Dental Emergency', 'טיפול חירום בשיניים', 'dental_emergency', 'optional'),
(1, 'Medical Expenses in Israel', 'הוצאות רפואיות בישראל', 'medical_israel', 'optional'),
(1, 'Pregnancy Coverage', 'הריון לא בסיכון', 'pregnancy', 'optional'),
(1, 'Rental Car Deductible', 'ביטול השתתפות עצמית לרכב שכור', 'rental_car', 'optional'),
(1, 'Extreme Sports', 'פעילות אתגרית', 'extreme_sports', 'optional'),
(1, 'Winter Sports', 'ספורט חורף', 'winter_sports', 'optional'),
(1, 'Electronics Coverage', 'מחשב נייד / טלפון / טאבלט', 'electronics', 'optional'),
(1, 'Camera Coverage', 'מצלמה', 'camera', 'optional');
```

## Database Optimization

### Additional Indexes for Performance

```sql
-- Composite indexes for common queries
CREATE INDEX idx_comparisons_user_status ON comparisons(user_id, status);
CREATE INDEX idx_comparisons_client_created ON comparisons(client_id, created_at);
CREATE INDEX idx_clients_user_status ON clients(user_id, status);
CREATE INDEX idx_whatsapp_user_status ON whatsapp_messages(user_id, status);

-- Full-text search indexes
ALTER TABLE clients ADD FULLTEXT(name, email);
ALTER TABLE comparisons ADD FULLTEXT(prompt, recommendation);
```

### Views for Common Queries

```sql
-- View for client statistics
CREATE VIEW client_statistics AS
SELECT 
    c.id,
    c.name,
    c.phone,
    c.email,
    COUNT(comp.id) as total_comparisons,
    MAX(comp.created_at) as last_comparison_date,
    COUNT(CASE WHEN comp.status = 'approved' THEN 1 END) as approved_comparisons
FROM clients c
LEFT JOIN comparisons comp ON c.id = comp.client_id
GROUP BY c.id;

-- View for user activity summary
CREATE VIEW user_activity_summary AS
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(DISTINCT c.id) as total_clients,
    COUNT(DISTINCT comp.id) as total_comparisons,
    COUNT(CASE WHEN comp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as comparisons_last_30_days
FROM users u
LEFT JOIN clients c ON u.id = c.user_id
LEFT JOIN comparisons comp ON u.id = comp.user_id
GROUP BY u.id;
```

This comprehensive database schema provides:

1. **Complete user and client management**
2. **Flexible insurance company and coverage system**
3. **Detailed comparison tracking**
4. **WhatsApp integration support**
5. **Activity logging and audit trails**
6. **System configuration management**
7. **Performance optimization with proper indexing**
8. **Data integrity with foreign key constraints**


The schema is designed to be scalable and can easily accommodate additional insurance types and features as your platform grows.