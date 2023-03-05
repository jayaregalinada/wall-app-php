<?php

declare(strict_types=1);
/**
 * This file is for login purposes ONLY
 */

session_start();

$_SESSION['errors'] = [];
$_SESSION['old'] = [];

// We implemented a CSRF (Cross-Site Request Forgery) to avoid hackers send request from our registration form
if (empty($_SESSION['token'])) {
    // Check if token is already exists
    $_SESSION['token'] = bin2hex(random_bytes(32)); // 👈 More secure and unpredictable hash
}

if (
    isset($_SESSION['token'], $_POST['token']) // 👈 Check if token exists
    && empty($_POST['token']) // 👈 Check if no value found in token upon submitting the form
    && !hash_equals($_SESSION['token'], $_POST['token']) // 👈 A much better === (identical operator)
) {
    $_SESSION['errors'][] = 'Token is invalid';
}

include('views/login.html.php');
