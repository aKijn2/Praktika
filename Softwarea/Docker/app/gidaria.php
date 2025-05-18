<?php
session_start();
date_default_timezone_set('Europe/Madrid');

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

    // ✅ Manejo del formulario de cambio de estado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_bidaia'], $_POST['egoera'])) {
        $id_bidaia = $_POST['id_bidaia'];
        $egoera_berria = $_POST['egoera'];

        // Actualizar estado en bidaia
        $stmt = $pdo->prepare("UPDATE bidaia SET egoera = ? WHERE id_bidaia = ?");
        $stmt->execute([$egoera_berria, $id_bidaia]);

        // Si se cambió a 'amaituta', guardar en historikoa
        if ($egoera_berria === 'amaituta') {
            // Obtener datos del viaje
            $stmt = $pdo->prepare("SELECT jatorria, helmuga FROM bidaia WHERE id_bidaia = ?");
            $stmt->execute([$id_bidaia]);
            $bidaia = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($bidaia) {
                $data = date('Y-m-d');
                $ordua = date('H:i:s');

                $stmt = $pdo->prepare("INSERT INTO historikoa (amaiera_data, amaiera_ordua, jatorria, helmuga, bidaia_id_bidaia)
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$data, $ordua, $bidaia['jatorria'], $bidaia['helmuga'], $id_bidaia]);
            }
        }

        // Redirigir para evitar reenvío al refrescar
        header("Location: gidaria.php");
        exit();
    }

    // ✅ Obtener ID del gidaria desde la sesión
    $stmt = $pdo->prepare("SELECT id_gidaria FROM gidaria WHERE emaila = ?");
    $stmt->execute([$_SESSION['emaila']]);
    $gidaria = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Obtener viajes pendientes (sin asignar)
    $stmt = $pdo->prepare("SELECT * FROM bidaia WHERE egoera = 'pendiente' AND gidaria_id_gidaria IS NULL");
    $stmt->execute();
    $bidaiak = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Obtener viajes asignados a este gidaria que NO estén amaituta
    $stmt = $pdo->prepare("SELECT * FROM bidaia WHERE gidaria_id_gidaria = ? AND egoera != 'amaituta'");
    $stmt->execute([$gidaria['id_gidaria']]);
    $bidaiak_onartuta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Obtener historiala de viajes amaituta para este gidaria
    $stmt = $pdo->prepare("
    SELECT h.jatorria, h.helmuga, h.amaiera_data 
    FROM historikoa h
    JOIN bidaia b ON h.bidaia_id_bidaia = b.id_bidaia
    WHERE b.gidaria_id_gidaria = ?
    ORDER BY h.amaiera_data DESC
");
    $stmt->execute([$gidaria['id_gidaria']]);
    $historiala = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="assets/css/gehiagoIkusiHistoriala.css" />

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
            <h2>ZUK amaitutako BIDAIEN HISTORIALA</h2>

            <?php if (count($historiala) === 0): ?>
                <p>Ez dago amaitutako bidaiarik.</p>
            <?php else: ?>
                <div class="historiala-grid" id="historiala-container">
                    <?php foreach ($historiala as $index => $item): ?>
                        <div class="historiala-card" <?= $index >= 2 ? 'style="display:none;"' : '' ?>>
                            <h4><span class="icon">📍</span> <?= htmlspecialchars($item['jatorria']) ?> → <?= htmlspecialchars($item['helmuga']) ?></h4>
                            <p><span class="icon">📅</span> <?= htmlspecialchars($item['amaiera_data']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($historiala) > 2): ?>
                    <div style="text-align: center; margin-top: 1em;">
                        <button class="button big" id="ver-mas-btn">GEHIAGO IKUSI</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


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
                                        <option value="bidaian" <?= $bidaia['egoera'] === 'bidaian' ? 'selected' : '' ?>>Bidaian</option>
                                        <option value="amaituta" <?= $bidaia['egoera'] === 'amaituta' ? 'selected' : '' ?>>Amaituta</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Footer -->
    <footer id="footer">
        <ul class="icons">
            <li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
        </ul>
    </footer>

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
    <script src="assets/js/gehiagoIkusiHistoriala.js"></script>
</body>

</html>