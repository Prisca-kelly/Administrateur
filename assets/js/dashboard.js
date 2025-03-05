function repatitionReservationParMois(mois, res) {
  var tauxReservation = {
    chart: {
      height: 380,
      type: "area",
    },
    dataLabels: {
      enabled: false,
    },
    series: [
      {
        name: "",
        data: res,
      },
    ],
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.7,
        opacityTo: 0.9,
        stops: [0, 90, 100],
      },
    },
    xaxis: {
      categories: mois,
    },
  };

  var chart = new ApexCharts(
    document.querySelector("#tauxReservations"),
    tauxReservation
  );

  chart.render();
}

// REPARTITION DES RESERVATIONS PAR HOTEL
function destinationReservation(data) {
  var hotelOptions = {
    series: [
      {
        name: "Reservations",
        data: data?.number,
      },
    ],
    chart: {
      type: "bar",
      height: 300,
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "55%",
        borderRadius: 5,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ["transparent"],
    },
    xaxis: {
      categories: data.noms,
    },
    fill: {
      opacity: 1,
    },
  };

  var chartHotel = new ApexCharts(
    document.querySelector("#chartHotel"),
    hotelOptions
  );
  chartHotel.render();
}

// UTILISATEURS
function utilisateurChart(data1, data2) {
  var optionsUtilisateur = {
    chart: {
      height: 300,
      type: "pie",
    },
    series: [data1.count, data2.count],
    labels: [data1.role, data2.role],
    legend: {
      show: false,
    },
  };

  var chartUtilisateurs = new ApexCharts(
    document.querySelector("#repartitionUtilisateurs"),
    optionsUtilisateur
  );

  chartUtilisateurs.render();
}
