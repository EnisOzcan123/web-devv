<?php
header('Content-Type: application/json'); // JSON çıktısı
include "config.php";

// Etkinlikleri tarihe göre sırala
$sorgu = $conn->query("SELECT * FROM events ORDER BY date ASC");

$etkinlikler = [];

while ($satir = $sorgu->fetch_assoc()) {
    $etkinlikler[] = $satir;
}

// JSON olarak döndür
echo json_encode($etkinlikler);
?>
