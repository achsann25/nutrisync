<?php
require_once '../config/database.php';

if (isset($_POST['simpan'])) {
    $no_rm = $_POST['no_rm'];
    $nama = $_POST['nama_pasien'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jk = $_POST['jenis_kelamin'];

    try {
        $sql = "INSERT INTO patients (no_rm, nama_pasien, tgl_lahir, jenis_kelamin) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$no_rm, $nama, $tgl_lahir, $jk]);

        // Jika berhasil, arahkan ke halaman utama dengan pesan sukses
        header("Location: ../index.php?status=sukses");
    } catch (PDOException $e) {
        // Jika gagal (misal No RM duplikat)
        echo "Error: " . $e->getMessage();
    }
}