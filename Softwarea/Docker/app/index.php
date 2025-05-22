<?php
session_start(); // Iniciamos la sesión

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

// Variables inicializadas para evitar errores
$historiala = [];
$izena = "";
$reserbak = [];
$bezeroa = null;

if (isset($_SESSION['emaila']) && $_SESSION['rol'] === 'bezeroa') {
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener ID y nombre del bezeroa
    $stmt = $pdo->prepare("SELECT id_bezeroa, izena FROM bezeroa WHERE emaila = ?");
    $stmt->execute([$_SESSION['emaila']]);
    $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bezeroa) {
      $izena = $bezeroa['izena'];

      // Cargar historial de viajes
      $stmt = $pdo->prepare("
        SELECT b.jatorria, b.helmuga, b.data AS amaiera_data, b.egoera
        FROM bidaia b
        WHERE b.bezeroa_id_bezeroa = ?
        ORDER BY b.data DESC
      ");
      $stmt->execute([$bezeroa['id_bezeroa']]);
      $historiala = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Cargar reservas activas (no usadas)
      $stmt = $pdo->prepare("
        SELECT id_erreserba, data_esleipena, ordua_esleipena 
        FROM erreserba 
        WHERE bezeroa_id_bezeroa = ? AND (egoera_erreserba IS NULL OR egoera_erreserba != 'erabilita')
      ");
      $stmt->execute([$bezeroa['id_bezeroa']]);
      $reserbak = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="assets/css/default/main.css" />
  <link rel="stylesheet" href="assets/css/default/gehiagoIkusiHistoriala.css" />
  <link rel="stylesheet" href="assets/css/default/gidariProfesionalakPanela.css" />
  <link rel="stylesheet" href="assets/css/bezeroak/review.css" />
  <link rel="stylesheet" href="assets/css/bezeroak/eskatuModalSplit.css" />
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
      <h2>
        <?php if (isset($_SESSION['emaila'])): ?>
          ONGI ETORRI, <?= htmlspecialchars($izena) ?>!
        <?php else: ?>
          HASI SAIOA ETA ESKATU TAXIA
        <?php endif; ?>
      </h2>
      <p>AlaiktoMUGI-rekin zure hurrengo bidaia azkar eta erraz antolatu.</p>
      <ul class="actions">
        <?php if (isset($_SESSION['emaila'])): ?>
          <li><a href="index.php?logout=true" class="button big">ITXI SAIOA</a></li>
        <?php else: ?>
          <li><a href="login.php" class="button big">SAIOA HASI</a></li>
        <?php endif; ?>
      </ul>
    </section>


    <!-- ✅ Modal: Eskatu orain (versión dividida) -->
    <div id="eskatuModalSplit" class="modal">
      <div class="modal-content">
        <span class="close" id="closeEskatu">&times;</span>
        <h2>Eskatu zure taxi orain</h2>
        <form action="php/bezeroa/eskatuHorain.php" method="POST" class="form-split">
          <!-- Columna izquierda -->
          <div class="form-column">
            <label for="jatorria">Jatorria:</label>
            <input type="text" id="jatorria" name="jatorria" required>

            <label for="helmuga">Helmuga:</label>
            <input type="text" id="helmuga" name="helmuga" required>

            <label for="pertsona_kopurua">Pertsona kopurua:</label>
            <input type="number" id="pertsona_kopurua" name="pertsona_kopurua" required min="1">
          </div>

          <!-- Columna derecha -->
          <div class="form-column">
            <label for="erreserba">Erreserba:</label>
            <select id="erreserba" name="erreserba_id">
              <option value="">-- Aukeratu zure erreserba --</option>
              <?php foreach ($reserbak as $res): ?>
                <option value="<?= $res['id_erreserba'] ?>">
                  <?= $res['id_erreserba'] ?> - <?= $res['data_esleipena'] ?> <?= $res['ordua_esleipena'] ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required min="<?= date('Y-m-d'); ?>">

            <label for="ordua">Ordua:</label>
            <input type="time" id="ordua" name="ordua" required>
          </div>

          <button type="submit" class="button" style="margin-top: 1em;">Bidali</button>
        </form>
      </div>
    </div>

    <!-- ✅ Modal: Erreserbatu -->
    <div id="erreserbatuModal" class="modal">
      <div class="modal-content fancy">
        <span class="close" id="closeErreserbatu">&times;</span>
        <div class="modal-body">
          <h2>Erreserbatu zure bidaia</h2>
          <form action="php/bezeroa/erreserbatu.php" method="POST">
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required min="<?= date('Y-m-d'); ?>">

            <label for="ordua">Ordua:</label>
            <input type="time" id="ordua" name="ordua" required>

            <button type="submit" class="button">Erreserbatu</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Banner -->
    <section id="intro" class="main">
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
          <a href="#"><img src="images/bezeroak/1.jpg" alt="" /></a>
          <h3>Garraio pertsonalizatua</h3>
        </header>
        <p>Bezero bakoitza berezia da guretzat!</p>
        <ul class="actions">
        <li><button class="button">GEHIAGO</button></li>
        </ul>
      </article>

      <!-- ✅ Modal para "Gehiago" -->
      <div id="gehiagoModal" class="modal">
          <div class="modal-content fancy">
          <span class="close" id="closeGehiago">&times;</span>
            <div class="modal-body">
          <h2>Garraio pertsonalizatua</h2>
          <p>
            Gure garraio zerbitzuak zure beharretara egokitzen dira. Zerbitzu esklusibo eta malguak eskaintzen ditugu, zure esperientzia ahalik eta erosoena izan dadin.
          </p>
              </div>
        </div>
      </div>

      <!-- ✅ BOTÓN -->
      <article class="item">
        <header>
          <a href="#"><img src="images/bezeroak/2.jpg" alt="" /></a>
          <h3>Gidari profesionalak</h3>
        </header>
        <p>Gure gidari guztiak esperientziadunak eta adeitsuak dira.</p>
        <ul class="actions">
          <li><a href="#" class="button" id="ezagutuBtn">Ezagutu</a></li>
        </ul>
      </article>

      <!-- ✅ MODAL Ezagutu -->
      <div id="ezagutuModal" class="modal">
        <div class="modal-content fancy">
          <span class="close" id="closeEzagutu">&times;</span>
          <div class="modal-body">
            <h2>Gidari Profesionalak</h2>
            <p>
              Gure gidari guztiak hautaketa prozesu zorrotza gainditu dute, eta esperientzia handia dute garraio seguru eta atsegina eskaintzen.
            </p>
            <p>
              Prest daude zure beharretara egokitzeko, puntualtasuna eta profesionaltasuna bermatuz.
            </p>
          </div>
        </div>
      </div>



      <article class="item">
        <header>
          <a href="#"><img src="images/bezeroak/3.jpg" alt="" /></a>
          <h3>Erreserbak online</h3>
        </header>
        <p>Plataforma intuitibo baten bidez zure ibilbidea erreserbatu dezakezu.</p>
        <ul class="actions">
          <li><a href="#" id="erreserbatuBtn" class="button">Erreserbatu</a></li>
        </ul>
      </article>

      <article class="item">
        <header>
          <a href="#"><img src="images/bezeroak/4.jpg" alt="" /></a>
          <h3>24/7 eskuragarri</h3>
        </header>
        <p>Zure mugikortasuna bermatzeko eguneko 24 orduetan lanean gaude!</p>
        <ul class="actions">
          <li><a href="#" class="button">Jarri harremanetan</a></li>
        </ul>
      </article>
    </section>

    <!-- Historiala -->
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
                <p><span class="icon">🚖</span> <?= htmlspecialchars($item['egoera']) ?></p>
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
      <div class="grid grid-pad">
        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Ane Etxebarria</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Oso azkar iritsi zen taxia eta gidaria jatorra zen. Segurtasunez iritsi nintzen helmugara.</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Jon Imaz</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>App-a oso erabilerraza da eta 5 minututan nuen taxia etxe atarian. Gomendagarria!</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Maite Arriola</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Gidariak beti laguntzen dute maletak sartzen, eta autoak garbi daude. Zerbitzu profesionala.</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Iker Mendizabal</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Nire bidaietako konfiantzazko taxi-zerbitzua bihurtu da. Errespetatzen!.</p>
          </div>
        </div>
      </div>

      <div class="grid grid-pad">
        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Leire Olaizola</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Erreserba prozesua azkarra eta erraza izan zen. Aukera ditzakezu auto mota desberdinak.</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Xabier Aranguren</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Ordutegi puntako orduetan ere ez dut itxaron behar izaten. Antolaketa ona dute.</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Nerea Zubizarreta</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Aplikazioan zuzenean ikusten duzu taxiaren kokapena. Lasaitasuna ematen du.</p>
          </div>
        </div>

        <div class="col-1-of-4">
          <div class="rate-box">
            <p class="rater-name">Gaizka Altuna</p>
            <div class="gold-star-group">
              <span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span><span class="gold-star">⭐</span>
            </div>
            <p>Betidanik erabiltzen dut zerbitzu hau aireportura joateko. Ez naiz inoiz berandu iritsi.</p>
          </div>
        </div>

        <span id="dots">...</span>
      </div>

      <button onclick="myFunction()" id="myBtn">Ikusi gehiago</button>
    </footer>


  </div>

  <div class="copyright">
    AlaiktoMUGI © 2025 - Webgunea garatua <a href="#">Achraf Allach Chahboun - Iker Hernández
      Navas</a>
  </div>

  <!-- Scripts -->
  <script src="assets/js/default/jquery.min.js"></script>
  <script src="assets/js/default/skel.min.js"></script>
  <script src="assets/js/default/util.js"></script>
  <script src="assets/js/default/main.js"></script>
  <script src="assets/js/default/gehiagoIkusiHistoriala.js"></script>
  <script src="assets/js/default/gidariProfesionalakPanela.js"></script>
  <script src="assets/js/bezeroak/eskatuHorain.js"></script>
  <script src="assets/js/bezeroak/review.js"></script>
  <script src="assets/js/bezeroak/eskatuHorainKudeaketa.js"></script>
  <script src="assets/js/bezeroak/gehiagoModal"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const gehiagoBtn = document.querySelector('.item .button');
      const gehiagoModal = document.getElementById('gehiagoModal');
      const closeBtn = document.getElementById('closeGehiago');

      gehiagoBtn.addEventListener('click', (e) => {
        e.preventDefault();
        gehiagoModal.style.display = 'block';
      });

      closeBtn.addEventListener('click', () => {
        gehiagoModal.style.display = 'none';
      });

      window.addEventListener('click', (e) => {
        if (e.target == gehiagoModal) {
          gehiagoModal.style.display = 'none';
        }
      });
    });
  </script>


</body>

</html>