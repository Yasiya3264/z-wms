<?php
// Database config
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_pass";
$dbname = "your_db_name";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  die("DB connection failed.");
}

$data = json_decode(file_get_contents("php://input"), true);

$location_code = $conn->real_escape_string($data['location_code']);
$length = floatval($data['length']);
$width = floatval($data['width']);
$height = floatval($data['height']);
$cbm = floatval($data['cbm']);
$occupancy = $conn->real_escape_string($data['occupancy']);
$status = intval($data['status']);

$sql = "INSERT INTO warehouse_locations 
  (location_code, length_cm, width_cm, height_cm, cbm, occupancy_type, status) 
  VALUES ('$location_code', $length, $width, $height, $cbm, '$occupancy', $status)";

if ($conn->query($sql)) {
  echo "✅ Inserted";
} else {
  echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
