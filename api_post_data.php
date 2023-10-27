<?php

// connexion
$host = "localhost";
$user = "anaso518_Pareildoliajl12h3109";
$pass = "-CPgIYHI-(Kr";
$db = "anaso518_Pareildolia_i1uy2n3";

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("La connexion à la base de données a échoué : " . $mysqli->connect_error);
}


// data brut
$dataJson = file_get_contents('php://input');
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$dateCreation = date('Y-m-d H:i:s');

// prepare
$sql = "INSERT INTO logs (ip, user_agent, date_creation, data_json) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // Liez les paramètres
    $stmt->bind_param("ssss", $ip, $userAgent, $dateCreation, $dataJson);

    // Exécutez la requête
    if ($stmt->execute()) {
        echo "Données insérées avec succès.";
    } else {
        echo "Erreur lors de l'insertion des données : " . $stmt->error;
    }

    // Fermez la déclaration
    $stmt->close();
} else {
    echo "Erreur de préparation de la requête : " . $mysqli->error;
}

$mysqli->close();

?>