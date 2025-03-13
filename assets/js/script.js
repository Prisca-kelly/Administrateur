// Alerte pour demander confirmation sur l'action à effectuer
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

// Alerte pour demander l'insertion d'une valeur afin de valider
function confirmInputSweetAlert(msg = "") {
  return Swal.fire({
    title: "Confirmation",
    text: msg,
    input: "number",
    inputAttributes: {
      min: 0,
    },
    showConfirmButton: true,
    showDenyButton: true,
    confirmButtonText: "Valider",
    denyButtonText: "Annuler",
  });
}

// Alerte pour marquer une opération réussie
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

// Alerte pour marquer une erreur de process
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
  if (temps) temps.innerText = heure < 12 ? "Bonjour" : "Bonsoir";
}
temps();

function ajaxRequest(method, url, data) {
  $.ajax({
    type: method,
    url: url,
    data: data,
    dataType: "json",
    success: function (response) {
      if (response.code === 200) {
        successSweetAlert(response.message);
      } else if (response.code === 400 || response.code === 500) {
        errorSweetAlert(response.message);
      }
    },
  });
}
