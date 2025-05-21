<?php
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Datu basearekin konexioa huts egin du: " . $e->getMessage());
}

$username = $surname = $email = $phone = $nan = $address = '';
$registration_success = false;  // Variable para verificar si el registro fue exitoso

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $nan = trim($_POST['nan']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones del servidor (por seguridad)
    if (
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^\d{9}$/', $phone) &&
        (empty($nan) || preg_match('/^\d{8}[A-Za-z]$/', $nan)) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password) &&
        $password === $confirm_password
    ) {
        $sql = "INSERT INTO bezeroa (izena, abizena, emaila, helbidea, telefonoa, pasahitza, nan) 
                VALUES (:izena, :abizena, :emaila, :helbidea, :telefonoa, :pasahitza, :nan)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':izena' => $username,
            ':abizena' => $surname,
            ':emaila' => $email,
            ':helbidea' => $address,
            ':telefonoa' => $phone,
            ':pasahitza' => $password,  // Almacenar la contraseña sin encriptar
            ':nan' => $nan
        ]);

        $registration_success = true; // Si el registro fue exitoso, actualiza esta variable
    }
}
?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <title>Register - AlaiktoMUGI</title>
    <link rel="stylesheet" href="assets/css/default/Register.css" />
    <style>
        /* Estilos para el popup */
        .popup {
            display: none;
            /* Ocultar por defecto */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            z-index: 1000;
        }

        .popup p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="Register-container">
        <h2>KONTUA SORTU</h2>

        <!-- Mostrar mensaje si el registro fue exitoso -->
        <?php if ($registration_success): ?>
            <div class="popup" id="popup">
                <p>Erabiltzailea arrakastaz erregistratu da!</p>
            </div>

            <script>
                // Mostrar el popup
                document.getElementById('popup').style.display = 'block';

                // Redirigir después de 3.5 segundos
                setTimeout(function() {
                    window.location.href = "login.php"; // Redirigir a la página de login
                }, 3500);
            </script>
        <?php endif; ?>

        <form action="Register.php" method="POST" novalidate>
            <div style="display: flex; gap: 2em; justify-content: center;">
                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="username">Izena</label>
                        <input type="text" id="username" name="username" required
                            value="<?= htmlspecialchars($username) ?>">
                    </div>

                    <div class="form-group">
                        <label for="surname">Abizena</label>
                        <input type="text" id="surname" name="surname" required
                            value="<?= htmlspecialchars($surname) ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Emaila</label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
                    </div>
                </div>

                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="phone">Telefonoa</label>
                        <input type="text" id="phone" name="phone" required maxlength="9"
                            value="<?= htmlspecialchars($phone) ?>">
                    </div>

                    <div class="form-group">
                        <label for="nan">NAN</label>
                        <input type="text" id="nan" name="nan" placeholder="Aukerakoa"
                            value="<?= htmlspecialchars($nan) ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Helbidea</label>
                        <input type="text" id="address" name="address" required
                            value="<?= htmlspecialchars($address) ?>">
                    </div>
                </div>
            </div>

            <div style="width: 100%; margin-top: 1em;">
                <div class="form-group" style="width: 300px; margin: 0 auto;">
                    <label for="password">Pasahitza</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group" style="width: 300px; margin: 0 auto;">
                    <label for="confirm_password">Errepikatu pasahitza</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <div style="text-align: center; margin-top: 2em;">
                <button type="submit" class="Register-btn">SORTU</button>
            </div>
        </form>
    </div>

    <script src="assets/js/default/pasahitza_balidazioa.js"></script>

</body>

</html>