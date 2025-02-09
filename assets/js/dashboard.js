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
      name: "Series 1",
      data: [10, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
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
    categories: [
      "Jan",
      "Fev",
      "Mars",
      "Avr",
      "Mai",
      "Juin",
      "Juil",
      "Août",
      "Sept",
      "Oct",
      "Nov",
      "Dec",
    ],
  },
};

var chart = new ApexCharts(
  document.querySelector("#tauxReservations"),
  tauxReservation
);

chart.render();

// REPARTITION DES RESERVATIONS PAR HOTEL
var hotelOptions = {
  series: [
    {
      name: "Reservations",
      data: [44, 55, 57, 56, 61, 58, 63, 60, 66],
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
    categories: [
      "Ibuscus",
      "Montana",
      "Julevier",
      "ONIKA",
      "OZUKI",
      "RYO",
      "KAÏZEN",
      "SEMTEM",
      "KORIU",
    ],
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

// UTILISATEURS
var optionsUtilisateur = {
  chart: {
    height: 300,
    type: "pie",
  },
  series: [90, 10],
  labels: ["Clients", "Administrateur"],
  legend: {
    show: false,
  },
};

var chartUtilisateurs = new ApexCharts(
  document.querySelector("#repartitionUtilisateurs"),
  optionsUtilisateur
);

chartUtilisateurs.render();
