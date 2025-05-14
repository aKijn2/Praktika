<?php
session_start(); // Iniciamos la sesión
?>
<!DOCTYPE html>
<html lang="eu">

<head>
    <title>AlaiktoMUGI - Taxi Zerbitzua</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/gidariak/main.css" />
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
                    <a href="#"><img src="#" alt="" /></a>
                    <h3>BIDAIAK IKUSI</h3>
                </header>
                <p>Ikusi dauden bidaiak.</p>
                <ul class="actions">
                    <li><a href="#" class="button">IREKI</a></li>
                </ul>
            </article>

            <article class="item">
                <header>
                    <a href="#"><img src="#" alt="" /></a>
                    <h3>NERE BIDAIAK</h3>
                </header>
                <p>Ikusi autatutako bidaiak.</p>
                <ul class="actions">
                    <li><a href="#" class="button">IRKEI</a></li>
                </ul>
            </article>
        </section>

        <!-- CTA -->
        <section id="intro" class="main">
            <h2>¿SAIOA AMAITU NAHI AL DUZU?</h2>
            <p>Dena bukatuta baldin baduzu, sahia amaitzeko prest zaude!</p>
            <ul class="actions">
                <?php if (isset($_SESSION['emaila'])): ?>
                    <li><a href="#" class="button big">ITXI SAIOA</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="button big">SAIOA HASI</a></li>
                <?php endif; ?>
            </ul>
        </section>

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
</body>

</html>