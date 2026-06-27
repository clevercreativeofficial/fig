<?php
ob_start();

require_once __DIR__ . '/../../path.php';
require_once CONFIG . '/db.php';

header('Content-Type: application/json');

// ─── Config ───────────────────────────────────────────────────
$MAX_ATTEMPTS     = 5;
$LOCKOUT_DURATION = 15 * 60;

function sendJson($code, $data) {
    ob_clean();
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// ─── Method guard ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(405, ['error' => 'Method not allowed.']);
}

// ─── Parse JSON body ──────────────────────────────────────────
$body     = json_decode(file_get_contents('php://input'), true);
$email    = trim(filter_var($body['email'] ?? '', FILTER_SANITIZE_EMAIL));
$password = $body['password'] ?? '';
$remember = $body['remember'] ?? false;

if (empty($email) || empty($password)) {
    sendJson(400, ['error' => 'Email and password are required.']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(400, ['error' => 'Invalid email format.']);
}

// ─── DB guard ─────────────────────────────────────────────────
if (!isset($conn)) {
    sendJson(500, ['error' => 'Database connection not established.']);
}

$user_ip      = $_SERVER['REMOTE_ADDR'];
$current_time = time();

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

// ─── Lockout check ───────────────────────────────────────────
if (isset($_SESSION['login_attempts'][$user_ip])) {
    $attempt_data = $_SESSION['login_attempts'][$user_ip];

    if (isset($attempt_data['locked_until']) && $attempt_data['locked_until'] > $current_time) {
        $remaining = $attempt_data['locked_until'] - $current_time;
        $minutes   = ceil($remaining / 60);
        $seconds   = $remaining % 60;
        sendJson(429, [
            'error'      => "Too many attempts. Try again in {$minutes}m {$seconds}s.",
            'locked'     => true,
            'retryAfter' => $remaining
        ]);
    }

    // Lockout expired — reset
    if (isset($attempt_data['locked_until']) && $attempt_data['locked_until'] <= $current_time) {
        unset($_SESSION['login_attempts'][$user_ip]);
    }
}

// ─── Step 1: Check email ──────────────────────────────────────
$stmt = $conn->prepare("SELECT id, password FROM user WHERE email = ?");

if (!$stmt) {
    sendJson(500, ['error' => 'Query preparation failed.', 'debug' => $conn->error]);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    trackFailedAttempt($user_ip, $email, $MAX_ATTEMPTS, $LOCKOUT_DURATION, $conn);
    $remaining = $MAX_ATTEMPTS - ($_SESSION['login_attempts'][$user_ip]['count'] ?? 0);
    sendJson(401, [
        'error'    => 'No account found with that email.',
        'attempts' => $remaining > 0 ? "{$remaining} attempt(s) remaining." : null
    ]);
}

// ─── Step 2: Verify password ──────────────────────────────────
$userInfo = $result->fetch_assoc();
$stmt->close();

if (!$userInfo['password'] || !password_verify($password, $userInfo['password'])) {
    trackFailedAttempt($user_ip, $email, $MAX_ATTEMPTS, $LOCKOUT_DURATION, $conn);
    $attemptData = $_SESSION['login_attempts'][$user_ip] ?? [];
    $remaining   = $MAX_ATTEMPTS - ($attemptData['count'] ?? 0);

    if (!empty($attemptData['locked_until'])) {
        $left = $attemptData['locked_until'] - time();
        sendJson(429, [
            'error'      => 'Too many attempts. Account locked for ' . ceil($LOCKOUT_DURATION / 60) . ' minute(s).',
            'locked'     => true,
            'retryAfter' => $left
        ]);
    }

    sendJson(401, [
        'error'    => 'Incorrect password.',
        'attempts' => "{$remaining} attempt(s) remaining."
    ]);
}

// ─── Authenticated ────────────────────────────────────────────
session_regenerate_id(true);
$_SESSION['user_id']   = $userInfo['id'];

clearFailedAttempts($user_ip, $email, $conn);

if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, [
        'expires'  => time() + (30 * 24 * 60 * 60),
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

sendJson(200, [
    'success'   => true,
    'message'   => 'Welcome back, ' . htmlspecialchars($userInfo['email']) . '!',
]);

// ─── Helpers ─────────────────────────────────────────────────

function trackFailedAttempt($ip, $email, $maxAttempts, $lockoutDuration, $conn) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = [
            'count'         => 0,
            'first_attempt' => time(),
            'last_attempt'  => time(),
            'emails'        => []
        ];
    }

    $data = &$_SESSION['login_attempts'][$ip];
    $data['count']++;
    $data['last_attempt'] = time();

    if ($email && !in_array($email, $data['emails'])) {
        $data['emails'][] = $email;
    }

    if ($data['count'] >= $maxAttempts) {
        $data['locked_until'] = time() + $lockoutDuration;
        logLockout($ip, $email, $data['count'], $conn);
    }
}

function clearFailedAttempts($ip, $email, $conn) {
    unset($_SESSION['login_attempts'][$ip]);
    clearDatabaseAttempts($ip, $email, $conn);
}

function logLockout($ip, $email, $attempts, $conn) {
    $stmt         = $conn->prepare("INSERT INTO login_attempts (ip_address, email, attempts, locked_until) VALUES (?, ?, ?, ?)");
    $locked_until = date('Y-m-d H:i:s', time() + 900);
    $stmt->bind_param("ssis", $ip, $email, $attempts, $locked_until);
    $stmt->execute();
    $stmt->close();
}

function clearDatabaseAttempts($ip, $email, $conn) {
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ? OR email = ?");
    $stmt->bind_param("ss", $ip, $email);
    $stmt->execute();
    $stmt->close();
}