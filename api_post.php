<?php

// Use an external configuration file for database credentials
require_once 'db_config.php'; // Ensure this file is properly secured

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

    $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $userAgent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
    $dateCreation = date('Y-m-d H:i:s');

    // Prepare the SQL statement
    $sql = "INSERT INTO logs (ip, user_agent, date_creation, data_json) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    // Bind parameters and execute the query
    $stmt->bind_param("ssss", $ip, $userAgent, $dateCreation, $dataJson);
    $stmt->execute();

    // Close the statement
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