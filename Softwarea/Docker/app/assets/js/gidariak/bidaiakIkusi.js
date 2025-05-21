function irekiBidaiarenModala(bidaia) {
  document.getElementById("modal_id_bidaia").value = bidaia.id_bidaia;
  document.getElementById("modal_jatorria").textContent = bidaia.jatorria;
  document.getElementById("modal_helmuga").textContent = bidaia.helmuga;
  document.getElementById("modal_data").textContent = bidaia.data;
  document.getElementById("modal_ordua").textContent = bidaia.ordua;
  document.getElementById("modal_kopurua").textContent =
    bidaia.pertsona_kopurua;

  document.getElementById("bidaiaModal").style.display = "flex";
}

function itxiModala() {
  document.getElementById("bidaiaModal").style.display = "none";
}
