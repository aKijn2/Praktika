<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css" />
    <title>Login</title>
</head>

<body>
    <div class="login-container">
        <h2>SAIOA HASI</h2>
        <form action="authenticate.php" method="POST">
            <div class="form-group">
                <label for="username">Erabiltzailea</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Pasahitza</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">HASI</button>
        </form>
    </div>
</body>

</html>