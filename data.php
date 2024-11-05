<?php
header('Content-Type: application/json'); // Set the content type to JSON

// Database connection parameters
$host = 'localhost'; // Use quotes
$dbname = 'postgres'; // Use quotes
$user = 'postgres'; // Use quotes
$password = 'postgresql'; // Use quotes (replace with your actual password)

try {
    // Create a new PDO instance
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch bus stops from the correct table
    $stmt = $pdo->query("SELECT stop_name AS name, ST_AsGeoJSON(geom) AS geom FROM stops_api");
    $stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert to GeoJSON format
    $geojson = [
        "type" => "FeatureCollection",
        "features" => []
    ];

    foreach ($stops as $stop) {
        $geojson['features'][] = [
            "type" => "Feature",
            "properties" => ["name" => $stop['name']],
            "geometry" => json_decode($stop['geom'])
        ];
    }

    echo json_encode($geojson); // Output the GeoJSON
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]); // Handle errors
}
?>