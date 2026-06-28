<?php

require_once __DIR__ . '/../path.php';

// Set JSON response header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ============================================================
// VALIDATE & SANITIZE INPUT
// ============================================================

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$artist = trim($_POST['artist'] ?? '');
$service = trim($_POST['service'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($service)) {
    $errors[] = 'Service selection is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// If validation fails
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// ============================================================
// SANITIZE FOR OUTPUT
// ============================================================

$name_safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email_safe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$artist_safe = htmlspecialchars($artist, ENT_QUOTES, 'UTF-8');
$service_safe = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
$timeline_safe = htmlspecialchars($timeline, ENT_QUOTES, 'UTF-8');
$message_safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// ============================================================
// PREPARE EMAIL
// ============================================================

$to = 'hello@figpro.com'; // Your email address
$subject = "New Booking Enquiry: {$artist_safe} — {$service_safe}";

// Admin notification email (to you)
$admin_message = "New booking enquiry received:\n\n";
$admin_message .= "Name: {$name_safe}\n";
$admin_message .= "Email: {$email_safe}\n";
$admin_message .= "Artist/Project: {$artist_safe}\n";
$admin_message .= "Service: {$service_safe}\n";
$admin_message .= "Timeline: {$timeline_safe}\n\n";
$admin_message .= "Message:\n{$message_safe}\n\n";
$admin_message .= "---\n";
$admin_message .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
$admin_message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Headers for admin email
$admin_headers = "From: {$email_safe}\r\n";
$admin_headers .= "Reply-To: {$email_safe}\r\n";
$admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Client confirmation email
$client_subject = "Got your enquiry — FIG Pro";
$client_message = "Hi {$name_safe},\n\n";
$client_message .= "Thanks for reaching out about your {$service_safe} project.\n\n";
$client_message .= "I read every enquiry myself and will be in touch within 48 hours.\n\n";
$client_message .= "In the meantime, if you have any questions, feel free to:\n";
$client_message .= "• Email: hello@figpro.com\n";
$client_message .= "• WhatsApp: +257 69 979 642\n\n";
$client_message .= "Looking forward to exploring what we can create together.\n\n";
$client_message .= "— A.\n";
$client_message .= "FIG Pro\n";
$client_message .= "Bujumbura, Burundi";

$client_headers = "From: hello@figpro.com\r\n";
$client_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ============================================================
// SEND EMAILS WITH LOGGING
// ============================================================

// Log submission for debugging
$log_file = __DIR__ . '/../logs/bookings.log';
$log_dir = dirname($log_file);

// Create logs directory if it doesn't exist
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

$log_entry = "[" . date('Y-m-d H:i:s') . "] ";
$log_entry .= "To: {$email_safe} | ";
$log_entry .= "Name: {$name_safe} | ";
$log_entry .= "Service: {$service_safe}\n";

// Attempt to send admin email
$admin_sent = @mail($to, $subject, $admin_message, $admin_headers);

if ($admin_sent) {
    // Email sent successfully
    $log_entry .= "Status: SUCCESS - Email sent to {$to}\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Also send client confirmation (don't fail if this doesn't work)
    @mail($email, $client_subject, $client_message, $client_headers);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Enquiry received. I\'ll be in touch within 48 hours.'
    ]);
} else {
    // Email failed - log the error
    $log_entry .= "Status: FAILED - mail() function returned false\n";
    $log_entry .= "Possible causes:\n";
    $log_entry .= "  1. Server mail() not configured\n";
    $log_entry .= "  2. Invalid 'From' address\n";
    $log_entry .= "  3. SMTP not working\n";
    $log_entry .= "  4. Recipient email rejected by server\n\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to send enquiry at this time. Please email directly: hello@figpro.com'
    ]);
}

exit;
?>