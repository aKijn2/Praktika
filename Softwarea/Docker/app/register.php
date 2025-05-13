<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <title>Register - AlaiktoMUGI</title>
    <link rel="stylesheet" href="assets/css/Register.css" />
</head>

<body>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="Register-container">

        <h2>KONTUA SORTU</h2>

        <form action="Register.php" method="POST">
            <div style="display: flex; gap: 2em; justify-content: center;">

                <!-- Columna izquierda -->
                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="username">Izena</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="surname">Abizena</label>
                        <input type="text" id="surname" name="surname" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Emaila</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="password">Telefonoa</label>
                        <input type="text" id="phone" name="phone" required>

                    </div>

                    <div class="form-group">
                        <label for="nan">Nan</label>
                        <input type="text" id="nan" name="nan" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Helbidea</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                </div>
            </div>

            <!-- Campo Pasahitza centrado -->
            <div style="width: 100%; margin-top: 1em;">
                <div class="form-group" style="width: 300px; margin: 0 auto;">
                    <label for="phone">Pasahitza</label>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>

            <!-- Botón centrado -->
            <div style="text-align: center; margin-top: 2em;">
                <button type="submit" class="Register-btn">SORTU</button>
            </div>

        </form>

    </div>
    <link rel="stylesheet" href="assets/css/Register.css" />
</body>

</html>