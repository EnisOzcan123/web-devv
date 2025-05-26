
<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION["userid"];

$sql = "SELECT e.title, e.date, e.type, 
               SUM(t.quantity) AS total_quantity,
               SUM(t.total_price) AS total_price,
               MAX(t.created_at) AS latest_purchase
        FROM tickets t
        JOIN events e ON t.event_id = e.id
        WHERE t.user_id = ?
        GROUP BY e.id
        ORDER BY latest_purchase DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Biletlerim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef6f9;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0077b6;
        }
        table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #00bcd4;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🎫 Satın Aldığınız Biletler</h2>
    <table>
        <tr>
            <th>Etkinlik</th>
            <th>Tarih</th>
            <th>Tür</th>
            <th>Adet</th>
            <th>Toplam Tutar</th>
            <th>Satın Alma Tarihi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["title"]) ?></td>
                <td><?= $row["date"] ?></td>
                <td><?= $row["type"] ?></td>
                <td><?= $row["total_quantity"] ?></td>
                <td><?= number_format($row["total_price"], 2) ?> TL</td>
                <td><?= $row["latest_purchase"] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
