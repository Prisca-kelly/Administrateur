<?php

require_once "../config/database.php";

if (isset($_GET["repatitionUtilisateur"]) && $_GET["repatitionUtilisateur"] == "true") {
    $result = $bdd->query("SELECT COUNT(*) as count, role FROM utilisateur GROUP BY role");
    $result->execute();

    echo json_encode($result->fetchAll());
}

if (isset($_GET["repatitionReservation"]) && $_GET["repatitionReservation"] == "true") {
    $result = $bdd->query("SELECT  d.nom,  COUNT(r.id_reservation) as count FROM reservation r RIGHT JOIN destination d ON r.id_destination = d.id_destination GROUP BY d.id_destination, d.nom LIMIT 100");
    $result->execute();

    echo json_encode($result->fetchAll());
}

if (isset($_GET["repatitionReservationParMois"]) && $_GET["repatitionReservationParMois"] == "true") {
    $result = $bdd->query("SELECT 
    CASE m.month
        WHEN 1 THEN 'Jan'
        WHEN 2 THEN 'Fév'
        WHEN 3 THEN 'Mars'
        WHEN 4 THEN 'Avr'
        WHEN 5 THEN 'Mai'
        WHEN 6 THEN 'Juin'
        WHEN 7 THEN 'Juil'
        WHEN 8 THEN 'Août'
        WHEN 9 THEN 'Sept'
        WHEN 10 THEN 'Oct'
        WHEN 11 THEN 'Nov'
        WHEN 12 THEN 'Déc'
    END as month_name,
    COALESCE(COUNT(r.id_reservation), 0) as reservation_count
FROM 
    (SELECT 1 AS month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 
     UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 
     UNION SELECT 11 UNION SELECT 12) m LEFT JOIN reservation r ON m.month = MONTH(r.date_creation) AND YEAR(r.date_creation) = 2025 GROUP BY m.month ORDER BY m.month LIMIT 100");
    $result->execute();

    echo json_encode($result->fetchAll());
}
