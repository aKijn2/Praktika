<?php
session_start();

if (!isset($_SESSION['emaila']) || $_SESSION['rol'] !== 'gidaria') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gidaria ID
    $stmt = $pdo->prepare("SELECT id_gidaria FROM gidaria WHERE emaila = ?");
    $stmt->execute([$_SESSION['emaila']]);
    $gidaria = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_gidaria = $gidaria['id_gidaria'];

    // Form datuak
    $id_bidaia = $_POST['id_bidaia'] ?? null;

    if ($id_bidaia) {
        $stmt = $pdo->prepare("UPDATE bidaia SET gidaria_id_gidaria = ?, egoera = 'onartuta' WHERE id_bidaia = ?");
        $stmt->execute([$id_gidaria, $id_bidaia]);
    }

    header("Location: ../../gidaria.php");
    exit();
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}
