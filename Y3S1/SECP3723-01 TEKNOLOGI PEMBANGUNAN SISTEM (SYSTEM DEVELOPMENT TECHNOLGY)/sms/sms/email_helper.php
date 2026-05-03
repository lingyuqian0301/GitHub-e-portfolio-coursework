<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// SMTP Configuration (Gmail) - Loaded from .env file
define('MAIL_HOST', $_ENV['MAIL_HOST']);
define('MAIL_PORT', (int)$_ENV['MAIL_PORT']); // SSL
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME']);
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD']); // App password
define('MAIL_FROM_ADDRESS', $_ENV['MAIL_FROM_ADDRESS']);
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME']);

// Localhost Mode - Set to true to save emails locally instead of sending via SMTP
define('LOCALHOST_MODE', $_ENV['LOCALHOST_MODE'] === 'true');

function format_text_to_html($text) {
    // Convert markdown-style formatting to HTML
    $text = htmlspecialchars($text);
    
    // Convert **bold** to <strong>bold</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Convert *italic* to <em>italic</em>
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    
    // Convert # Heading to <h2>
    $text = preg_replace('/^# (.*?)$/m', '<h2>$1</h2>', $text);
    
    // Convert ## Heading to <h3>
    $text = preg_replace('/^## (.*?)$/m', '<h3>$1</h3>', $text);
    
    // Convert line breaks to <br>
    $text = nl2br($text);
    
    // Wrap in HTML body tags
    $html = '<html><head><meta charset="UTF-8"></head><body style="font-family: Arial, sans-serif; line-height: 1.6;">' . $text . '</body></html>';
    
    return $html;
}

function send_notification_email($to, $subject, $message) {
    $logDate = date('Y-m-d H:i:s');
    $logMessage = "[$logDate] To: $to | Subject: $subject | Message: " . substr($message, 0, 120) . "..." . PHP_EOL;
    file_put_contents('email_log.txt', $logMessage, FILE_APPEND);

    if (send_via_smtp($to, $subject, $message)) {
        file_put_contents('email_log.txt', "[$logDate] Status: ✓ Sent via SMTP" . PHP_EOL, FILE_APPEND);
        return true;
    }

    $errorMsg = "[$logDate] Status: ✗ Failed to send via SMTP" . PHP_EOL;
    file_put_contents('email_log.txt', $errorMsg, FILE_APPEND);
    return false;
}

function send_via_smtp($to, $subject, $message) {
    $logDate = date('Y-m-d H:i:s');
    $html_message = format_text_to_html($message);
    
    error_log("[$logDate] SMTP: Connecting to " . MAIL_HOST . ":" . MAIL_PORT);
    
    // Connect to Gmail SMTP with SSL
    $fp = @fsockopen('ssl://' . MAIL_HOST, MAIL_PORT, $errno, $errstr, 30);
    
    if (!$fp) {
        error_log("[$logDate] SMTP Connection Failed: [$errno] $errstr");
        file_put_contents('email_log.txt', "[$logDate] SMTP Connection Error: $errstr\n", FILE_APPEND);
        return false;
    }
    
    error_log("[$logDate] SMTP: Connected successfully");
    stream_set_timeout($fp, 10);
    
    // Read server response
    $response = fgets($fp, 1024);
    error_log("[$logDate] SMTP Server: " . trim($response));
    
    // Send EHLO
    fwrite($fp, "EHLO localhost\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] EHLO Response: " . trim($response));
    
    // Consume additional EHLO responses
    while (substr($response, 3, 1) === '-') {
        $response = fgets($fp, 1024);
        error_log("[$logDate] EHLO: " . trim($response));
    }
    
    // Start authentication
    fwrite($fp, "AUTH LOGIN\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] AUTH LOGIN Response: " . trim($response));
    
    if (strpos($response, '334') === false) {
        error_log("[$logDate] AUTH LOGIN failed");
        fclose($fp);
        return false;
    }
    
    // Send username
    fwrite($fp, base64_encode(MAIL_USERNAME) . "\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] Username Auth Response: " . trim($response));
    
    // Send password
    fwrite($fp, base64_encode(MAIL_PASSWORD) . "\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] Password Auth Response: " . trim($response));
    
    if (strpos($response, '235') === false) {
        error_log("[$logDate] Authentication failed: " . trim($response));
        fclose($fp);
        file_put_contents('email_log.txt', "[$logDate] SMTP Auth Failed\n", FILE_APPEND);
        return false;
    }
    
    error_log("[$logDate] Authentication successful");
    
    // Send MAIL FROM
    fwrite($fp, "MAIL FROM:<" . MAIL_FROM_ADDRESS . ">\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] MAIL FROM Response: " . trim($response));
    
    // Send RCPT TO
    fwrite($fp, "RCPT TO:<" . $to . ">\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] RCPT TO Response: " . trim($response));
    
    // Send DATA
    fwrite($fp, "DATA\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] DATA Response: " . trim($response));
    
    if (strpos($response, '354') === false) {
        error_log("[$logDate] DATA command failed");
        fclose($fp);
        return false;
    }
    
    // Prepare email headers
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">\r\n";
    $headers .= "To: " . $to . "\r\n";
    $headers .= "Subject: " . $subject . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Date: " . date('r') . "\r\n";
    
    $email_body = $headers . "\r\n" . $html_message;
    
    // Send email data
    fwrite($fp, $email_body . "\r\n.\r\n");
    $response = fgets($fp, 1024);
    error_log("[$logDate] Send Response: " . trim($response));
    
    if (strpos($response, '250') === false) {
        error_log("[$logDate] Email send failed: " . trim($response));
        fclose($fp);
        return false;
    }
    
    // Send QUIT
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    
    error_log("[$logDate] SMTP: Email sent successfully to $to");
    return true;
}

