<?php
header('Content-Type: application/json');

// GNews API key
$apiKey = '';
$endpoint = "";

// Use cURL for better control
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // timeout after 5 seconds

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch) || $httpCode !== 200) {
    echo json_encode([
        "status" => "error",
        "message" => "Unable to fetch news from GNews",
        "http_code" => $httpCode,
        "curl_error" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// Validate and decode JSON response
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($data['articles'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON structure received"
    ]);
    exit;
}

// Success
echo json_encode([
    "status" => "success",
    "articles" => $data['articles']
]);
?>
