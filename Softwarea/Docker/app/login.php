<?php
// Comprobamos si ya está logueado el usuario. Si es así, lo redirigimos a la página de inicio.
session_start();

// Verifica si ya hay una sesión activa, redirige al usuario si está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: frogak2_saioaHasita.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos de conexión a la base de datos
    $host = 'localhost';
    $dbname = 'alaiktomugi';
    $user = 'root';
    $password = 'mysql';

    // Crear la conexión
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Habilitar modo de errores
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }

    // Obtener los datos del formulario
    $nan = $_POST['username'];
    $password = $_POST['password'];

    // Consulta SQL para verificar las credenciales
    $stmt = $pdo->prepare("SELECT * FROM bezeroa WHERE nan = :nan AND pasahitza = :password");
    $stmt->bindParam(':nan', $nan);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Si se encuentran las credenciales correctas
    if ($stmt->rowCount() > 0) {
        // Obtener los datos del usuario
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Iniciar la sesión
        $_SESSION['user_id'] = $user['id_bezeroa'];
        $_SESSION['user_nan'] = $user['nan'];  // Guardamos el NIF (Nan) del usuario para sesión

        // Redirigir al usuario a la página frogak2_saioaHasita.php
        header("Location: frogak2_saioaHasita.php");
        exit();
    } else {
        // Si las credenciales son incorrectas, mostrar mensaje de error
        $error = "Nan edo pasahitza okerra!";
    }
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
        
        <?php if (isset($error)) { echo '<p style="color:red;">' . $error . '</p>'; } ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Erabiltzailea (NAN)</label>
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