// ===== NOTIFICATION FUNCTIONS =====

/**
 * Send Course Registration Approval Email
 */
function send_registration_approval_email($to, $fullName, $courseName, $courseCode, $section, $semester) {
    $subject = "Course Registration Approved - $courseCode";
    $message = "## Course Registration Approved\n\n";
    $message .= "Dear $fullName,\n\n";
    $message .= "Great news! Your course registration has been **APPROVED**.\n\n";
    $message .= "### Course Details:\n";
    $message .= "- **Course Code:** $courseCode\n";
    $message .= "- **Course Name:** $courseName\n";
    $message .= "- **Section:** $section\n";
    $message .= "- **Semester:** $semester\n\n";
    $message .= "You are now enrolled in this course. Please check your course page for more information.\n\n";
    $message .= "Best regards,\nSMS Administration Team";
    
    return send_notification_email($to, $subject, $message);
}

/**
 * Send Course Registration Pending Email (Waiting List)
 */
function send_registration_pending_email($to, $fullName, $courseName, $courseCode, $section, $semester) {
    $subject = "Course Registration Pending - $courseCode";
    $message = "## Registration Pending\n\n";
    $message .= "Dear $fullName,\n\n";
    $message .= "Your course registration is currently **PENDING** - the course is at maximum capacity.\n\n";
    $message .= "### Course Details:\n";
    $message .= "- **Course Code:** $courseCode\n";
    $message .= "- **Course Name:** $courseName\n";
    $message .= "- **Section:** $section\n";
    $message .= "- **Semester:** $semester\n\n";
    $message .= "You will be notified automatically if a seat becomes available.\n\n";
    $message .= "Best regards,\nSMS Administration Team";
    
    return send_notification_email($to, $subject, $message);
}

/**
 * Send Course Cancellation Email
 */
function send_cancellation_email($to, $fullName, $courseName, $courseCode, $section) {
    $subject = "Course Registration Cancelled - $courseCode";
    $message = "## Course Registration Cancelled\n\n";
    $message .= "Dear $fullName,\n\n";
    $message .= "Your registration for the following course has been **CANCELLED**:\n\n";
    $message .= "- **Course Code:** $courseCode\n";
    $message .= "- **Course Name:** $courseName\n";
    $message .= "- **Section:** $section\n\n";
    $message .= "If this was a mistake, you can register again.\n\n";
    $message .= "Best regards,\nSMS Administration Team";
    
    return send_notification_email($to, $subject, $message);
}

/**
 * Send Welcome Email on Registration
 */
function send_welcome_email($to, $fullName, $userType) {
    $subject = "Welcome to Student Management System";
    $message = "## Welcome to SMS\n\n";
    $message .= "Dear $fullName,\n\n";
    $message .= "Your account has been successfully created!\n\n";
    $message .= "**Account Type:** " . ucfirst($userType) . "\n\n";
    $message .= "You can now log in to the system using your email and password.\n\n";
    $message .= "Please keep your login credentials safe.\n\n";
    $message .= "Best regards,\nSMS Administration Team";
    
    return send_notification_email($to, $subject, $message);
}

?>
