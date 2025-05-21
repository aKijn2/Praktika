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

    // ✅ Obtener historiala de viajes amaituta para este gidaria con todos los campos requeridos
    $stmt = $pdo->prepare("
        SELECT h.bidaia_id_bidaia AS id_bidaia, h.amaiera_data, h.amaiera_ordua, h.jatorria, h.helmuga
        FROM historikoa h
        JOIN bidaia b ON h.bidaia_id_bidaia = b.id_bidaia
        WHERE b.gidaria_id_gidaria = ?
        ORDER BY h.amaiera_data DESC, h.amaiera_ordua DESC
    ");
    $stmt->execute([$gidaria['id_gidaria']]);
    $historiala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}

$izena = "";
// Obtener ID y nombre del gidaria
$stmt = $pdo->prepare("SELECT id_gidaria, izena FROM gidaria WHERE emaila = ?");
$stmt->execute([$_SESSION['emaila']]);
$gidaria = $stmt->fetch(PDO::FETCH_ASSOC);
if ($gidaria) {
    $izena = $gidaria['izena'];
} else {
    // Manejo de error si no se encuentra el gidaria
    echo "Errorea: Gidaria ez da aurkitu.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="eu">

<head>
    <title>AlaiktoMUGI - Taxi Zerbitzua</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/default/main.css" />
    <link rel="stylesheet" href="assets/css/gidariak/bidaiakIkusi.css" />
    <link rel="stylesheet" href="assets/css/gehiagoIkusiHistoriala.css" />

</head>

<body>

    <!-- Header -->
    <header id="header" class="alt">
        <div class="inner">
            <h1>
                <span style="display:block;">AlaiktoMUGI <br> Gidari Panela</span>
            </h1>
            <p>Kudeatu bezeroen bidaiak, historiala ikusi eta gehiago gure gidari panelarekin.</p>
        </div>
    </header>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- CTA -->
        <section id="intro" class="main">
            <?php if (isset($_SESSION['emaila'])): ?>
                <h2>ONGI ETORRI, <?= htmlspecialchars($izena) ?>!</h2>
            <?php else: ?>
                HASI SAIOA ETA ESKATU TAXIA
            <?php endif; ?>
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
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center;">ID</th>
                                <th style="text-align: center;">Amaiera Data</th>
                                <th style="text-align: center;">Amaiera Ordua</th>
                                <th style="text-align: center;">Jatorria</th>
                                <th style="text-align: center;">Helmuga</th>
                                <th style="text-align: center;">Egoera</th>
                            </tr>
                        </thead>
                        <tbody id="historiala-tbody">
                            <?php foreach ($historiala as $index => $item): ?>
                                <tr class="historiala-row" <?= $index >= 2 ? 'style="display:none;"' : '' ?>>
                                    <td><?= htmlspecialchars($item['id_bidaia']) ?></td>
                                    <td><?= htmlspecialchars($item['amaiera_data']) ?></td>
                                    <td><?= htmlspecialchars($item['amaiera_ordua']) ?></td>
                                    <td><?= htmlspecialchars($item['jatorria']) ?></td>
                                    <td><?= htmlspecialchars($item['helmuga']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($historiala) > 2): ?>
                    <div style="text-align: center; margin-top: 1em;">
                        <button class="button big" id="ver-mas-btn">GEHIAGO IKUSI</button>
                        <button class="button big" id="ver-menos-btn" style="display:none;">GUTXIAGO IKUSI</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </section>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var btnMas = document.getElementById('ver-mas-btn');
                var btnMenos = document.getElementById('ver-menos-btn');
                var rows = document.querySelectorAll('.historiala-row');
                if (btnMas && btnMenos) {
                    btnMas.addEventListener('click', function() {
                        rows.forEach(function(row) {
                            row.style.display = '';
                        });
                        btnMas.style.display = 'none';
                        btnMenos.style.display = '';
                    });
                    btnMenos.addEventListener('click', function() {
                        rows.forEach(function(row, idx) {
                            row.style.display = idx < 2 ? '' : 'none';
                        });
                        btnMas.style.display = '';
                        btnMenos.style.display = 'none';
                    });
                }
            });
        </script>

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
                                <form method="POST" action="php/gidaria/onartu_bidaia.php">
                                    <input type="hidden" name="id_bidaia" value="<?= $bidaia['id_bidaia'] ?>">
                                    <input type="submit" name="onartu" value="Onartu" class="button small">
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

    <div class="copyright">
        AlaiktoMUGI © 2025 - Webgunea garatua <a href="#">Achraf Allach Chahboun - Iker Hernández
            Navas</a>
    </div>

    <!-- Scripts -->
    <script src="assets/js/default/jquery.min.js"></script>
    <script src="assets/js/default/skel.min.js"></script>
    <script src="ets/js/gidariak/bidaiakIkusi.js"></script>
    <script src="assets/js/default/gehiagoIkusiHistoriala.js"></script>
</body>assets/js/default/util.js"></script>
    <script src="assets/js/default/main.js"></script>
    <script src="ass

</html>