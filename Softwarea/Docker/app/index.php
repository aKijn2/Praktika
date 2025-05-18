<?php
session_start(); // Iniciamos la sesión

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

// Conexión y carga de historial si el usuario es bezeroa
$historiala = [];

if (isset($_SESSION['emaila']) && $_SESSION['rol'] === 'bezeroa') {
  $host = "db";
  $db = "alaiktomugi";
  $user = "root";
  $pass = "mysql";

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener ID bezeroa
    $stmt = $pdo->prepare("SELECT id_bezeroa FROM bezeroa WHERE emaila = ?");
    $stmt->execute([$_SESSION['emaila']]);
    $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bezeroa) {
      $stmt = $pdo->prepare("
SELECT h.jatorria, h.helmuga, h.amaiera_data
FROM historikoa h
JOIN bidaia b ON h.bidaia_id_bidaia = b.id_bidaia
WHERE b.bezeroa_id_bezeroa = ?
ORDER BY h.amaiera_data DESC
  ");
      $stmt->execute([$bezeroa['id_bezeroa']]);
      $historiala = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  } catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
  }
}

?>
<!DOCTYPE html>
<html lang="eu">

<head>
  <title>AlaiktoMUGI - Taxi Zerbitzua</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="assets/css/main.css" />
  <link rel="stylesheet" href="assets/css/erreserbatu.css" />
  <link rel="stylesheet" href="assets/css/eskatuHorain.css" />
  <link rel="stylesheet" href="assets/css/gehiagoIkusiHistoriala.css" />
</head>

<body>

  <!-- Header -->
  <header id="header" class="alt">

    <div class="inner">
      <h1>AlaiktoMUGI</h1>
      <p>Mugikortasuna zure eskura, bidaiak erraz eta seguru egiteko. Taxi zerbitzu azkar eta fidagarria, Euskal Herrian.</p>
    </div>
  </header>

  <!-- Wrapper -->
  <div id="wrapper">

    <!-- CTA -->
    <section id="intro" class="main">
      <h2>HASI SAIOA ETA ESKATU TAXIA</h2>
      <p>AlaiktoMUGI-rekin zure hurrengo bidaia azkar eta erraz antolatu.</p>
      <ul class="actions">
        <?php if (isset($_SESSION['emaila'])): ?>
          <li><a href="index.php?logout=true" class="button big">ITXI SAIOA</a></li>
        <?php else: ?>
          <li><a href="login.php" class="button big">SAIOA HASI</a></li>
        <?php endif; ?>
      </ul>
    </section>

    <!-- ✅ Modal: Eskatu orain -->
    <div id="eskatuModal" class="modal">
      <div class="modal-content">
        <span class="close" id="closeEskatu">&times;</span>
        <h2>Eskatu zure taxi orain</h2>
        <form action="eskatuHorain.php" method="POST">
          <label for="jatorria">Jatorria:</label>
          <input type="text" id="jatorria" name="jatorria" required>

          <label for="helmuga">Helmuga:</label>
          <input type="text" id="helmuga" name="helmuga" required>

          <label for="pertsona_kopurua">Pertsona kopurua:</label>
          <input type="number" id="pertsona_kopurua" name="pertsona_kopurua" required min="1">

          <label for="data">Data:</label>
          <input type="date" id="data" name="data" required min="<?= date('Y-m-d'); ?>">

          <label for="ordua">Ordua:</label>
          <input type="time" id="ordua" name="ordua" required>

          <button type="submit" class="button">Bidali</button>
        </form>
      </div>
    </div>

    <!-- ✅ Modal: Erreserbatu -->
    <div id="erreserbatuModal" class="modal">
      <div class="modal-content">
        <span class="close" id="closeErreserbatu">&times;</span>
        <h2>Erreserbatu zure bidaia</h2>
        <form action="erreserbatu.php" method="POST">
          <label for="data">Data:</label>
          <input type="date" id="data" name="data" required min="<?= date('Y-m-d'); ?>">

          <label for="ordua">Ordua:</label>
          <input type="time" id="ordua" name="ordua" required>

          <button type="submit" class="button">Erreserbatu</button>
        </form>
      </div>
    </div>

    <!-- Banner -->
    <section id="intro" class="main">
      <span class="icon fa-car major"></span>
      <h2>Gure taxiak zure zain daude</h2>
      <p>Edozein lekutara garaiz eta segurtasunez. <br /> Eska ezazu zure bidaia klik bakar batez.</p>
      <ul class="actions">
        <li><a href="#" id="eskatuOrainBtn" class="button big">Eskatu orain</a></li>
      </ul>
    </section>

    <!-- Items -->
    <section class="main items">
      <article class="item">
        <header>
          <a href="#"><img src="images/1.jpg" alt="" /></a>
          <h3>Garraio pertsonalizatua</h3>
        </header>
        <p>Bezero bakoitza berezia da guretzat!</p>
        <ul class="actions">
          <li><a href="#" class="button">Gehiago</a></li>
        </ul>
      </article>

      <article class="item">
        <header>
          <a href="#"><img src="images/2.jpg" alt="" /></a>
          <h3>Gidari profesionalak</h3>
        </header>
        <p>Gure gidari guztiak esperientziadunak eta adeitsuak dira.</p>
        <ul class="actions">
          <li><a href="#" class="button">Ezagutu</a></li>
        </ul>
      </article>

      <article class="item">
        <header>
          <a href="#"><img src="images/3.jpg" alt="" /></a>
          <h3>Erreserbak online</h3>
        </header>
        <p>Plataforma intuitibo baten bidez zure ibilbidea erreserbatu dezakezu.</p>
        <ul class="actions">
          <li><a href="#" id="erreserbatuBtn" class="button">Erreserbatu</a></li>
        </ul>
      </article>

      <article class="item">
        <header>
          <a href="#"><img src="images/4.jpg" alt="" /></a>
          <h3>24/7 eskuragarri</h3>
        </header>
        <p>Zure mugikortasuna bermatzeko eguneko 24 orduetan lanean gaude!</p>
        <ul class="actions">
          <li><a href="#" class="button">Jarri harremanetan</a></li>
        </ul>
      </article>
    </section>

    <?php if (isset($_SESSION['emaila']) && $_SESSION['rol'] === 'bezeroa'): ?>
      <section id="intro" class="main">
        <h2>ZUK EGINDAKO BIDAIEN HISTORIALA</h2>

        <?php if (count($historiala) === 0): ?>
          <p>Ez duzu oraindik amaitutako bidaiarik.</p>
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
    <?php endif; ?>

    <!-- Footer -->
    <footer id="footer">
      <ul class="icons">
        <li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
      </ul>
    </footer>

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
  <script src="assets/js/eskatuHorain.js"></script>
  <script src="assets/js/gehiagoIkusiHistoriala.js"></script>
</body>

</html>