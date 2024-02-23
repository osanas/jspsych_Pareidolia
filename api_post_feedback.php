<?php

// Utilisation d'un fichier de configuration externe pour les identifiants de la base de données
require_once 'appconfig.php'; // Assurez-vous que ce fichier est correctement sécurisé

// Activation du rapport d'erreurs mysqli en mode exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Établissement d'une connexion à la base de données
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Récupération et nettoyage des données d'entrée
    $dataJson = file_get_contents('php://input');
    if (!$dataJson) {
        throw new Exception("error dataJson");
    }
    $decodedData = json_decode($dataJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("error json_last_error");
    }

    // Assurez-vous que 'userID' est présent dans les données décodées
    if (!isset($decodedData['userID'])) {
        throw new Exception("UserID manquant");
    }
    $userID = $decodedData['userID'];

    // Convertit les données en JSON pour stockage (si nécessaire)
    // Note: Assurez-vous que la colonne `feedback` peut stocker correctement des données JSON.
    $feedbackJson = json_encode($decodedData);

    // Mise à jour de la base de données avec les données JSON dans la colonne 'feedback' pour le 'userID' spécifié
    $sql = "UPDATE logs SET feedback = ? WHERE user_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $feedbackJson, $userID);
    $stmt->execute();

    // Vérification si la mise à jour a réussi
    if ($stmt->affected_rows === 0) {
        throw new Exception("Aucune mise à jour effectuée. Vérifiez l'existence du userID.");
    }

    // Fermeture du statement
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    // Gestion des exceptions telles que les erreurs de connexion et de requête
    echo "error". $e->getMessage();
} finally {
    // Fermeture de la connexion si elle a été établie
    if ($mysqli) {
        $mysqli->close();
    }
}
?>