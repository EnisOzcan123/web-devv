
<?php
session_start();
include "config.php";

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION["userid"];

$apiKey = "570d803043d71414b7d23113d492c04b";
$city = "Istanbul";
$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=tr";

$bugun = date("Y-m-d");
$havaEngeliVar = false;

$response = @file_get_contents($url);
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data["weather"][0]["main"])) {
        $durum = strtolower($data["weather"][0]["main"]);
        if (in_array($durum, ['rain', 'storm', 'snow', 'thunderstorm'])) {
            $havaEngeliVar = true;
        }
    }
}

$etkinlikler = $conn->query("SELECT * FROM events ORDER BY date ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bilet Al</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f9ff;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0277bd;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #00acc1;
            color: white;
        }
        input[type='number'] {
            width: 60px;
            padding: 5px;
        }
        .disabled {
            background-color: #e0e0e0;
            color: #999;
        }
        .btn {
            margin-top: 30px;
            display: inline-block;
            padding: 12px 20px;
            background-color: #43a047;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        #payment-section {
            display: none;
            margin-top: 40px;
            padding: 20px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            border-radius: 8px;
        }
        #payment-section input, select {
            padding: 10px;
            margin-top: 10px;
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        #total-price-info {
            font-weight: bold;
            color: #00796b;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🎟️ Bilet Al</h2>
    <form id="ticketForm" method="POST" action="my_tickets.php">
        <table>
            <tr>
                <th>Etkinlik</th>
                <th>Tarih</th>
                <th>Tür</th>
                <th>Fiyat</th>
                <th>Stok</th>
                <th>Adet</th>
            </tr>
            <?php while ($e = $etkinlikler->fetch_assoc()): ?>
                <?php
                    $etkinlikTarihi = $e['date'];
                    $engellenmis = ($etkinlikTarihi == $bugun && $havaEngeliVar);
                ?>
                <tr<?= $engellenmis ? ' class="disabled"' : '' ?>>
                    <td><?= htmlspecialchars($e["title"]) ?></td>
                    <td><?= $etkinlikTarihi ?></td>
                    <td><?= $e["type"] ?></td>
                    <td><?= $e["price"] ?> TL</td>
                    <td><?= $e["capacity"] ?></td>
                    <td>
                        <?php if (!$engellenmis): ?>
                            <input type="number" name="quantity[<?= $e['id'] ?>]" min="0" max="<?= $e["capacity"] ?>" value="0">
                        <?php else: ?>
                            <span>İptal edildi</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div style="text-align: center;">
            <button type="button" class="btn" onclick="showPayment()">Satın Al</button>
        </div>

        <div id="payment-section">
            <h3>🧾 Ödeme Yöntemi Seçin</h3>
            <label><input type="radio" name="payment_method" value="eft" checked onchange="togglePaymentDetails()"> EFT</label>
            <label><input type="radio" name="payment_method" value="card" onchange="togglePaymentDetails()"> Kredi Kartı</label>

            <div id="total-price-info">Toplam Tutar: 0 TL</div>

            <div id="eft-info" style="margin-top:15px;">
                <strong>IBAN:</strong> TR61 6161 2525 2222 6161 2222 61
            </div>

            <div id="card-fields" style="display: none;">
                <input type="text" name="card_name" placeholder="Ad Soyad">
                <input type="text" name="card_number" placeholder="Kart Numarası">
                <input type="text" name="card_date" placeholder="Son Kullanma Tarihi (AA/YY)">
                <input type="text" name="card_cvv" placeholder="CVV">
            </div>

            <div style="text-align: center;">
                <button class="btn" type="submit">Ödemeyi Tamamla</button>
            </div>
        </div>
    </form>
</div>

<script>
    function showPayment() {
        const form = document.getElementById('ticketForm');
        const payment = document.getElementById('payment-section');
        let hasTicket = false;
        const inputs = form.querySelectorAll("input[type='number']");
        inputs.forEach(input => {
            if (parseInt(input.value) > 0) hasTicket = true;
        });
        if (hasTicket) {
            payment.style.display = "block";
            updateTotal();
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        } else {
            alert("Lütfen en az bir etkinlik seçin.");
        }
    }

    function togglePaymentDetails() {
        const method = document.querySelector("input[name='payment_method']:checked").value;
        const card = document.getElementById('card-fields');
        const eft = document.getElementById('eft-info');
        if (method === "card") {
            card.style.display = "block";
            eft.style.display = "none";
        } else {
            card.style.display = "none";
            eft.style.display = "block";
        }
    }

    function updateTotal() {
        const inputs = document.querySelectorAll("input[type='number'][name^='quantity']");
        const prices = document.querySelectorAll("table tr");
        let priceMap = {};
        prices.forEach(row => {
            const input = row.querySelector("input[name^='quantity']");
            if (input) {
                const id = input.name.match(/\d+/)[0];
                const priceText = row.children[3].innerText;
                const price = parseFloat(priceText.replace(" TL", "").replace(",", "."));
                priceMap[id] = price;
            }
        });

        let total = 0;
        inputs.forEach(input => {
            const id = input.name.match(/\d+/)[0];
            const qty = parseInt(input.value) || 0;
            total += qty * (priceMap[id] || 0);
        });

        document.getElementById("total-price-info").innerText = "Toplam Tutar: " + total.toLocaleString("tr-TR") + " TL";
    }

    document.querySelectorAll("input[type='number'][name^='quantity']").forEach(input => {
        input.addEventListener("input", updateTotal);
    });
</script>
</body>
</html>
