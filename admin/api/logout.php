<?php
require_once __DIR__ . '/../../path.php';
require_once CONFIG . '/db.php';

// destroy all connections
session_destroy();
unset($_SESSION['user_id']);

header('location: ' . APP_URL . 'login/');
die();