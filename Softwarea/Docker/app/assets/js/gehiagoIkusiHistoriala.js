document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("ver-mas-btn");
  const cards = document.querySelectorAll(".historiala-card");
  let visibleCount = 2;

  if (!btn) return;

  // Inicialmente solo 2 visibles, ya está en PHP pero por si acaso
  cards.forEach((card, i) => {
    card.style.display = i < visibleCount ? "flex" : "none";
  });

  btn.addEventListener("click", () => {
    const total = cards.length;
    const nextCount = visibleCount + 2;

    if (visibleCount >= total) {
      // Si ya muestra todo, oculta extras y vuelve a 2
      for (let i = 2; i < total; i++) {
        cards[i].style.display = "none";
      }
      visibleCount = 2;
      btn.textContent = "GEHIAGO IKUSI";
    } else {
      // Mostrar 2 más
      for (let i = visibleCount; i < nextCount && i < total; i++) {
        cards[i].style.display = "flex";
      }
      visibleCount = nextCount;

      if (visibleCount >= total) {
        btn.textContent = "IKUSI GUTXIAGO";
      } else {
        btn.textContent = "GEHIAGO IKUSI";
      }
    }
  });
});
