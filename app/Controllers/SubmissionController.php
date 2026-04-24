<?php

require __DIR__ . '/../../config/database.php';

class SubmissionController {

    public function create() {
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../Views/student/submission_form.php';
    }

    public function show() {
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../Views/student/submission_details.php';
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
            header('Location: ' . BASE_URL . '/student/submission/create');
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
            header('Location: ' . BASE_URL . '/student/submission/create');
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
                    "INSERT INTO research_documents (submission_id, document_type, file_path, version, is_current)
                     VALUES (?, ?, ?, 1, 1)"
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
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;

        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['submission_error'] = 'حدث خطأ أثناء تقديم البحث. يرجى المحاولة مرة أخرى.';
            header('Location: ' . BASE_URL . '/student/submission/create');
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

    public function submitRevision($submissionId) {
        AuthMiddleware::requireRole('student');
        $studentId = (int) $_SESSION['user_id'];
        $db = Database::getConnection();

        // 1. Verify ownership and status
        $stmt = $db->prepare("SELECT status FROM research_submissions WHERE id = ? AND student_id = ?");
        $stmt->bind_param('ii', $submissionId, $studentId);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();

        if (!$submission || $submission['status'] !== 'revision_requested') {
            $_SESSION['submission_error'] = "لا يمكن تقديم تعديلات لهذا البحث حالياً.";
            header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
            exit;
        }

        $revisableDocumentTypes = [
            'protocol',
            'review_application',
            'conflict_of_interest',
            'irb_checklist',
            'pi_consent',
            'patient_consent',
            'photos_biopsies_consent',
            'research_file'
        ];

        $revisionFiles = [];
        if (isset($_FILES['revised_files']) && is_array($_FILES['revised_files']['name'] ?? null)) {
            foreach ($revisableDocumentTypes as $documentType) {
                if (!isset($_FILES['revised_files']['error'][$documentType])) {
                    continue;
                }
                $errorCode = (int) $_FILES['revised_files']['error'][$documentType];
                if ($errorCode === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($errorCode !== UPLOAD_ERR_OK) {
                    $_SESSION['submission_error'] = "فشل رفع الملف الخاص بـ {$documentType}.";
                    header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
                    exit;
                }

                $file = [
                    'name' => $_FILES['revised_files']['name'][$documentType],
                    'type' => $_FILES['revised_files']['type'][$documentType],
                    'tmp_name' => $_FILES['revised_files']['tmp_name'][$documentType],
                    'error' => $errorCode,
                    'size' => $_FILES['revised_files']['size'][$documentType],
                ];

                $validation = $this->validateFile($file);
                if ($validation !== true) {
                    $_SESSION['submission_error'] = $validation;
                    header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
                    exit;
                }

                $revisionFiles[$documentType] = $file;
            }
        }

        // Backward compatibility with the previous single-file field.
        if (empty($revisionFiles) && isset($_FILES['revised_file']) && ($_FILES['revised_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $singleFile = $_FILES['revised_file'];
            $validation = $this->validateFile($singleFile);
            if ($validation !== true) {
                $_SESSION['submission_error'] = $validation;
                header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
                exit;
            }
            $revisionFiles['research_file'] = $singleFile;
        }

        if (empty($revisionFiles)) {
            $_SESSION['submission_error'] = "يرجى رفع ملف تعديل واحد على الأقل.";
            header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
            exit;
        }

        $db->begin_transaction();
        try {
            // 2. Create a revision round (one upload action can include multiple document updates)
            $createRoundStmt = $db->prepare("INSERT INTO revision_rounds (submission_id, student_id, status, submitted_at) VALUES (?, ?, 'submitted', CURRENT_TIMESTAMP)");
            $createRoundStmt->bind_param('ii', $submissionId, $studentId);
            $createRoundStmt->execute();
            $revisionRoundId = (int) $createRoundStmt->insert_id;

            // 3. Upload files, mark previous current file for each type as not current, and insert new version
            $uploadDir = __DIR__ . '/../../storage/submissions/' . $submissionId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            foreach ($revisionFiles as $documentType => $file) {
                $versionQuery = $db->prepare("SELECT COALESCE(MAX(version), 0) AS max_version FROM research_documents WHERE submission_id = ? AND document_type = ?");
                $versionQuery->bind_param('is', $submissionId, $documentType);
                $versionQuery->execute();
                $versionRow = $versionQuery->get_result()->fetch_assoc();
                $nextVersion = (int) ($versionRow['max_version'] ?? 0) + 1;

                $deactivateCurrent = $db->prepare("UPDATE research_documents SET is_current = 0 WHERE submission_id = ? AND document_type = ? AND is_current = 1");
                $deactivateCurrent->bind_param('is', $submissionId, $documentType);
                $deactivateCurrent->execute();

                $fileName = 'revision_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
                $filePath = $uploadDir . '/' . $fileName;
                $relativePath = 'submissions/' . $submissionId . '/' . $fileName;

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new Exception("Error uploading revision file for {$documentType}.");
                }

                $insertDocStmt = $db->prepare(
                    "INSERT INTO research_documents (submission_id, document_type, file_path, version, is_current, revision_round_id)
                     VALUES (?, ?, ?, ?, 1, ?)"
                );
                $insertDocStmt->bind_param('issii', $submissionId, $documentType, $relativePath, $nextVersion, $revisionRoundId);
                $insertDocStmt->execute();
            }

            // 4. Update submission status to under_review
            $stmt = $db->prepare("UPDATE research_submissions SET status = 'under_review' WHERE id = ?");
            $stmt->bind_param('i', $submissionId);
            $stmt->execute();

            // 5. Reset review status to pending
            $stmt = $db->prepare("UPDATE reviews SET review_status = 'pending' WHERE submission_id = ?");
            $stmt->bind_param('i', $submissionId);
            $stmt->execute();

            $db->commit();
            $_SESSION['submission_success'] = "تم إرسال التعديلات بنجاح. البحث الآن قيد المراجعة مرة أخرى.";
            header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
            exit;

        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['submission_error'] = "حدث خطأ أثناء رفع التعديلات: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/student/submissions/' . $submissionId);
            exit;
        }
    }
}
