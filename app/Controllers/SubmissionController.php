<?php

require __DIR__ . '/../../config/database.php';

class SubmissionController {

    public function create() {
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../Views/student/submission_form.php';
    }

    public function store() {
        AuthMiddleware::requireRole('student');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $studentId = (int) $_SESSION['user_id'];
        $db = Database::getConnection();

        // Validate required text fields
        $title = trim($_POST['title'] ?? '');
        $principalInvestigator = trim($_POST['principal_investigator'] ?? '');
        $coInvestigators = trim($_POST['co_investigators'] ?? '');

        // Fetch department and specialty from database
        $stmt = $db->prepare("SELECT department, specialty FROM users WHERE id = ?");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $_SESSION['submission_error'] = 'حدث خطأ في استرجاع بيانات المستخدم. يرجى المحاولة مرة أخرى.';
            header('Location: /student/submission/create');
            exit;
        }

        $department = $user['department'];
        $specialization = $user['specialty'];

        $errors = [];

        if (empty($title)) $errors[] = 'عنوان البحث مطلوب';
        if (empty($principalInvestigator)) $errors[] = 'اسم الباحث الرئيسي مطلوب';

        // Validate required documents
        $requiredDocuments = [
            'document_protocol' => 'protocol',
            'document_review_application' => 'review_application',
            'document_conflict_of_interest' => 'conflict_of_interest',
            'document_irb_checklist' => 'irb_checklist',
            'document_pi_consent' => 'pi_consent',
            'document_patient_consent' => 'patient_consent'
        ];

        $optionalDocuments = [
            'document_photos_biopsies_consent' => 'photos_biopsies_consent'
        ];

        $files = [];

        foreach ($requiredDocuments as $formFieldName => $documentType) {
            if (!isset($_FILES[$formFieldName]) || $_FILES[$formFieldName]['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "المستند المطلوب ({$documentType}) لم يتم رفعه";
                continue;
            }

            $file = $_FILES[$formFieldName];
            $validation = $this->validateFile($file);
            if ($validation !== true) {
                $errors[] = $validation;
                continue;
            }

            $files[$documentType] = $file;
        }

        // Optional documents
        foreach ($optionalDocuments as $formFieldName => $documentType) {
            if (isset($_FILES[$formFieldName]) && $_FILES[$formFieldName]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$formFieldName];
                $validation = $this->validateFile($file);
                if ($validation !== true) {
                    $errors[] = $validation;
                    continue;
                }
                $files[$documentType] = $file;
            }
        }

        // If there are validation errors
        if (!empty($errors)) {
            $_SESSION['submission_error'] = implode(', ', $errors);
            header('Location: /student/submission/create');
            exit;
        }

        // Start transaction
        $db->begin_transaction();

        try {
            // Insert into research_submissions
            $stmt = $db->prepare(
                "INSERT INTO research_submissions (student_id, title, principal_investigator, co_investigators, status)
                 VALUES (?, ?, ?, ?, 'submitted')"
            );

            $stmt->bind_param('isss', $studentId, $title, $principalInvestigator, $coInvestigators);
            $stmt->execute();

            if ($stmt->errno !== 0) {
                throw new Exception("Error inserting submission: " . $stmt->error);
            }

            $submissionId = $stmt->insert_id;

            // Upload files and insert into research_documents
            $uploadDir = __DIR__ . '/../../storage/submissions/' . $submissionId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($files as $documentType => $file) {
                $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
                $filePath = $uploadDir . '/' . $fileName;

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new Exception("Error uploading file: {$file['name']}");
                }

                // Save relative path for database
                $relativePath = 'submissions/' . $submissionId . '/' . $fileName;

                $stmt = $db->prepare(
                    "INSERT INTO research_documents (submission_id, document_type, file_path)
                     VALUES (?, ?, ?)"
                );

                $stmt->bind_param('iss', $submissionId, $documentType, $relativePath);
                $stmt->execute();

                if ($stmt->errno !== 0) {
                    throw new Exception("Error inserting document: " . $stmt->error);
                }
            }

            // Log the action
            $action = 'research_submitted';
            $details = "تم تقديم بحث جديد: {$title}";
            $stmt = $db->prepare(
                "INSERT INTO system_logs (user_id, submission_id, action, details)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param('iiss', $studentId, $submissionId, $action, $details);
            $stmt->execute();

            $db->commit();

            $_SESSION['submission_success'] = "تم تقديم بحثك بنجاح. يمكنك متابعة حالة البحث من لوحة التحكم.";
            header('Location: /student/dashboard');
            exit;

        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['submission_error'] = 'حدث خطأ أثناء تقديم البحث. يرجى المحاولة مرة أخرى.';
            header('Location: /student/submission/create');
            exit;
        }
    }

    private function validateFile($file) {
        $maxSize = 10 * 1024 * 1024; // 10 MB
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedExtensions = ['pdf', 'doc', 'docx'];

        $fileName = $file['name'];

        // Check file size
        if ($file['size'] > $maxSize) {
            return "الملف {$fileName} يتجاوز الحد الأقصى المسموح (10 ميجابايت)";
        }

        // Check MIME type
        if (!in_array($file['type'], $allowedTypes)) {
            return "نوع الملف {$fileName} غير مدعوم. يرجى استخدام PDF أو DOC أو DOCX";
        }

        // Check file extension
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            return "امتداد الملف {$fileName} غير صحيح. يرجى استخدام PDF أو DOC أو DOCX";
        }

        return true;
    }
}
