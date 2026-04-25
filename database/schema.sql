CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    department VARCHAR(100) NULL,
    specialty VARCHAR(100) NULL,
    id_front_path VARCHAR(500) NULL,
    id_back_path VARCHAR(500) NULL,
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
    version INT NOT NULL DEFAULT 1,
    is_current TINYINT(1) NOT NULL DEFAULT 1,
    revision_round_id INT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS revision_rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('submitted', 'under_review', 'closed') DEFAULT 'submitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_at TIMESTAMP NULL,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE research_documents
ADD CONSTRAINT fk_research_documents_revision_round
FOREIGN KEY (revision_round_id) REFERENCES revision_rounds(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    payment_type ENUM('initial', 'sample_size') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    paymob_order_id BIGINT NULL,
    paymob_transaction_id BIGINT NULL,
    payment_method VARCHAR(50) NULL,
    failure_reason VARCHAR(500) NULL,
    receipt_url VARCHAR(500),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_paymob_order_id (paymob_order_id),
    INDEX idx_paymob_transaction_id (paymob_transaction_id),
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

CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL UNIQUE,
    certificate_number VARCHAR(100) NOT NULL UNIQUE,
    issued_by INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES research_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE CASCADE
);
