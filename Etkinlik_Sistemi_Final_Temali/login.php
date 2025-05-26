<?php
session_start();
include "config.php";

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["password"];

    $sorgu = $conn->prepare("SELECT id, password, role, approved, must_change_password FROM users WHERE email = ?");
    $sorgu->bind_param("s", $email);
    $sorgu->execute();
    $sorgu->store_result();

    if ($sorgu->num_rows == 1) {
        $sorgu->bind_result($id, $hashli_sifre, $rol, $onay, $must_change);
        $sorgu->fetch();

        if (password_verify($sifre, $hashli_sifre)) {
            if ($onay == 1) {
                $_SESSION["userid"] = $id;
                $_SESSION["role"] = $rol;
                $_SESSION["email"] = $email;

                if ($must_change == 1) {
                    header("Location: change_password.php");
                    exit;
                }

                if ($rol == "admin") {
                    header("Location: admin_panel.php");
                    exit;
                } else {
                    header("Location: index.php");
                    exit;
                }
            } else {
                $mesaj = "Hesabın henüz onaylanmadı.";
            }
        } else {
            $mesaj = "Şifre hatalı.";
        }
    } else {
        $mesaj = "Bu e-posta ile kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="Style_theme.css">
    <title>Giriş Yap</title>
    <style>
    body {
        background: url('https://cdn1.ntv.com.tr/gorsel/Bt3XLG_ZSkSXr6L7rkgEFQ.jpg?width=960&mode=crop&scale=both') no-repeat center center fixed;
        background-size: cover;
    }
    </style>
</head>
<body>
<div class="container">
    <form method="POST">
        <h2>Giriş Yap</h2>
        <input type="email" name="email" placeholder="E-posta" required><br><br>
        <input type="password" name="password" placeholder="Şifre" required><br><br>
        <input type="submit" value="Giriş Yap">
    </form>
    <p style="color:red;"><?php echo $mesaj; ?></p>
</div>
</body>
</html>
