const ezagutuBtn = document.getElementById("ezagutuBtn");
const ezagutuModal = document.getElementById("ezagutuModal");
const closeEzagutu = document.getElementById("closeEzagutu");

ezagutuBtn.addEventListener("click", function (e) {
  e.preventDefault();
  ezagutuModal.style.display = "block";
});

closeEzagutu.addEventListener("click", function () {
  ezagutuModal.style.display = "none";
});

window.addEventListener("click", function (e) {
  if (e.target == ezagutuModal) {
    ezagutuModal.style.display = "none";
  }
});
