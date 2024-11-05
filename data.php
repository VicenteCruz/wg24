<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection parameters
$host = 'localhost'; // Use quotes
$dbname = 'postgres'; // Use quotes
$user = 'postgres'; // Use quotes
$password = 'postgresql'; // Use quotes (replace with your actual password)

try {
    // Create a new PDO instance
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch bus stops from the correct table, including district and routes
    $stmt = $pdo->query("SELECT stop_id as id, stop_name AS name, ST_AsGeoJSON(geom) AS geom, district, routes, municipality_id AS municipality, patterns FROM stops_api");
    $stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert to GeoJSON format
    $geojson = [
        "type" => "FeatureCollection",
        "features" => []
    ];

    foreach ($stops as $stop) {
        $geojson['features'][] = [
            "type" => "Feature",
            "properties" => [
                "id" => $stop['id'],
                "name" => $stop['name'],
                "district" => (int)$stop['district'], // Add district as an integer
                "municipality" => (int)$stop['municipality'],
                "routes" => (empty($stop['routes']) || $stop['routes'] === '{}') ? [] : array_map('intval', explode(',', trim($stop['routes'], '{}'))), // Convert to integer array
                "patterns" => (empty($stop['patterns']) || $stop['patterns'] === '{}') ? [] : array_map('intval', explode(',', trim($stop['patterns'], '{}'))) // Convert patterns to integer array
            ],
            "geometry" => json_decode($stop['geom'])
        ];
        
    }

    echo json_encode($geojson); // Output the GeoJSON
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]); // Handle errors
}
?>