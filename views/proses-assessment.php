<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_POST['hitung']) || $_SERVER['REQUEST_METHOD'] == 'POST') {
    $p_id  = $_POST['patient_id'];
    $jk    = $_POST['jk'];
    $usia  = (int)$_POST['usia'];
    $bb    = (float)$_POST['bb'];
    $tb    = (float)$_POST['tb'];
    
    // Data Kompleks
    $tl    = (float)($_POST['tl'] ?? 0);
    $lila  = (float)($_POST['lila'] ?? 0);
    $td    = $_POST['td'] ?: '-';
    $gd    = (int)($_POST['gd'] ?? 0);
    $klinis = $_POST['klinis'] ?? '';
    $f_akt = (float)$_POST['f_aktivitas'];
    $f_str = (float)$_POST['f_stres'];

    // Estimasi TB via Chumlea
    if ($tb <= 0 && $tl > 0) {
        $tb = ($jk == 'L') ? (64.19 - (0.04 * $usia) + (2.02 * $tl)) : (84.88 - (0.24 * $usia) + (1.83 * $tl));
    }

    $bbi = hitungBBI($tb, $jk);
    $imt = ($tb > 0) ? ($bb / (($tb/100)**2)) : 0;
    $rmr = hitungMifflin($bb, $tb, $usia, $jk);
    $tee = hitungTEE($rmr, $f_akt, $f_str);

    try {
        $sql = "INSERT INTO assessments (patient_id, tgl_periksa, bb_kg, tb_cm, usia_thn, faktor_aktivitas, faktor_stres, hasil_bbi, hasil_bmi, hasil_tee, tinggi_lutut, lila, tekanan_darah, gula_darah, keluhan_klinis) 
                VALUES (?, CURRENT_DATE, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$p_id, $bb, $tb, $usia, $f_akt, $f_str, $bbi, $imt, $tee, $tl, $lila, $td, $gd, $klinis]);
        
        header("Location: hasil-kalkulasi.php?id=" . $conn->lastInsertId());
        exit();
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}