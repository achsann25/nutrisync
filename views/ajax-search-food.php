<?php
require_once '../config/database.php';

$q = $_GET['q'] ?? '';
$stmt = $conn->prepare("SELECT id, name, energy_kcal, serving_size FROM foods WHERE name LIKE ? LIMIT 10");
$stmt->execute(["%$q%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);