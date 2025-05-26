<?php
session_start();
include "config.php";

// Giriş kontrolü
if (!isset($_SESSION["userid"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $type = $_POST["type"];
    $date = $_POST["date"];
    $capacity = $_POST["capacity"];
    $price = $_POST["price"];

    $ekle = $conn->prepare("INSERT INTO events (title, type, date, capacity, price) VALUES (?, ?, ?, ?, ?)");
    $ekle->bind_param("sssii", $title, $type, $date, $capacity, $price);

    if ($ekle->execute()) {
        $mesaj = "Etkinlik başarıyla eklendi 😎";
    } else {
        $mesaj = "Hata: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="Style_theme.css">
    <title>Etkinlik Ekle</title>
</head>
<body>
<div class="container">

    <h2> Yeni Etkinlik Ekle</h2>

    
    <form method="POST">
        Başlık: <input type="text" name="title" required><br><br>
        Tür: 
            <select name="type">
                <option>Açık Hava Sineması</option>
                <option>Spor Müsabakaları</option>
                <option>Konser</option>
                <option>Tiyatro</option>
            </select><br><br>
        Tarih: <input type="date" name="date" required><br><br>
        Kontenjan: <input type="number" name="capacity" required><br><br>
        Fiyat: <input type="number" step="0.01" name="price" required><br><br>
        <input type="submit" value="Ekle">
         <a href="admin_panel.php" style="display:inline-block; margin-bottom:40px; background:#007bff; margin-left: 200px;; color:white; padding:8px 14px; border-radius:5px; text-decoration:none;">⬅️Geri </a>

    </form>

   

    <p style="color:green;"><?php echo $mesaj; ?></p>

</div></body>
</html>
