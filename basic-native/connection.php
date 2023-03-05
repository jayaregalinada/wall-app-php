<?php

declare(strict_types=1); // Not required but helpful in debugging to give you more concise info about the error

include('config.php');

// Create an instance of the connection
$connection = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_DATABASE
);

// Attempt to connect to the database and check if there are any errors
if ($connection->connect_errno) {
    \sprintf(
        'Failed to connect to MySQL Server: (%s) %s',
        $connection->connect_errno,
        $connection->connect_error
    );
    exit();
}

// We return the $connection instance, so we can use it for later
return $connection;
