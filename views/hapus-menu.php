<?php
require_once '../config/database.php';

$id = $_GET['id'];
$asm_id = $_GET['asm_id'];

$stmt = $conn->prepare("DELETE FROM patient_meals WHERE id = ?");
$stmt->execute([$id]);

header("Location: pilih-menu.php?asm_id=" . $asm_id);
exit();