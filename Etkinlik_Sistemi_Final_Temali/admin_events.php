
<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

if (isset($_GET["sil"])) {
    $sil_id = intval($_GET["sil"]);
    $sil = $conn->prepare("DELETE FROM events WHERE id = ?");
    $sil->bind_param("i", $sil_id);
    $sil->execute();
}

$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $type = $_POST["type"];
    $date = $_POST["date"];
    $capacity = $_POST["capacity"];
    $price = $_POST["price"];

    $ekle = $conn->prepare("INSERT INTO events (title, type, date, capacity, price) VALUES (?, ?, ?, ?, ?)");
    $ekle->bind_param("sssii", $title, $type, $date, $capacity, $price);
    if ($ekle->execute()) {
        $mesaj = "Etkinlik başarıyla eklendi.";
    } else {
        $mesaj = "Etkinlik eklenemedi.";
    }
}

$etkinlikler = $conn->query("SELECT * FROM events ORDER BY date ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlik Yönetimi</title>
    <link rel="stylesheet" href="Style_theme.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f6fb;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 1000px;
            background: white;
            margin: 40px auto;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        h2, h3 {
            color: #4b0082;
            margin-bottom: 20px;
            text-align: center;
        }
        form input[type="text"],
        form input[type="date"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        form input[type="submit"] {
            background-color: #3f51b5;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }
        form input[type="submit"]:hover {
            background-color: #32408f;
        }
        .etkinlik {
            background: #f8f9ff;
            padding: 12px 16px;
            margin-bottom: 12px;
            border-left: 4px solid #3f51b5;
            border-radius: 6px;
        }
        .etkinlik a {
            color: crimson;
            text-decoration: none;
            font-weight: bold;
        }
        .etkinlik a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            text-align: center;
            padding: 10px 20px;
            margin-right: 10px;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }
        .green {
            background-color: #4caf50;
        }
        .logout {
            background-color: #e53935;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🛠️ Etkinlik Yönetimi</h2>

    <h3>➕ Yeni Etkinlik Ekle</h3>
    <form method="POST">
        <label>Başlık:</label>
        <input type="text" name="title" required>

        <label>Tür:</label>
        <select name="type">
            <option>Açık Hava Sineması</option>
            <option>Spor Müsabakaları</option>
            <option>Konser</option>
            <option>Tiyatro</option>
        </select>

        <label>Tarih:</label>
        <input type="date" name="date" required>

        <label>Kontenjan:</label>
        <input type="number" name="capacity" required>

        <label>Fiyat:</label>
        <input type="number" step="0.01" name="price" required>

        <input type="submit" value="Etkinlik Ekle">
    </form>

    <p style="color:green; text-align:center;"><?= htmlspecialchars($mesaj) ?></p>

    <h3>📋 Mevcut Etkinlikler</h3>
    <?php while ($e = $etkinlikler->fetch_assoc()): ?>
        <div class="etkinlik">
            <strong><?= $e['title'] ?></strong> – <?= $e['type'] ?><br>
            Tarih: <?= $e['date'] ?> – Fiyat: <?= $e['price'] ?> TL – Kalan: <?= $e['capacity'] ?>
            <br>
            <a href="?sil=<?= $e['id'] ?>" onclick="return confirm('Bu etkinliği silmek istediğinize emin misiniz?')">❌ Sil</a>
        </div>
    <?php endwhile; ?>

    <br>
    <a href="admin_panel.php" class="btn green">⬅️ Admin Paneline Dön</a>
    <a href="logout.php" class="btn logout">🚪 Çıkış Yap</a>
</div>
</body>
</html>
