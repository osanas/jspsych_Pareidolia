<?php
$servername = "your_server_name";
$username = "your_username";
$password = "your_password";
$dbname = "your_dbname";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to fetch data
$sql = "SELECT data_json FROM logs";
$result = $conn->query($sql);

$words_with_fd = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $json_data = $row["data_json"];
        $decoded_json = json_decode($json_data, true);

        foreach ($decoded_json['trials'] as $trial) {
            if (isset($trial['stimulus']) && preg_match('/FD_([0-9]+)_/', $trial['stimulus'], $matches)) {
                $current_fd = (float)$matches[1] / 10;
            }

            if ($trial['trial_type'] === 'survey-text' && isset($trial['response']) && is_array($trial['response'])) {
                foreach ($trial['response'] as $word) {
                    if (!empty($word)) {
                        $word = trim(strtolower($word));
                        $words_with_fd[] = ['word' => $word, 'fd' => $current_fd];
                    }
                }
            }
        }
    }
} else {
    echo "0 results";
}

// Calculate the frequency of each word
$word_frequencies = array_count_values(array_column($words_with_fd, 'word'));

// Attach frequency to each word-FD pair
foreach ($words_with_fd as &$word_fd) {
    $word_fd['frequency'] = $word_frequencies[$word_fd['word']];
}

// Remove duplicates
$unique_words_with_fd = array_map("unserialize", array_unique(array_map("serialize", $words_with_fd)));

// Convert to indexed array and then to JSON
$words_json = json_encode(array_values($unique_words_with_fd)); 

// Call Python script for t-SNE computation
$command = escapeshellcmd("python3 semantic_analysis.py '$words_json'");
$output = shell_exec($command);

// Decode the JSON output from the Python script
$tsne_results = json_decode($output, true);

// Store or update $tsne_results in the database
// ...

// Provide $tsne_results for D3.js visualization
echo json_encode($tsne_results);

$conn->close();
?>
