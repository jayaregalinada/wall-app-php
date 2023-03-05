<?php

declare(strict_types=1);
/**
 * This file is for creating tables to the database
 */

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('Only for CLI');
// 👆 That will check if this file run via CLI otherwise it will NOT execute anything

$con = require('connection.php');
// 👆 Let's get the instance of mysqli

$queries = [];

$usersTable = constant('TABLE_USERS');

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
// The <<<SQL is called HEREDOC, allowing multiline string
// or file literal for sending input streams
// allowing us to write NON-PHP string WITHOUT the problem of quotations

// 🎯 Practice on adding IF NOT EXISTS when creating tables
// 🎯 Adding ON UPDATE CURRENT_TIMESTAMP will make sure that every update, it will generate a new CURRENT_TIMESTAMP

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

/**
 * 💡 Improvements that you can implement:
 * - Add UNIQUE constraint to email in users table
 * - Add Soft delete implementation
 */
