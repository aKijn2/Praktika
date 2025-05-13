// Cuando se hace clic en "Erreserbatu"
document.querySelectorAll(".button").forEach((btn) => {
  if (btn.textContent.trim() === "Erreserbatu") {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      document.getElementById("reservaModal").style.display = "block";
    });
  }
});

// Cerrar el modal
document.querySelector(".close").onclick = function () {
  document.getElementById("reservaModal").style.display = "none";
};

// Cerrar al hacer clic fuera
window.onclick = function (event) {
  if (event.target == document.getElementById("reservaModal")) {
    document.getElementById("reservaModal").style.display = "none";
  }
};
