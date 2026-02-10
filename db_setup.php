<?php
require_once 'config/database.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_pasien VARCHAR(255) NOT NULL,
        no_rm VARCHAR(50) NOT NULL,
        jenis_kelamin ENUM('L', 'P') NOT NULL,
        tgl_lahir DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS assessments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        bb_kg FLOAT,
        tb_cm FLOAT,
        lila FLOAT DEFAULT 0,
        tekanan_darah VARCHAR(20),
        gula_darah INT,
        keluhan_klinis TEXT,
        hasil_bmi FLOAT,
        hasil_bbi FLOAT,
        hasil_tee FLOAT,
        tgl_periksa DATE,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
    );
    ";

    $conn->exec($sql);
    echo "<h1 style='color:green'>BERHASIL! Tabel sudah dibuat di Railway.</h1>";
    echo "<p>Silakan hapus file ini demi keamanan, lalu cek aplikasi kamu.</p>";

} catch(PDOException $e) {
    echo "<h1 style='color:red'>GAGAL:</h1> " . $e->getMessage();
}
?>