<?php
require_once __DIR__ . '/../path.php';
require_once CONFIG . '/constants.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . 'login/');
    exit();
}