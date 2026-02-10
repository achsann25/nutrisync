<?php 
require_once 'config/database.php'; 
include 'includes/header.php'; 

// Mengambil total pasien dan total assessment untuk statistik dashboard
$total_pasien = $conn->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$total_assessment = $conn->query("SELECT COUNT(*) FROM assessments")->fetchColumn();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">NutriSync Dashboard</h2>
                    <p class="mb-0 opacity-75">Sistem Informasi Asuhan Gizi Terstandar & Monitoring Dietisien</p>
                </div>
                <div class="d-none d-md-block">
                    <i class="bi bi-hospital fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Total Pasien</h6>
                <h3 class="fw-bold mb-0"><?= $total_pasien; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Total Pemeriksaan</h6>
                <h3 class="fw-bold mb-0"><?= $total_assessment; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-md-end d-flex align-items-center justify-content-md-end">
        <a href="views/tambah-pasien.php" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill"></i> Registrasi Pasien Baru
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">Daftar Pasien Rawat Inap / Jalan</h5>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Cari No. RM atau Nama...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. RM</th>
                                <th>Nama Lengkap Pasien</th>
                                <th>Gender</th>
                                <th class="text-center">Aksi Intervensi Gizi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT * FROM patients ORDER BY id DESC");
                            if ($stmt->rowCount() > 0):
                                while ($row = $stmt->fetch()):
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-secondary font-monospace"><?= $row['no_rm']; ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $row['nama_pasien']; ?></div>
                                        <small class="text-muted">Terdaftar: <?= date('d/m/Y', strtotime($row['created_at'] ?? 'now')); ?></small>
                                    </td>
                                    <td>
                                        <?php if($row['jenis_kelamin'] == 'L'): ?>
                                            <span class="text-primary"><i class="bi bi-gender-male"></i> Laki-laki</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="bi bi-gender-female"></i> Perempuan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="views/assessment.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-success px-3">
                                                <i class="bi bi-calculator"></i> Hitung Gizi
                                            </a>
                                            <a href="views/riwayat-assessment.php?patient_id=<?= $row['id']; ?>" class="btn btn-sm btn-info text-white px-3">
                                                <i class="bi bi-clock-history"></i> Riwayat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                        Belum ada data pasien terdaftar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 d-print-none">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="bi bi-database-check fs-2 text-primary"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 fw-bold">Database Bahan Pangan</h6>
                    <small class="text-muted">Tersedia 1.600+ referensi nilai gizi siap digunakan.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="bi bi-shield-check fs-2 text-success"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 fw-bold">Standar ADIME/NCP</h6>
                    <small class="text-muted">Proses asuhan gizi terstandar sesuai aturan Kemenkes.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>