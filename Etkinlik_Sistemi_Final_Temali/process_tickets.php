
<?php
session_start();
include "config.php";

// Debug logs
file_put_contents("log_POST.txt", print_r($_POST, true));
file_put_contents("log_SESSION.txt", print_r($_SESSION, true));

if (!isset($_SESSION["userid"])) {
    die("Kullanıcı oturumu bulunamadı.");
}

$userid = $_SESSION["userid"];

if (!isset($_POST["quantity"]) || !is_array($_POST["quantity"])) {
    die("Adet bilgisi alınamadı.");
}

$quantities = $_POST["quantity"];
$method = $_POST["payment_method"] ?? '';
$valid_payment = false;

if ($method === "eft") {
    $valid_payment = true;
} elseif (
    $method === "card"
    && !empty($_POST["card_name"])
    && !empty($_POST["card_number"])
    && !empty($_POST["card_date"])
    && !empty($_POST["card_cvv"])
) {
    $valid_payment = true;
}

if (!$valid_payment) {
    die("Geçerli ödeme yöntemi seçilmedi veya bilgiler eksik.");
}

foreach ($quantities as $event_id => $qty) {
    $qty = intval($qty);
    if ($qty <= 0) continue;

    $event = $conn->prepare("SELECT price, capacity FROM events WHERE id = ?");
    $event->bind_param("i", $event_id);
    $event->execute();
    $result = $event->get_result();

    if ($result->num_rows === 0) continue;

    $row = $result->fetch_assoc();
    $price = $row["price"];
    $capacity = $row["capacity"];

    if ($qty > $capacity) continue;

    $total_price = $qty * $price;

    $save = $conn->prepare("INSERT INTO tickets (user_id, event_id, quantity, total_price, created_at) VALUES (?, ?, ?, ?, NOW())");
    $save->bind_param("iiid", $userid, $event_id, $qty, $total_price);
    $save->execute();

    $update = $conn->prepare("UPDATE events SET capacity = capacity - ? WHERE id = ?");
    $update->bind_param("ii", $qty, $event_id);
    $update->execute();
}

header("Location: my_tickets.php");
exit;
?>
