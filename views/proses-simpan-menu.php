<?php
// views/proses-simpan-menu.php

require_once '../config/database.php';

// Pastikan data dikirim melalui metode GET (sesuai dengan link/form di pilih-menu.php)
if (isset($_GET['asm_id']) && isset($_GET['food_id']) && isset($_GET['waktu'])) {
    
    // 1. Tangkap data dari URL
    $asm_id  = $_GET['asm_id'];
    $food_id = $_GET['food_id'];
    $waktu   = $_GET['waktu'];
    $qty     = 1; // Default porsi adalah 1, bisa dikembangkan nanti jika ingin dinamis

    try {
        // 2. Persiapkan query SQL untuk menyimpan pilihan menu
        // Tabel: patient_meals (assessment_id, waktu_makan, food_id, qty)
        $sql = "INSERT INTO patient_meals (assessment_id, waktu_makan, food_id, qty) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        // 3. Eksekusi query
        $stmt->execute([
            $asm_id, 
            $waktu, 
            $food_id, 
            $qty
        ]);

        // 4. Redirect kembali ke halaman pilih-menu dengan ID assessment yang sama
        header("Location: pilih-menu.php?asm_id=" . $asm_id . "&status=success");
        exit();

    } catch (PDOException $e) {
        // Jika terjadi error pada database
        die("Gagal menyimpan menu ke database: " . $e->getMessage());
    }

} else {
    // Jika file diakses secara ilegal tanpa parameter yang diperlukan
    header("Location: ../index.php");
    exit();
}