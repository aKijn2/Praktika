<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            padding: 4em;
            border-radius: 8px;
            box-shadow: -20px -16px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding-left: 2em;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-btn {
            width: 24em;
            padding: 13px;
            background-color:rgba(255, 255, 255, 0);
            color: black;
            border: none;
            border-radius: 4px;
            outline: black 1px solid;
            cursor: pointer;
            margin-top: 5em;
        }

        .login-btn:hover {
            background-color:rgb(255, 198, 198);
        }
    </style>
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
</head>