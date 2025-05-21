document.addEventListener("DOMContentLoaded", function () {
  const erreserbaSelect = document.getElementById("erreserba");
  const dataField = document.getElementById("data-field");
  const orduaField = document.getElementById("ordua-field");

  function toggleDateTimeFields() {
    const hasReservation = erreserbaSelect && erreserbaSelect.value;
    if (hasReservation) {
      dataField.style.display = "none";
      orduaField.style.display = "none";
      document.getElementById("data").required = false;
      document.getElementById("ordua").required = false;
    } else {
      dataField.style.display = "block";
      orduaField.style.display = "block";
      document.getElementById("data").required = true;
      document.getElementById("ordua").required = true;
    }
  }

  if (erreserbaSelect) {
    toggleDateTimeFields(); // Inicial al cargar
    erreserbaSelect.addEventListener("change", toggleDateTimeFields);
  }
});
