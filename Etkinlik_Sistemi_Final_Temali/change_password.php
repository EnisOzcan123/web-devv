<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yeni = $_POST["password"];
    $hash = password_hash($yeni, PASSWORD_DEFAULT);

    $guncelle = $conn->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
    $guncelle->bind_param("si", $hash, $_SESSION["userid"]);

    if ($guncelle->execute()) {
        // Şifre başarıyla güncellendiyse giriş sonrası yönlendirme
        if ($_SESSION["role"] == "admin") {
            header("Location: admin_panel.php");
        } else {
            header("Location: interests.php"); // index.php yerine doğrudan ilgi alanlarına gitsin
        }
        exit;
    } else {
        $mesaj = "Şifre güncellenemedi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="Style_theme.css"><title>Şifre Değiştir</title></head>
<body>
<div class="container">

    <h2>🔐 İlk Giriş - Şifreni Değiştir</h2>
    <form method="POST">
        Yeni Şifre: <input type="password" name="password" required><br><br>
        <input type="submit" value="Değiştir">
    </form>
    <p style="color:red;"><?= $mesaj ?></p>

</div></body>
</html>
