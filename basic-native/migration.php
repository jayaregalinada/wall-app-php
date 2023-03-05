<?php

declare(strict_types=1);
/**
 * This file is for creating tables to the database
 */

// 👇 This will check if this file run via CLI otherwise it will NOT execute anything
(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('Only for CLI');

// 👇 Lets get the instance of mysqli
$con = require('connection.php');

$queries = [];

$usersTable = constant('TABLE_USERS');

// This is called HEREDOC, allowing multiline string
// or file literal for sending input streams
// allowing us to write NON-PHP string WITHOUT the problem of quotations
$queries[$usersTable] = <<<SQL
CREATE TABLE IF NOT EXISTS $usersTable (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password CHAR(60) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
SQL;

// ✅ Adding IF NOT EXISTS keyword will make sure that the table will ONLY create IF the table exists
// ✅ Adding ON UPDATE CURRENT_TIMESTAMP will make sure that every update, it will generate a new CURRENT_TIMESTAMP

$messagesTable = constant('TABLE_MESSAGES');
$queries[$messagesTable] = <<<SQL
CREATE TABLE IF NOT EXISTS $messagesTable (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    message LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_message_user FOREIGN KEY (id)
    REFERENCES $usersTable(id)
);
SQL;

$commentsTable = constant('TABLE_COMMENTS');
$queries[$commentsTable] = <<<SQL
CREATE TABLE IF NOT EXISTS $commentsTable (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    comment LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comment_user FOREIGN KEY (id)
    REFERENCES $usersTable(id)
);
SQL;

foreach ($queries as $table => $query) {
    echo $con->query($query) ? "Successfully created table: {$table}" . PHP_EOL : 'Something went wrong';
}

// ✅ Once you run this migration using:
// `php migration.php`
// The expected output MUST be like this:
//========================================
// Successfully created table: users
// Successfully created table: messages
// Successfully created table: comments
//========================================


$con->close(); // ⚠️ Always close the connection
