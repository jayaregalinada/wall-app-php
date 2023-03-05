<?php

declare(strict_types=1);
/**
 * This file is for registration purposes
 */

session_start();

$_SESSION['errors'] = [];

if (empty($_SESSION['token'])) {
    // Check if token is already
    $_SESSION['token'] = bin2hex(random_bytes(32)); // 👈 More secure and unpredictable hash
}

if (isset($_SESSION['token'], $_POST['token']) && empty($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])) {
    $_SESSION['errors'][] = 'Token is invalid';
}

if (isset($_POST['first_name'])) {
    // Add validation for the first_name
}

if (isset($_POST['last_name'])) {
    // Add validation for the last_name
}

if (isset($_POST['email'])) {
    // Add validation for email
    if (!filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
        // Check if email is actually valid email format
        $_SESSION['errors'][] = 'Email is invalid';
    }
}

if (isset($_POST['password'])) {
    // Add validation for password
}

include('views/register.html.php');
