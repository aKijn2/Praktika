<?php
session_start();

$host = "db"; // Docker-compatible
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";

try {
    // Conexión
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si se enviaron los datos por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos del formulario
        $emaila = $_POST['username'] ?? '';
        $pasahitza = $_POST['password'] ?? '';

        // Verificar en la tabla bezeroa
        $stmt = $pdo->prepare("SELECT * FROM bezeroa WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['emaila'] = $user['emaila'];
            $_SESSION['rol'] = 'bezeroa';
            header("Location: index.html");
            exit;
        }

        // Verificar en la tabla gidaria
        $stmt = $pdo->prepare("SELECT * FROM gidaria WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['emaila'] = $user['emaila'];
            $_SESSION['rol'] = 'gidaria';
            header("Location: index.php");
            exit;
        }

        // Si no coincide
        echo "Erabiltzailea edo pasahitza ez da zuzena.";
    } else {
        // Si accedieron directamente sin enviar datos
        echo "";
    }

} catch (PDOException $e) {
    die("Errorea konexioan: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css" />
    <title>Login - AlaiktoMUGI</title>
</head>

<body>
    <div class="login-container">
        <h2>SAIOA HASI</h2>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Erabiltzailea (emaila)</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Pasahitza</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password">
                    <a href="register.php" class="register-href">Berria nahiz, sortu kontua.</a>
                </label>
            </div>

            <button type="submit" class="login-btn">HASI</button>
        </form>
    </div>
</body>

</html>