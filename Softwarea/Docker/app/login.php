<?php
session_start();

// Configuración de la base de datos
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";
$error = "";

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $emaila = trim($_POST['username'] ?? '');
        $pasahitza = trim($_POST['password'] ?? '');

        // Buscar como bezeroa
        $stmt = $pdo->prepare("SELECT * FROM bezeroa WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bezeroa) {
            $_SESSION['emaila'] = $bezeroa['emaila'];
            $_SESSION['rol'] = 'bezeroa';
            header("Location: index.php");
            exit();
        }

        // Buscar como gidaria
        $stmt = $pdo->prepare("SELECT * FROM gidaria WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $gidaria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gidaria) {
            $_SESSION['emaila'] = $gidaria['emaila'];
            $_SESSION['rol'] = 'gidaria';
            header("Location: gidaria.php");
            exit();
        }

        // Si no coincide con ningún usuario
        $error = "Erabiltzailea edo pasahitza ez da zuzena.";
    }

} catch (PDOException $e) {
    $error = "Errorea konexioan: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <title>Login - AlaiktoMUGI</title>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="login-container">
        <h2>SAIOA HASI</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Erabiltzailea (Emaila)</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Pasahitza</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <a href="register.php" class="register-link">Erregistratu</a>
            </div>

            <button type="submit" class="login-btn">HASI</button>
        </form>
    </div>
</body>

</html>