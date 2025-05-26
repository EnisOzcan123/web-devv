

const sepet = JSON.parse(localStorage.getItem("sepet")) || [];
const sepetDiv = document.getElementById("sepetListesi");
const odemeAlani = document.getElementById("odemeAlani");
const toplamFiyatSpan = document.getElementById("toplamFiyat");

let toplam = 0;

if (sepet.length === 0) {
  sepetDiv.innerHTML = "<p>Sepetiniz boş.</p>";
} else {
  sepet.forEach((etkinlik, index) => {
    const fiyat = etkinlik.tur === "Öğrenci" ? 50 : 100; // örnek fiyat
    toplam += fiyat;

    const etkinlikDiv = document.createElement("div");
    etkinlikDiv.classList.add("etkinlik");
    etkinlikDiv.innerHTML = `
      <h3>${etkinlik.baslik}</h3>
      <p><strong>Tarih:</strong> ${etkinlik.tarih}</p>
      <p><strong>Bilet Türü:</strong> Tam</p>
      <p><strong>Fiyat:</strong> ${fiyat} TL</p>
    `;
    sepetDiv.appendChild(etkinlikDiv);
  });

  toplamFiyatSpan.textContent = toplam.toFixed(2);
  odemeAlani.style.display = "block";
}

function odemeYap() {
  alert("Ödeme işlemi başarılı! 🎉");
  localStorage.removeItem("sepet");
  window.location.href = "etkinlikler.html";
}