<?php

declare(strict_types=1);
/**
 * This file is for registration purposes ONLY
 */

session_start();

$_SESSION['errors'] = []; // 👈 To hold any errors
$_SESSION['old'] = []; // 👈 To hold old fields after it submitted

if (isset($_SESSION['success'])) {
    // Always remove the success message
    unset($_SESSION['success']);
}

/**
 * This will validate the field value
 * Return ❌ `false` if the value has numeric characters
 * Otherwise, return ✅ `true` if field value is valid
 */
function validateField(string $value): bool
{
    // Even though we can use preg_match here, let's try an old way fashion

    $valueToArray = str_split($value);
    // 👆 Let's make the $value to an array, so we can iterate it
    foreach ($valueToArray as $letter) {
        if (is_numeric($letter)) {
            // 👆 Let's check if each letter "is numeric" (pun intended)
            return false; // 👈 We immediately return false, so it won't check the other characters
        }
    }

    return true; // 👈 Always use Happy Path 🙂
}

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

if (isset($_POST['first_name'])) {
    $_SESSION['old']['first_name'] = $_POST['first_name'];
    if (!validateField($_POST['first_name'])) {
        $_SESSION['errors'][] = 'First name is invalid';
    }
}

if (isset($_POST['last_name'])) {
    $_SESSION['old']['last_name'] = $_POST['last_name'];
    if (!validateField($_POST['last_name'])) {
        $_SESSION['errors'][] = 'Last name is invalid';
    }
}

if (isset($_POST['email'])) {
    $_SESSION['old']['email'] = $_POST['email'];

    $sanitizedEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    // 👆 That will remove or FILTER any illegal characters from the email $_POST['email']

    // 🎯 Practice to limit creating variables if you're NOT intended to reuse the variable
    // if (!filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
    // 👆 You can use this if statement without using any variables
    if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
        // 👆 That will validate email address format

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
    // 👆 That will check if the form has been submitted

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
        // [❓] This is just the inverted version of (!$execute) statement, why not use else?
        // [✅] Nothing personal, but try to avoid using: else, if else, as much as possible, it can be nasty sometimes
        $_SESSION['success'] = 'Successful registration';
    }
}

include('views/register.html.php');

/**
 * 💡 Improvements that you can implement:
 * - Hash the password, NEVER-EVER saved raw password to the database
 * - Try adding `password_confirmation` and make sure that the `password` and `password_confirmation` are the same
 */