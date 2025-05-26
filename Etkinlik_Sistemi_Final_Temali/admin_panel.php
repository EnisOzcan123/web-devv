<?php
session_start();
include "config.php";


if (!isset($_SESSION["userid"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}


if (isset($_GET["onayla"])) {
    $kullanici_id = $_GET["onayla"];
    $guncelle = $conn->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $guncelle->bind_param("i", $kullanici_id);
    $guncelle->execute();
}


$sonuc = $conn->query("SELECT id, email FROM users WHERE approved = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="Style_theme.css">
    <title>Admin Paneli</title>
</head>
<body>
<div class="container">

    <h2>Hoş geldiniz</h2>

   <a href="admin_events.php" class="btn green">Etkinlikleri Yönet</a>


    <h3>Onay Bekleyen Kullanıcılar</h3>

    <?php if ($sonuc->num_rows > 0): ?>
        <ul>
            <?php while ($row = $sonuc->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($row["email"]) ?> - 
                    <a href="admin_panel.php?onayla=<?= $row["id"] ?>">✅ Onayla</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Şu anda onay bekleyen kullanıcı yok.Lütfen kayıt bekleyiniz!</p>
    <?php endif; ?>
    <a href="logout.php" style="margin-bottom:20px;margin-right:15px;">🚪 Çıkış Yap</a>
</div></body>
</html>
