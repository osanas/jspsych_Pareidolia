<?php

// Établissez la connexion à la base de données
$host = "localhost";
$user = "anaso518_Pareildoliajl12h3109";
$pass = "-CPgIYHI-(Kr";
$db = "anaso518_Pareildolia_i1uy2n3";

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("La connexion à la base de données a échoué : " . $mysqli->connect_error);
}

// Votre données JSON brut
// $jsonData = '{"nom": "John", "age": 30, "ville": "Paris"}';

// Préparez la requête SQL avec un paramètre
// $query = "INSERT INTO votre_table (ID, DateCreation, IP, UserAgent, DataJson) VALUES (?, ?, ?, ?, ?)";
$query = "INSERT INTO votre_table (DataJson) VALUES (?)";
$stmt = $mysqli->prepare($query);

if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $mysqli->error);
}

// Liez le paramètre
$stmt->bind_param("s", $jsonData);

// Exécutez la requête
if ($stmt->execute()) {
    echo "Données JSON insérées avec succès.";
} else {
    echo "Erreur lors de l'insertion des données JSON : " . $stmt->error;
}

// Fermez la connexion
$stmt->close();
$mysqli->close();

?>