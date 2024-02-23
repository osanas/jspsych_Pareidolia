<?php

// Use an external configuration file for database credentials
require_once 'appconfig.php'; // Ensure this file is properly secured

// Enable error reporting for mysqli in exception mode
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish a database connection
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Retrieve and sanitize input data
    $dataJson = file_get_contents('php://input');
    if (!$dataJson) {
        throw new Exception("error");
    }
    $decodedData = json_decode($dataJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("error");
    }

    // Validate or sanitize $dataJson as needed

    // Fonction de validation d'un essai individuel
    function validateTrial($trial)
    {
        if (isset($trial['rt']) && !is_numeric($trial['rt'])) {
            return "Erreur : 'rt' manquant ou non numérique.";
        }
        if (!isset($trial['trial_index']) || !is_int($trial['trial_index'])) {
            return "Erreur : 'trial_index' manquant ou non entier.";
        }
        if (!isset($trial['time_elapsed']) || !is_numeric($trial['time_elapsed'])) {
            return "Erreur : 'time_elapsed' manquant ou non numérique.";
        }
        if (!isset($trial['internal_node_id']) || !is_string($trial['internal_node_id'])) {
            return "Erreur : 'internal_node_id' manquant ou non chaîne.";
        }
        if (!isset($trial['name']) || !is_string($trial['name'])) {
            return "Erreur : 'name' manquant ou non chaîne.";
        }
        if (isset($trial['url_stimulus']) && !filter_var($trial['url_stimulus'], FILTER_VALIDATE_URL)) {
            return "Erreur : 'url_stimulus' non valide.";
        }
        if (isset($trial['data']) && is_array($trial['data'])) {
            if (!isset($trial['data']['userID']) || !is_string($trial['data']['userID'])) {
                return "Erreur : 'userID' dans 'data' manquant ou non chaîne.";
            }
            if (!isset($trial['data']['age']) || !is_numeric($trial['data']['age'])) {
                return "Erreur : 'age' dans 'data' manquant ou non numérique.";
            }
            // ... autres validations pour 'data'
        }

        // add validation words ----------- TODO
        return "valid"; // Tout est valide
    }

    //foreach ($decodedData as $trial) {
    //  $result = validateTrial($trial);
    //if ($result !== "valid") {
    //  die($result);
    //}
    //}

    $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $userAgent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
    $dateCreation = date('Y-m-d H:i:s');

    $userID = "NA";
    $age = "NA";
    $primary_language = "NA";
    $project_code = "NA";
    $feedback = "NA";
    foreach ($decodedData['trials'] as $trial) {
        if (isset($trial['name']) && $trial['name'] === 'demographic') {
            if (isset($trial['data'])) {
                $demographicData = $trial['data'];
                $userID = isset($demographicData['userID']) ? $demographicData['userID'] : $userID;
                $age = isset($demographicData['age']) ? $demographicData['age'] : $age;
                $primary_language = isset($demographicData['primary_language']) ? $demographicData['primary_language'] : $primary_language;
                $project_code = isset($demographicData['project_code']) ? $demographicData['project_code'] : $project_code;
                $feedback = "yo";
                break;
            }
        }
    }

    // Préparation de la requête SQL
    $sql = "INSERT INTO logs (date_creation, ip, user_id, age, project_code, primary_language, user_agent, data_json, feedback) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    // $sql = "INSERT INTO logs (ip, user_agent, date_creation, data_json, user_id, age, project_code, primary_language) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    // Liaison des paramètres
    $stmt->bind_param("sssssssss", $dateCreation, $ip, $userID, $age, $project_code, $primary_language, $userAgent, $dataJson, $feedback);
    // $stmt->bind_param("ssssssss", $ip, $userAgent, $dateCreation, $dataJson, $userID, $age, $project_code, $primary_language);

    // Exécution de la requête
    $stmt->execute();

    // Fermeture du statement
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    // Handle exceptions such as connection and query errors
    echo "error"; // . $e->getMessage();
} finally {
    // Close the connection if it was established
    if ($mysqli) {
        $mysqli->close();
    }
}

?>