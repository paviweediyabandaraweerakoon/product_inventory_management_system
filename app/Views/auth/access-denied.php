<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/* Check if the user is logged in */

$message = $_SESSION['flash_message'] ?? ($message ?? 'You do not have permission to log in.');
unset($_SESSION['flash_message']);

$title = 'Access Denied - Inventory Pro';
$heading = 'Access Denied';
$icon = 'shield-alert';
$iconWrapperClass = 'bg-amber-500/20 text-amber-500';
$headingClass = 'text-amber-600';
$buttonText = 'Back to Login';
$buttonHref = '/login';

require_once __DIR__ . '/../layouts/status_layout.php';