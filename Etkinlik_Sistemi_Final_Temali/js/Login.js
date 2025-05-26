// js/login.js

document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = e.target[0].value;
  const password = e.target[1].value;

  // Bu da sahte giriş kontrolü, gerçek API ile değiştirilecek
  if (email === "test@kullanici.com" && password === "6161") {
    alert("Giriş başarılı!");
    // Giriş yaptıktan sonra etkinlik listesi sayfasına yönlendir
    window.location.href = "etkinlikler.html";
  } else {
    alert("Hatalı e-posta veya şifre!");
  }
});
