<?php
session_start();

if (!isset($_SESSION['emaila']) || $_SESSION['rol'] !== 'gidaria') {
    header("Location: index.php");
    exit();
}

// 🔁 Variables de conexión
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //  1. Si hay POST para actualizar egoera
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_bidaia'], $_POST['egoera'])) {
        $id_bidaia = $_POST['id_bidaia'];
        $egoera_berria = $_POST['egoera'];

        $stmt = $pdo->prepare("UPDATE bidaia SET egoera = ? WHERE id_bidaia = ?");
        $stmt->execute([$egoera_berria, $id_bidaia]);

        // Redirigir para evitar reenvío
        header("Location: gidaria.php");
        exit();
    }

    //  2. Obtener ID del gidaria
    $stmt = $pdo->prepare("SELECT id_gidaria FROM gidaria WHERE emaila = ?");
    $stmt->execute([$_SESSION['emaila']]);
    $gidaria = $stmt->fetch(PDO::FETCH_ASSOC);

    //  3. Viajes pendientes
    $stmt = $pdo->prepare("SELECT * FROM bidaia WHERE egoera = 'pendiente' AND gidaria_id_gidaria IS NULL");
    $stmt->execute();
    $bidaiak = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //  4. Viajes asignados al gidaria
    $stmt = $pdo->prepare("SELECT * FROM bidaia WHERE gidaria_id_gidaria = ? AND egoera != 'amaituta'");
    $stmt->execute([$gidaria['id_gidaria']]);

    $bidaiak_onartuta = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <title>AlaiktoMUGI - Taxi Zerbitzua</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/gidariak/main.css" />
    <link rel="stylesheet" href="assets/css/gidariak/bidaiakIkusi.css" />
</head>

<body>

    <!-- Header -->
    <header id="header" class="alt">
        <div class="inner">
            <h1>AlaiktoMUGI<br>Gidari Panela</h1>
            <p>Kudeatu bezeroen bidaiak, historiala ikusi eta gehiago gure gidari panelarekin.</p>
        </div>
    </header>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Items -->
        <section class="main items">
            <article class="item">
                <header>
                    <h3>BIDAIAK IKUSI</h3>
                </header>
                <p>Ikusi dauden bidaiak.</p>
                <ul class="actions">
                    <li><a class="button"
                            onclick="document.getElementById('bidaiaModal').style.display='flex'">IREKI</a></li>
                </ul>
            </article>

            <article class="item">
                <header>
                    <h3>NERE BIDAIAK</h3>
                </header>
                <p>Ikusi autatutako bidaiak.</p>
                <ul class="actions">
                    <li><a class="button" onclick="document.getElementById('nireBidaiaModal').style.display='flex'">IREKI</a></li>
                </ul>
            </article>
        </section>

        <!-- BIDAIEN HISTORIALA -->
        <section id="intro" class="main">
            <h2>ZUK amaitutaKO BIDAIEN HISTORIALA</h2>
            <p>COMING SOON</p>
            <ul class="actions">
                <li><a class="button big">BORRATU HISTORIALA</a></li>
            </ul>
        </section>

        <!-- CTA -->
        <section id="intro" class="main">
            <h2>¿SAIOA AMAITU NAHI AL DUZU?</h2>
            <p>Dena bukatuta baldin baduzu, sahia amaitzeko prest zaude!</p>
            <ul class="actions">
                <?php if (isset($_SESSION['emaila'])): ?>
                    <li><a href="index.php?logout=true" class="button big">ITXI SAIOA</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="button big">SAIOA HASI</a></li>
                <?php endif; ?>
            </ul>
        </section>
    </div>

    <!-- Bidaiak ikusi model -->
    <div id="bidaiaModal" class="modal">
        <div class="modal-content">
            <span class="modal-close"
                onclick="document.getElementById('bidaiaModal').style.display='none'">&times;</span>
            <h2>Bidaia Aukerak</h2>
            <table class="bidaia-table">
                <thead>
                    <tr>
                        <th>Jatorria</th>
                        <th>Helmuga</th>
                        <th>Data</th>
                        <th>Ordua</th>
                        <th>Pertsonak</th>
                        <th>Onartu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bidaiak as $bidaia): ?>
                        <tr>
                            <td><?= htmlspecialchars($bidaia['jatorria']) ?></td>
                            <td><?= htmlspecialchars($bidaia['helmuga']) ?></td>
                            <td><?= $bidaia['data'] ?></td>
                            <td><?= $bidaia['ordua'] ?></td>
                            <td><?= $bidaia['pertsona_kopurua'] ?></td>
                            <td>
                                <form method="POST" action="onartu_bidaia.php">
                                    <input type="hidden" name="id_bidaia" value="<?= $bidaia['id_bidaia'] ?>">
                                    <input type="checkbox" name="onartu" onchange="this.form.submit()">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Nere bidaiak model -->
    <div id="nireBidaiaModal" class="modal">
        <div class="modal-content">
            <span class="modal-close"
                onclick="document.getElementById('nireBidaiaModal').style.display='none'">&times;</span>
            <h2>Nire Bidaiak</h2>
            <table class="bidaia-table">
                <thead>
                    <tr>
                        <th>Jatorria</th>
                        <th>Helmuga</th>
                        <th>Data</th>
                        <th>Ordua</th>
                        <th>Pertsonak</th>
                        <th>Egoera Aldatu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bidaiak_onartuta as $bidaia): ?>
                        <tr>
                            <td><?= htmlspecialchars($bidaia['jatorria']) ?></td>
                            <td><?= htmlspecialchars($bidaia['helmuga']) ?></td>
                            <td><?= $bidaia['data'] ?></td>
                            <td><?= $bidaia['ordua'] ?></td>
                            <td><?= $bidaia['pertsona_kopurua'] ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id_bidaia" value="<?= $bidaia['id_bidaia'] ?>">
                                    <select name="egoera" onchange="this.form.submit()">
                                        <option value="onartuta" <?= $bidaia['egoera'] === 'onartuta' ? 'selected' : '' ?>>Onartuta</option>
                                        <option value="amaituta" <?= $bidaia['egoera'] === 'amaituta' ? 'selected' : '' ?>>Amaituta</option>
                                        <option value="bidaian" <?= $bidaia['egoera'] === 'bidaian' ? 'selected' : '' ?>>Bidaian</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="copyright">
        AlaiktoMUGI © 2025 - Webgunea garatua <a href="#">Achraf Allach Chahboun - Iker Hernández
            Navas</a>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/skel.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/gidariak/bidaiakIkusi.js"></script>
</body>

</html>