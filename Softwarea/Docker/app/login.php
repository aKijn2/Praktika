<?php
session_start(); // Iniciamos la sesión

// Configuración de la base de datos
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";
$error = "";
$verification_code = null; // Para almacenar el código de verificación

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si el formulario se envía por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $emaila = $_POST['username'] ?? '';
        $pasahitza = $_POST['password'] ?? '';

        // Verificación en la tabla 'bezeroa' (clientes)
        $stmt = $pdo->prepare("SELECT * FROM bezeroa WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Usuario encontrado, creamos el código de verificación
            $_SESSION['emaila'] = $user['emaila'];
            $_SESSION['rol'] = 'bezeroa';
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;

            // Enviamos el correo de verificación
            enviar_mail($user['emaila'], $verification_code);

            // Redirigimos a la página de verificación
            header("Location: verification.php");
            exit;
        }

        // Verificación en la tabla 'gidaria' (administradores)
        $stmt = $pdo->prepare("SELECT * FROM gidaria WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Usuario encontrado, creamos el código de verificación
            $_SESSION['emaila'] = $user['emaila'];
            $_SESSION['rol'] = 'gidaria';
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;

            // Enviamos el correo de verificación
            enviar_mail($user['emaila'], $verification_code);

            // Redirigimos a la página de verificación
            header("Location: verification.php");
            exit;
        }

        $error = "Erabiltzailea edo pasahitza ez da zuzena."; // Usuario o contraseña incorrectos
    }
} catch (PDOException $e) {
    $error = "Errorea konexioan: " . $e->getMessage(); // Error en la conexión
}

// Función para enviar el correo
function enviar_mail($to, $codigo_verificacion) {
    $subject = "Kodigoaren egiaztapena - AlaiktoMUGI";
    $txt = "Zure kodea: " . $codigo_verificacion . "\nSartu kodea saioa hasteko.";
    $headers = "From: ikertolosaldealhi@gmail.com" . "\r\n" .
    "CC: 1ag3.ikerhern@tolosaldealh.eus";

    mail($to, $subject, $txt, $headers);  // Utilizamos la función mail de PHP
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
