<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$apiKey = "570d803043d71414b7d23113d492c04b";  // 
$city = "Istanbul";
$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=tr";

$durum = "Bilinmiyor";
$durum_detay = "Veri alınamadı";
$sicaklik = null;
$iptalDurumu = false;

$response = @file_get_contents($url);
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data["weather"][0]["main"])) {
        $durum = $data["weather"][0]["main"];
        $durum_detay = $data["weather"][0]["description"];
        $sicaklik = $data["main"]["temp"];
        $iptalDurumu = in_array(strtolower($durum), ['rain', 'storm', 'snow', 'thunderstorm']);
    }
}

$etkinlikler = $conn->query("SELECT * FROM events ORDER BY date ASC");

// İlgi alanları metni üzerinden case-insensitive karşılaştırma
$ilgiEtkinlikler = $conn->query("SELECT * FROM events 
    WHERE LOWER(type) IN (
        SELECT LOWER(TRIM(t)) 
        FROM (
            SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(interests, ',', numbers.n), ',', -1) AS t
            FROM (
                SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
            ) numbers
            JOIN users u ON u.id = {$_SESSION['userid']}
            WHERE numbers.n <= 1 + LENGTH(interests) - LENGTH(REPLACE(interests, ',', ''))
        ) as parsed
    )
    ORDER BY date ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Etkinlik Yönetimi</title>
    <link rel="stylesheet" href="Style_theme.css">
    
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #f8f9fa, #e0f7fa);
        color: #333;
    }
    .container {
        width: 90%;
        max-width: 1400px;
        margin: auto;
        padding: 30px;
    }
    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #2c3e50;
    }
    h3 {
        font-size: 22px;
        margin-top: 30px;
        margin-bottom: 15px;
        color: #34495e;
    }
    .flex-columns {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
    }
    .flex-left, .flex-right {
        flex: 1;
        min-width: 320px;
    }
    .etkinlik {
        background: #ffffff;
        border-left: 5px solid #00bcd4;
        padding: 12px 16px;
        margin-bottom: 12px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .weather-box, .announcement-box {
        background: #ffffff;
        padding: 15px;
        border-left: 5px solid #ff9800;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }
    .announcement-box p {
        margin: 0;
    }
    .footer-buttons {
        margin-top: 40px;
    }
    .footer-buttons a.btn {
        display: inline-block;
        margin: 10px 15px 0 0;
        padding: 12px 24px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: white;
        border-radius: 5px;
        transition: background 0.3s;
    }
    .btn.green {
        background-color: #4caf50;
    }
    .btn.green:hover {
        background-color: #43a047;
    }
    .btn.logout {
        background-color: #e53935;
    }
    .btn.logout:hover {
        background-color: #d32f2f;
    }
</style>

</head>
<body>
<div class="container">
    <h2>🎊 Etkinlik Yönetim Sistemi</h2>
    <p>Hoş geldin, <?= htmlspecialchars($_SESSION["email"]) ?></p>

    <div class="section">
        <h3>✨ İlginizi Çekebilecek Etkinlikler</h3>
        <?php if ($ilgiEtkinlikler && $ilgiEtkinlikler->num_rows > 0): ?>
            <?php while ($e = $ilgiEtkinlikler->fetch_assoc()): ?>
                <div class="etkinlik">
                    <strong><?= $e['title'] ?></strong> – <?= $e['type'] ?><br>
                    Tarih: <?= $e['date'] ?> – Fiyat: <?= $e['price'] ?> TL – Kalan: <?= $e['capacity'] ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>İlginizi çeken etkinlik bulunamadı.</p>
        <?php endif; ?>
    </div>

    <div class="flex-columns">
        <div class="flex-left">
            <h3>📅 Tüm Etkinlikler</h3>
            <?php $etkinlikler->data_seek(0); while ($e = $etkinlikler->fetch_assoc()): ?>
                <div class="etkinlik">
                    <strong><?= $e['title'] ?></strong> – <?= $e['type'] ?><br>
                    Tarih: <?= $e['date'] ?> – Fiyat: <?= $e['price'] ?> TL – Kalan: <?= $e['capacity'] ?>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="flex-right">
            <div class="weather-box">
                <h3>🌤️ Hava Durumu – <?= $city ?></h3>
                <p>Durum: <?= ucfirst($durum_detay) ?></p>
                <p>Sıcaklık: <?= is_numeric($sicaklik) ? round($sicaklik) . "°C" : "Veri alınamadı" ?></p>
            </div>

            <div class="announcement-box">
                <h3>📢 Duyuru</h3>
                <?php if ($iptalDurumu): ?>
                    <p style="color: red;"><strong>Uyarı:</strong> Hava durumu uygun değil. Bugünkü etkinlikler iptal edilmiştir.</p>
                <?php else: ?>
                    <p>Etkinlikler planlandığı gibi devam etmektedir.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <br>
    <div class="footer-buttons">
        <a href="cart.php" class="btn green">🛒 Bilet Al</a>
        <a href="my_tickets.php" class="btn green">🎫 Biletlerim</a>
        <a href="logout.php" class="btn logout">🚪 Çıkış Yap</a>
    </div>
</div>
</body>
</html>
