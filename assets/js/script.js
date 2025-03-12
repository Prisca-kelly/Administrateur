function confirmSweetAlert(msg = "") {
  return Swal.fire({
    icon: "question",
    title: "Confirmation",
    text: msg,
    showConfirmButton: true,
    showDenyButton: true,
    confirmButtonText: "Oui",
    denyButtonText: "Non",
  });
}

function confirmInputSweetAlert(msg = "") {
  return Swal.fire({
    title: "Confirmation",
    text: msg,
    input:"number",
    inputAttributes: {
      min: 0
    },
    showConfirmButton: true,
    showDenyButton: true,
    confirmButtonText: "Valider",
    denyButtonText: "Annuler",
  });
}

function successSweetAlert(msg = "", timer = 2000) {
  Swal.fire({
    icon: "success",
    title: "Opération réussie",
    text: msg,
    showConfirmButton: false,
    timer: timer,
  }).then(function () {
    location.reload();
  });
}

function errorSweetAlert(msg = "", timer = 2000) {
  Swal.fire({
    icon: "error",
    title: "Erreur",
    text: msg,
    confirmButtonText: "Ok",
    timer: timer,
  }).then((out) => {});
}

function temps() {
  const heure = new Date().getHours();
  const temps = document.getElementById("temps");
  temps.innerText = heure < 12 ? "Bonjour" : "Bonsoir";
}

temps();
