
<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected = $_POST["interests"] ?? [];
    $joined = implode(",", $selected);

    $stmt = $conn->prepare("UPDATE users SET interests = ? WHERE id = ?");
    $stmt->bind_param("si", $joined, $_SESSION["userid"]);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

$secenekler = ["Açık Hava Sineması", "Konser", "Spor Müsabakaları", "Tiyatro"];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İlgi Alanları</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e6f2ff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: white;
            margin: 50px auto;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 5px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0077b6;
        }
        label {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #0077b6;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #005f87;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎯 İlgi Alanlarını Seç</h2>
        <form method="POST">
            <?php foreach ($secenekler as $secenek): ?>
                <label><input type="checkbox" name="interests[]" value="<?= $secenek ?>"> <?= $secenek ?></label>
            <?php endforeach; ?>
            <input type="submit" value="Kaydet">
        </form>
    </div>
</body>
</html>
