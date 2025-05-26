<?php
$apiKey = ""
$city = "Istanbul";

$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=tr";
$response = file_get_contents($url);
$data = json_decode($response, true);

$durum = $data["weather"][0]["description"];
$sicaklik = $data["main"]["temp"];
?>

<div class="weather-box">
    <h3>🌤️ Hava Durumu – <?= $city ?></h3>
    <p>Durum: <?= ucfirst($durum) ?></p>
    <p>Sıcaklık: <?= round($sicaklik) ?>°C</p>
</div>
