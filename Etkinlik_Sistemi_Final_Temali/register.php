<?php
include "config.php";

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $kontrol = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $kontrol->bind_param("s", $email);
    $kontrol->execute();
    $kontrol->store_result();

    if ($kontrol->num_rows > 0) {
        $mesaj = "Bu e-posta zaten kayıtlı!";
    } else {
        $ekle = $conn->prepare("INSERT INTO users (email, password, role, approved, must_change_password) VALUES (?, ?, 'user', 0, 1)");
        $ekle->bind_param("ss", $email, $sifre);
        if ($ekle->execute()) {
            $mesaj = "Kayıt başarılı! Yönetici onayını bekleyin.";
        } else {
            $mesaj = "Bir hata oluştu: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="Style_theme.css">
    <style>
    body {
        background: url('https://www.rudaw.net/s3/rudaw.net/ContentFiles/788435Image1.jpg?version=6428388') no-repeat center -150px;
        background-size: cover;
    }
    </style>
</head>
<body>
<div class="container">
    <h2>Kayıt Ol</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="E-posta" required />
        <input type="password" name="password" placeholder="Şifre" required />
        <input type="submit" value="Kayıt">
    </form>
    <p class="message"><?= htmlspecialchars($mesaj) ?></p>
    <a href="login.php">Zaten hesabın var mı? Giriş yap</a>
</div>
</body>
</html>
