<?php
session_start(); // Iniciamos la sesión

// Verificamos que el usuario esté autenticado y tenga un código de verificación
if (!isset($_SESSION['emaila']) || !isset($_SESSION['verification_code'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = $_POST["verification_code"];

    // Verificamos que el código ingresado sea correcto
    if ($codigo_ingresado == $_SESSION['verification_code']) {
        // El código es correcto
        $_SESSION['verified'] = true;
        unset($_SESSION['verification_code']); // Eliminamos el código de sesión

        // Redirigimos según el rol
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'gidaria') {
            header("Location: gidaria.php");
        } else {
            header("Location: index.php"); // Por defecto los bezeroa van a index.php
        }
        exit();
    } else {
        // El código es incorrecto
        $mensaje = "Kodea okerra.";
    }
}
?>



<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <title>Berifikazioa - AlaiktoMUGI</title>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="login-container">
        <h2>Berifikazioa</h2>
        <form action="verification.php" method="POST">
            <div class="form-group">
                <label for="verification_code">Sartu egiaztatzeko kodea:</label>
                <input type="text" id="verification_code" name="verification_code" required>
            </div>

            <button type="submit" class="login-btn">Bidali</button>
        </form>
    </div>

</body>

</html>