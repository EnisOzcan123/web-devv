document.getElementById("registerForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = e.target[0].value;
  const password = e.target[1].value;

  // Şu an gerçek backend yok, bu yüzden sahte kayıt mesajı gösteriyoruz
  console.log("Kayıt Bilgileri:", { email, password });

  alert("Kayıt başarılı! (Gerçek kayıt işlemi backend hazır olunca yapılacak)");
  window.location.href = "index.html";
});