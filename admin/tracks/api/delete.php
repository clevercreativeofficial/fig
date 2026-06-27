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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid track ID'
    ]);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM tracks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "s", $id);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        'success' => true,
        'message' => 'Track deleted successfully'
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => 'Error deleting track'
    ]);
}