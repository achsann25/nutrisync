<?php 
require_once '../config/database.php'; 
include '../includes/header.php'; 

$id_pasien = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id_pasien]);
$p = $stmt->fetch();

$tgl_lahir = new DateTime($p['tgl_lahir'] ?? '2000-01-01');
$today = new DateTime();
$usia = $today->diff($tgl_lahir)->y;
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0"><i class="bi bi-clipboard2-pulse"></i> Clinical Assessment Gizi</h3>
        <span class="badge bg-light text-dark border p-2">Pasien: <?= $p['nama_pasien'] ?> (<?= $usia ?> Thn)</span>
    </div>

    <form action="proses-assessment.php" method="POST">
        <input type="hidden" name="patient_id" value="<?= $id_pasien ?>">
        <input type="hidden" name="jk" value="<?= $p['jenis_kelamin'] ?>">
        <input type="hidden" name="usia" value="<?= $usia ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 border-top border-primary border-4 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-bounding-box me-2"></i>DOMAIN A: ANTROPOMETRI</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="bb" class="form-control form-control-lg border-primary" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="tb" class="form-control form-control-lg">
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-primary bg-opacity-10 rounded border border-primary border-dashed">
                                    <label class="form-label small fw-bold text-primary">Tinggi Lutut (cm) - <span class="text-muted">Gunakan jika pasien tidak bisa berdiri</span></label>
                                    <input type="number" step="0.1" name="tl" class="form-control" placeholder="Hanya untuk estimasi Chumlea">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Lingkar Lengan Atas / LiLA (cm)</label>
                                <input type="number" step="0.1" name="lila" class="form-control" placeholder="Untuk ukur cadangan otot">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 border-top border-danger border-4 mb-4 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-thermometer-high me-2"></i>DOMAIN B & C: KLINIS & BIOKIMIA</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Tekanan Darah (mmHg)</label>
                                <input type="text" name="td" class="form-control" placeholder="120/80">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Gula Darah (mg/dL)</label>
                                <input type="number" name="gd" class="form-control" placeholder="GDS">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Keluhan Klinis / Fisik</label>
                                <textarea name="klinis" class="form-control" rows="4" placeholder="Contoh: Mual, Muntah, Kesulitan menelan, Edema..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm border-0 border-top border-success border-4 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-lightning-charge-fill me-2"></i>DETERMINASI ENERGI (ETIOLOGI)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Faktor Aktivitas</label>
                                <select name="f_aktivitas" class="form-select form-select-lg">
                                    <option value="1.2">Bedrest / Tidak beraktivitas (1.2)</option>
                                    <option value="1.3">Aktivitas Ringan (1.3)</option>
                                    <option value="1.5">Aktivitas Sedang (1.5)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Faktor Stres (Kondisi Penyakit)</label>
                                <select name="f_stres" class="form-select form-select-lg">
                                    <option value="1.0">Normal / Tanpa Stres (1.0)</option>
                                    <option value="1.2">Infeksi / Pasca Operasi (1.2)</option>
                                    <option value="1.3">Sepsis / Kanker / Infeksi Berat (1.3)</option>
                                    <option value="1.5">Luka Bakar Berat (1.5)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid mb-5">
                    <button type="submit" name="hitung" class="btn btn-primary btn-lg py-3 fw-bold shadow">
                        <i class="bi bi-calculator-fill me-2"></i> ANALISIS NCP & HITUNG GIZI
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/header.php'; ?>