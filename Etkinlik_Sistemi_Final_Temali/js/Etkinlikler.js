const etkinlikler = [
  {
    id: 1,
    baslik: "Kodlama Atölyesi",
    tur: "Teknoloji",
    tarih: "2025-05-27",
    kontenjan: 50,
    aciklama: "Yeni başlayanlar için HTML/CSS/JS eğitimi."
  },
  {
    id: 2,
    baslik: "Müzik Gecesi",
    tur: "Sanat",
    tarih: "2025-06-03",
    kontenjan: 200,
    aciklama: "Canlı müzik ve açık hava konseri."
  },
  {
    id: 3,
    baslik: "Tiyatro Gösterisi",
    tur: "Kültür",
    tarih: "2025-06-10",
    kontenjan: 100,
    aciklama: "Modern tiyatrodan keyifli bir eser."
  }
];

const listeDiv = document.getElementById("etkinlikListesi");

etkinlikler.forEach(etkinlik => {
  const etkinlikDiv = document.createElement("div");
  etkinlikDiv.classList.add("etkinlik");

  etkinlikDiv.innerHTML = `
    <h3>${etkinlik.baslik}</h3>
    <p><strong>Tür:</strong> ${etkinlik.tur}</p>
    <p><strong>Tarih:</strong> ${etkinlik.tarih}</p>
    <p><strong>Kontenjan:</strong> ${etkinlik.kontenjan}</p>
    <p>${etkinlik.aciklama}</p>
    <button onclick="sepeteEkle(${etkinlik.id})">Bilet Al</button>
    <hr/>
  `;

  listeDiv.appendChild(etkinlikDiv);
});

function sepeteEkle(etkinlikId) {
  const secilen = etkinlikler.find(e => e.id === etkinlikId);
  let sepet = JSON.parse(localStorage.getItem("sepet")) || [];
  sepet.push(secilen);
  localStorage.setItem("sepet", JSON.stringify(sepet));
  alert(`${secilen.baslik} etkinliği sepete eklendi!`);
}