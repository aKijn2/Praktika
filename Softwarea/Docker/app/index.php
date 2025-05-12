<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Hello, World!</p>
    <p>PHP Version: <?php echo phpversion(); ?></p>
    <p>Server Name: <?php echo $_SERVER['SERVER_NAME']; ?></p>
    <p>Server Address: <?php echo $_SERVER['SERVER_ADDR']; ?></p>
    <p>Server Port: <?php echo $_SERVER['SERVER_PORT']; ?></p>
    <p>Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
</body>
</html>