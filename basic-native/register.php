<?php

declare(strict_types=1);
/**
 * This file is for registration purposes
 */

session_start();

$_SESSION['errors'] = []; // 👈 To hold any errors
$_SESSION['old'] = []; // 👈 To hold old fields after it submitted

if (isset($_SESSION['success'])) {
    // Always remove the success message
    unset($_SESSION['success']);
}

function validateField(string $value): bool {
    // This will validate the field value if it has numeric character
    $valueToArray = str_split($value);
    foreach ($valueToArray as $letter) {
        if (is_numeric($letter)) {
            return false;
        }
    }

    return true;
}

if (empty($_SESSION['token'])) {
    // Check if token is already
    $_SESSION['token'] = bin2hex(random_bytes(32)); // 👈 More secure and unpredictable hash
}

if (isset($_SESSION['token'], $_POST['token']) && empty($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])) {
    $_SESSION['errors'][] = 'Token is invalid';
}

if (isset($_POST['first_name'])) {
    $_SESSION['old']['first_name'] = $_POST['first_name'];
    // Add validation for the first_name
    if (!validateField($_POST['first_name'])) {
        $_SESSION['errors'][] = 'First name is invalid';
    }
}

if (isset($_POST['last_name'])) {
    $_SESSION['old']['last_name'] = $_POST['last_name'];
    // Add validation for the last_name
    if (!validateField($_POST['last_name'])) {
        $_SESSION['errors'][] = 'Last name is invalid';
    }
}

if (isset($_POST['email'])) {
    $_SESSION['old']['email'] = $_POST['email'];
    // Add validation for email
    if (!filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
        // Check if email is actually valid email format
        $_SESSION['errors'][] = 'Email is invalid';
    }
}

if (isset($_POST['password'])) {
    // Add validation for password
    if (strlen($_POST['password']) < 4) {
        $_SESSION['errors'][] = 'Password must have at least four (4) characters';
    }
}

if (!empty($_POST) && count($_SESSION['errors']) === 0) {
    // 👆 This will check if the form has been submitted

    $_SESSION['old'] = []; // Reset any old fields
    $connection = require('connection.php');
    // 👆 Let's get the instance of mysqli to allow use to insert to the database
    $usersTable = constant('TABLE_USERS'); // 👈 We get the constant value from the `connection.php`
    $query = <<<SQL
INSERT INTO $usersTable (first_name, last_name, email, password)
VALUES ('{$_POST['first_name']}', '{$_POST['last_name']}', '{$_POST['email']}', '{$_POST['password']}');
SQL;
    // 👆 We use a HEREDOC to allow us to write multiline strings without issues

    $execute = $connection->query($query);
    // 👆 Let's attempt to execute the $query, any result will be stored in $execute variable
    if (!$execute) {
        $_SESSION['errors'][] = 'Something went wrong when trying to save to the Database';
    }
    if ($execute) {
        // [❓] This is just the inverted version of (!$execute) statement, why not use else
        // [✅] Nothing personal, but try to avoid else, if else as much as possible, it can be nasty sometimes
        $_SESSION['success'] = 'Successful registration';
    }
}

include('views/register.html.php');
