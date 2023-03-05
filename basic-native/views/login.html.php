<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php
    // Check if registration is successful
    if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; ?>
        </div>
    <?php endif; ?>

    <?php
    // Check if errors is empty
    // You can use empty() but it's better to use count() as the $_SESSION['errors'] is an array that is countable
    if (count($_SESSION['errors']) > 0): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php
        // The endif statement of $_SESSION['errors']
    endif; ?>
    <form action="login.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <fieldset>
            <legend>Login</legend>
            <div class="form-inputs">
                <div>
                    <label for="email">Email: <input type="email" required name="email"
                                                     value="<?php echo $_SESSION['old']['email'] ?? ''; ?>"></label>
                </div>
                <div>
                    <label for="password">Password: <input type="password" required name="password"></label>
                </div>
                <button type="submit">Login</button>
            </div>
        </fieldset>
    </form>
</div>
</body>
</html>