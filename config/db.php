<?php
require_once __DIR__ . '/../path.php';
require_once ROOT_PATH . '/config/constants.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>