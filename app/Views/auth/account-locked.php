<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** @var string $message */
/* Check if the user is logged in */
$message = $_SESSION['flash_message'] ?? ($message ?? 'Your account is currently locked.');
unset($_SESSION['flash_message']);

$title = 'Account Locked - Inventory Pro';
$heading = 'Account Locked';
$icon = 'lock-keyhole';
$iconWrapperClass = 'bg-red-500/20 text-red-500';
$headingClass = 'text-red-600';
$buttonText = 'Back to Login';
$buttonHref = '/login';

require_once __DIR__ . '/../layouts/status_layout.php';