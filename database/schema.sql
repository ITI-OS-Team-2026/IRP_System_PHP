CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    role ENUM('student', 'admin', 'sample_size_officer', 'reviewer', 'manager') NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS research_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    principal_investigator VARCHAR(255) NOT NULL,
    co_investigators TEXT,
    serial_number VARCHAR(50) UNIQUE NULL,
    sample_size INT,
    status ENUM('submitted', 'admin_reviewed', 'initial_paid', 'sample_sized', 'fully_paid', 'under_review', 'revision_requested', 'approved', 'rejected') DEFAULT 'submitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS research_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    document_type ENUM(
        'protocol', 
        'review_application', 
        'conflict_of_interest', 
        'irb_checklist', 
        'pi_consent', 
        'patient_consent', 
        'photos_biopsies_consent',
        'research_file'
    ) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    payment_type ENUM('initial', 'sample_size') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    receipt_url VARCHAR(500),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    review_status ENUM('pending', 'approved', 'rejected', 'modification_requested') DEFAULT 'pending',
    feedback_notes TEXT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    submission_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,

    national_id VARCHAR(50) NOT NULL,
    
    faculty VARCHAR(100),
    department VARCHAR(100),
    specialty VARCHAR(100),

    id_front_path VARCHAR(500),
    id_back_path VARCHAR(500),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
