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
        $emaila = $_POST['username'] ?? '';
        $pasahitza = $_POST['password'] ?? '';

        // Intentamos encontrar al usuario en la tabla bezeroa
        $stmt = $pdo->prepare("SELECT * FROM bezeroa WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bezeroa) {
            $_SESSION['emaila'] = $bezeroa['emaila'];
            $_SESSION['rol'] = 'bezeroa';
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;

            enviar_mail($bezeroa['emaila'], $verification_code);
            header("Location: verification.php");
            exit;
        }

        // Si no se encuentra como bezeroa, buscamos como gidaria
        $stmt = $pdo->prepare("SELECT * FROM gidaria WHERE emaila = ? AND pasahitza = ?");
        $stmt->execute([$emaila, $pasahitza]);
        $gidaria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gidaria) {
            $_SESSION['emaila'] = $gidaria['emaila'];
            $_SESSION['rol'] = 'gidaria';
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;

            enviar_mail($gidaria['emaila'], $verification_code);
            header("Location: verification.php");
            exit;
        }

        // Si no es bezeroa ni gidaria
        $error = "Erabiltzailea edo pasahitza ez da zuzena.";
    }

} catch (PDOException $e) {
    $error = "Errorea konexioan: " . $e->getMessage();
}

// ✅ Funtzioa: kodea bidaltzeko
function enviar_mail($to, $kodea)
{
    $subject = "Kodigoaren egiaztapena - AlaiktoMUGI";
    $txt = "Zure kodea: " . $kodea . "\nSartu kodea saioa hasteko.";
    $headers = "From: ikertolosaldealhi@gmail.com\r\n" .
        "CC: 1ag3.ikerhern@tolosaldealh.eus";

    mail($to, $subject, $txt, $headers);
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