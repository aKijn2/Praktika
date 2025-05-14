// Modal: Eskatu orain
const eskatuModal = document.getElementById("eskatuModal");
const eskatuBtn = document.getElementById("eskatuOrainBtn");
const closeEskatu = document.getElementById("closeEskatu");

eskatuBtn.onclick = function (e) {
  e.preventDefault();
  eskatuModal.style.display = "block";
};

closeEskatu.onclick = function () {
  eskatuModal.style.display = "none";
};

// Modal: Erreserbatu
const erreserbatuModal = document.getElementById("erreserbatuModal");
const erreserbatuBtn = document.getElementById("erreserbatuBtn");
const closeErreserbatu = document.getElementById("closeErreserbatu");

erreserbatuBtn.onclick = function (e) {
  e.preventDefault();
  erreserbatuModal.style.display = "block";
};

closeErreserbatu.onclick = function () {
  erreserbatuModal.style.display = "none";
};

// Cerrar cualquier modal al clicar fuera
window.onclick = function (event) {
  if (event.target == eskatuModal) {
    eskatuModal.style.display = "none";
  }
  if (event.target == erreserbatuModal) {
    erreserbatuModal.style.display = "none";
  }
};
