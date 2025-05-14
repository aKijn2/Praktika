<?php
session_start(); // Iniciamos la sesión
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
</head>

<body>

  <!-- Header -->
  <header id="header" class="alt">
    <div class="inner">
      <h1>AlaiktoMUGI</h1>
      <p>Mugikortasuna zure esku. Taxi zerbitzu azkar eta fidagarria Euskal Herrian.</p>
    </div>
  </header>

  <!-- Wrapper -->
  <div id="wrapper">

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
        <p>Bezero bakoitza berezia da guretzat. Zure beharretara egokitzen gara.</p>
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
        <p>Zure mugikortasuna bermatzeko eguneko 24 orduetan lanean.</p>
        <ul class="actions">
          <li><a href="#" class="button">Jarri harremanetan</a></li>
        </ul>
      </article>
    </section>

    <!-- CTA -->
    <section id="intro" class="main">
      <h2>HASI SAIOA ETA ESKATU TAXIA!</h2>
      <p>AlaiktoMUGI-rekin zure hurrengo bidaia azkar eta erraz antolatu.</p>
      <ul class="actions">
        <?php if (isset($_SESSION['emaila'])): ?>
          <li><a href="#" class="button big">SAIOA HASITA</a></li>
        <?php else: ?>
          <li><a href="login.php" class="button big">SAIOA HASI</a></li>
        <?php endif; ?>
      </ul>
    </section>

    <!-- Footer -->
    <footer id="footer">
      <ul class="icons">
        <li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
      </ul>
    </footer>

  </div>

  <div class="copyright">
    AlaiktoMUGI © 2025 - Webgunea garatua <a href="https://templated.co/">Achraf Allach Chahboun - Iker Hernández
      Navas</a>
  </div>

  <!-- Scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/skel.min.js"></script>
  <script src="assets/js/util.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/eskatuHorain.js"></script>
</body>

</html>