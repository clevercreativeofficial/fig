<?php
require_once __DIR__ . '/../../../path.php';
require_once CONFIG . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($conn)) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$title = $input['title'] ?? '';
$artist = $input['artist'] ?? '';
$role = $input['user_role'] ?? '';
$year = (int)($input['release_year'] ?? 0);
$spotifyUrl = $input['spotifyUrl'] ?? '';
$featured = !empty($input['featured']) ? 1 : 0;
$live = !empty($input['live']) ? 1 : 0;


// Validation
if (!$title) {
    echo json_encode([
        'success' => false,
        'message' => 'Title is required'
    ]);
    exit;
} elseif (!$artist) {
    echo json_encode([
        'success' => false,
        'message' => 'Artist is required'
    ]);
    exit;
} elseif (!$role) {
    echo json_encode([
        'success' => false,
        'message' => 'Role is required'
    ]);
    exit;
} elseif ($year < 2000 || $year > 2030) {
    echo json_encode([
        'success' => false,
        'message' => 'Year must be between 2000 and 2030'
    ]);
    exit;
} elseif (!filter_var($spotifyUrl, FILTER_VALIDATE_URL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Spotify URL'
    ]);
    exit;
} elseif (strpos($spotifyUrl, 'open.spotify.com/embed/') === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Spotify URL must be an embed link (e.g. https://open.spotify.com/embed/track/...)'
    ]);
    exit;
}

// check if track already is featured
if ($featured == 1) {
    $checkStmt = $conn->prepare("SELECT id FROM tracks WHERE featured = 1");
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Only one track can be featured at a time'
        ]);
        exit;
    }
}

// Use prepared statements (mandatory fix)
$stmt = $conn->prepare("
    INSERT INTO tracks (title, artist, user_role, release_year, spotify_url, featured, live_on_site)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("sssissi", $title, $artist, $role, $year, $spotifyUrl, $featured, $live);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Track saved successfully'
    ]);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
    exit;
}