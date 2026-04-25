<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailService {
    private $config;

    public function __construct() {
        $configPath = __DIR__ . '/../../config/mail.php';
        $this->config = file_exists($configPath) ? require $configPath : [];
    }

    public function sendTemplate($templateKey, array $recipient, array $data = []) {
        $template = $this->buildTemplate($templateKey, $recipient, $data);

        return $this->sendEmail(
            (string) ($recipient['email'] ?? ''),
            $template['subject'],
            $template['html'],
            $template['text'],
            (string) ($recipient['name'] ?? '')
        );
    }

    public function sendEmail($toEmail, $subject, $htmlBody, $textBody = null, $toName = '') {
        $toEmail = trim((string) $toEmail);
        $subject = trim((string) $subject);
        $htmlBody = (string) $htmlBody;
        $textBody = $textBody !== null ? (string) $textBody : '';

        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Recipient email is invalid.');
        }

        if ($subject === '') {
            throw new Exception('Email subject is required.');
        }

        if (trim($htmlBody) === '' && trim($textBody) === '') {
            throw new Exception('Email body is required.');
        }

        if (!class_exists(PHPMailer::class)) {
            throw new Exception('PHPMailer is not installed. Run composer install first.');
        }

        $mailer = new PHPMailer(true);
        $this->configureMailer($mailer);

        $fromAddress = trim((string) ($this->config['from_address'] ?? ''));
        if ($fromAddress === '') {
            throw new Exception('MAIL_FROM_ADDRESS is not configured.');
        }

        $fromName = trim((string) ($this->config['from_name'] ?? $this->config['app_name'] ?? 'IRP System'));
        $mailer->setFrom($fromAddress, $fromName);
        $mailer->addAddress($toEmail, trim($toName));

        $replyToAddress = trim((string) ($this->config['reply_to_address'] ?? ''));
        if ($replyToAddress !== '') {
            $replyToName = trim((string) ($this->config['reply_to_name'] ?? $fromName));
            $mailer->addReplyTo($replyToAddress, $replyToName);
        }

        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody !== '' ? $textBody : trim(strip_tags($htmlBody));

        return $mailer->send();
    }

    private function configureMailer(PHPMailer $mailer) {
        $host = trim((string) ($this->config['host'] ?? ''));
        if ($host === '') {
            throw new Exception('MAIL_HOST is not configured.');
        }

        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = (int) ($this->config['port'] ?? 587);
        $mailer->Timeout = (int) ($this->config['timeout'] ?? 20);
        $mailer->CharSet = 'UTF-8';
        $mailer->SMTPDebug = (int) ($this->config['debug'] ?? SMTP::DEBUG_OFF);

        $username = trim((string) ($this->config['username'] ?? ''));
        $password = (string) ($this->config['password'] ?? '');
        $mailer->SMTPAuth = $username !== '';
        if ($mailer->SMTPAuth) {
            $mailer->Username = $username;
            $mailer->Password = $password;
        }

        $encryption = strtolower(trim((string) ($this->config['encryption'] ?? 'tls')));
        if ($encryption === 'ssl') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
    }

    private function buildTemplate($templateKey, array $recipient, array $data) {
        $name = trim((string) ($recipient['name'] ?? ''));
        if ($name === '') {
            $name = 'عميلنا العزيز';
        }

        $appName = trim((string) ($this->config['app_name'] ?? 'IRP System'));
        $loginUrl = trim((string) ($data['login_url'] ?? $this->buildLoginUrl()));
        $supportEmail = trim((string) ($data['support_email'] ?? ($this->config['reply_to_address'] ?? '')));

        switch ($templateKey) {
            case 'user_activated':
                $subject = 'تم تفعيل حسابك في ' . $appName;
                $headline = 'تم تفعيل الحساب بنجاح';
                $body = 'تم تفعيل حسابك بنجاح. يمكنك الآن تسجيل الدخول إلى النظام ومتابعة إجراءاتك من صفحة الدخول.';
                break;

            case 'staff_account_created':
                $subject = 'تم إنشاء حسابك الوظيفي في ' . $appName;
                $headline = 'تم إنشاء الحساب';
                $roleLabel = trim((string) ($data['role_label'] ?? ''));
                $body = 'تم إنشاء حسابك الوظيفي بنجاح' . ($roleLabel !== '' ? ' بوصفه ' . $roleLabel : '') . '. يمكنك الآن استخدام بيانات الدخول المرسلة إليك للوصول إلى النظام.';
                break;

            default:
                throw new Exception('Unknown mail template: ' . $templateKey);
        }

        $html = $this->renderTemplate($appName, $name, $headline, $body, $loginUrl, $supportEmail);
        $text = $this->renderTextTemplate($appName, $name, $headline, $body, $loginUrl, $supportEmail);

        return [
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ];
    }

    private function renderTemplate($appName, $name, $headline, $body, $loginUrl, $supportEmail) {
        $appNameEscaped = htmlspecialchars($appName, ENT_QUOTES, 'UTF-8');
        $nameEscaped = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $headlineEscaped = htmlspecialchars($headline, ENT_QUOTES, 'UTF-8');
        $bodyEscaped = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
        $loginUrlEscaped = htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8');
        $supportEmailEscaped = htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8');

        $button = $loginUrl !== ''
            ? '<p style="margin:24px 0 0;"><a href="' . $loginUrlEscaped . '" style="display:inline-block;background:#1f2937;color:#fff;text-decoration:none;padding:12px 20px;border-radius:0;">الانتقال إلى صفحة الدخول</a></p>'
            : '';

        $supportLine = $supportEmail !== ''
            ? '<p style="margin:24px 0 0;color:#374151;">للمساعدة، تواصل مع الإدارة عبر <a href="mailto:' . $supportEmailEscaped . '">' . $supportEmailEscaped . '</a>.</p>'
            : '';

        return '<!doctype html>'
            . '<html lang="ar" dir="rtl">'
            . '<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $headlineEscaped . '</title></head>'
            . '<body style="margin:0;background:#f7f7f3;font-family:Arial,Helvetica,sans-serif;color:#111827;">'
            . '<div style="max-width:640px;margin:0 auto;padding:32px 20px;">'
            . '<div style="background:#ffffff;border:1px solid #111827;padding:32px;">'
            . '<p style="margin:0 0 12px;color:#6b7280;">' . $appNameEscaped . '</p>'
            . '<h1 style="margin:0 0 16px;font-size:24px;line-height:1.4;">' . $headlineEscaped . '</h1>'
            . '<p style="margin:0 0 12px;">مرحباً ' . $nameEscaped . '،</p>'
            . '<p style="margin:0;line-height:1.9;">' . $bodyEscaped . '</p>'
            . $button
            . $supportLine
            . '</div>'
            . '</div>'
            . '</body>'
            . '</html>';
    }

    private function renderTextTemplate($appName, $name, $headline, $body, $loginUrl, $supportEmail) {
        $lines = [
            $appName,
            $headline,
            '',
            'مرحباً ' . $name . '،',
            $body,
        ];

        if ($loginUrl !== '') {
            $lines[] = '';
            $lines[] = 'صفحة الدخول: ' . $loginUrl;
        }

        if ($supportEmail !== '') {
            $lines[] = '';
            $lines[] = 'للمساعدة: ' . $supportEmail;
        }

        return implode("\n", $lines);
    }

    private function buildLoginUrl() {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $baseUrl = rtrim($baseUrl, '/');

        return $baseUrl === '' ? '/login' : $baseUrl . '/login';
    }
}