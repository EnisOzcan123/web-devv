<?php

$host = "localhost";
$user = "root";           // XAMPP için genelde root
$password = "";           // XAMPP'ta şifre genelde yok
$database = "etkinlik_db"; // phpMyAdmin'de oluşturduğun DB adı

$conn = new mysqli($host, $user, $password, $database);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


$conn->set_charset("utf8");
?>
