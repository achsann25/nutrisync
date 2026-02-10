<?php 
require_once '../config/database.php'; 
include '../includes/header.php'; 

// Logika simpan data akan kita taruh di sini nanti
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registrasi Pasien Baru</h5>
            </div>
            <div class="card-body">
                <form action="proses-pasien.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekam Medis (RM)</label>
                        <input type="text" name="no_rm" class="form-control" placeholder="Contoh: RM-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="simpan" class="btn btn-success">Simpan Data Pasien</button>
                        <a href="../index.php" class="btn btn-light">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>