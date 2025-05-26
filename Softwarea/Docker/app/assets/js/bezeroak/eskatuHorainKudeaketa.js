document.addEventListener("DOMContentLoaded", function () {
  const erreserbaSelect = document.getElementById("erreserba");
  const dataInput = document.getElementById("data");
  const orduaInput = document.getElementById("ordua");
  const dataLabel = document.querySelector("label[for='data']");
  const orduaLabel = document.querySelector("label[for='ordua']");

  if (
    !erreserbaSelect ||
    !dataInput ||
    !orduaInput ||
    !dataLabel ||
    !orduaLabel
  ) {
    console.error("Uno o más elementos no se encontraron");
    return;
  }

  function toggleDateTimeFields() {
    const hasReservation = erreserbaSelect.value !== "";

    dataInput.style.display = hasReservation ? "none" : "block";
    orduaInput.style.display = hasReservation ? "none" : "block";
    dataLabel.style.display = hasReservation ? "none" : "block";
    orduaLabel.style.display = hasReservation ? "none" : "block";

    dataInput.required = !hasReservation;
    orduaInput.required = !hasReservation;
  }

  // Ejecutar al cargar
  toggleDateTimeFields();

  // Ejecutar cuando cambia la selección
  erreserbaSelect.addEventListener("change", toggleDateTimeFields);
});
