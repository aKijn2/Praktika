<?php
$host = 'db';
$dbname = 'alaiktomugi';
$user = 'root';
$password = 'mysql';

// Konexioa sortu
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Habilitar modo de errores
} catch (PDOException $e) {
    die("Errorea konexioan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <title>AlaiktoMUGI - Hasiera</title>
</head>

<body>
    <h1>Ongi etorri AlaiktoMUGI sistemara</h1>

    <h2>Bezeroak</h2>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM bezeroa");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                    <td>{$row['id_bezeroa']}</td>
                    <td>{$row['izena']}</td>
                    <td>{$row['abizena']}</td>
                    <td>{$row['emaila']}</td>
                    <td>{$row['helbidea']}</td>
                    <td>{$row['telefonoa']}</td>
                    <td>{$row['pasahitza']}</td>
                    <td>{$row['nan']}</td>
                  </tr>";
    }
    ?>
    </table>
</body>

</html>