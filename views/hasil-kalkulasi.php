<?php 
require_once '../config/database.php'; 
require_once '../includes/functions.php'; 
include '../includes/header.php'; 

// 1. Validasi ID
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_asm = $_GET['id'];

// 2. Ambil data gabungan Assessment dan Pasien
$query = $conn->prepare("
    SELECT a.*, p.nama_pasien, p.no_rm, p.jenis_kelamin, p.tgl_lahir 
    FROM assessments a 
    JOIN patients p ON a.patient_id = p.id 
    WHERE a.id = ?
");
$query->execute([$id_asm]);
$data = $query->fetch();

if (!$data) {
    die("Data assessment tidak ditemukan!");
}

// 3. Kalkulasi Makronutrisi untuk Tampilan
$total_kalori = $data['hasil_tee'];
$protein_g = ($total_kalori * 0.15) / 4;
$lemak_g   = ($total_kalori * 0.25) / 9;
$karbo_g   = ($total_kalori * 0.60) / 4;
?>

<div class="container py-4">
    <div class="card border-0 shadow-lg mb-5">
        <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">NUTRITION CARE PROCESS (NCP) REPORT</h4>
                <small class="text-info">NutriSync Clinical Management System</small>
            </div>
            <div class="text-end d-print-none">
                <button onclick="window.print()" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
            </div>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <div class="row mb-4 bg-light p-3 rounded mx-1 border">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="35%">Nama Pasien</td><td>: <strong><?= $data['nama_pasien'] ?></strong></td></tr>
                        <tr><td>No. Rekam Medis</td><td>: <?= $data['no_rm'] ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6 text-md-end border-start">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="35%">Jenis Kelamin</td><td>: <?= $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                        <tr><td>Tanggal Periksa</td><td>: <?= date('d/m/Y', strtotime($data['tgl_periksa'])) ?></td></tr>
                    </table>
                </div>
            </div>

            <h5 class="text-primary fw-bold border-bottom pb-2 mb-4">A - ASSESSMENT GIZI</h5>
            <div class="row mb-5 g-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-white shadow-sm h-100">
                        <h6 class="small fw-bold text-muted text-uppercase mb-3">Data Antropometri</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td>Berat Badan</td><td class="fw-bold text-end"><?= $data['bb_kg'] ?> kg</td></tr>
                            <tr><td>Tinggi Badan</td><td class="fw-bold text-end"><?= number_format($data['tb_cm'], 1) ?> cm</td></tr>
                            <tr class="border-top">
                                <td>Status IMT</td>
                                <td class="text-end">
                                    <span class="badge bg-info text-dark"><?= getStatusIMT($data['hasil_bmi']) ?> (<?= number_format($data['hasil_bmi'], 1) ?>)</span>
                                </td>
                            </tr>
                            <tr><td>Berat Badan Ideal</td><td class="text-primary fw-bold text-end"><?= number_format($data['hasil_bbi'], 1) ?> kg</td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-white shadow-sm h-100">
                        <h6 class="small fw-bold text-muted text-uppercase mb-3">Data Klinis & Biokimia</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td>Tekanan Darah</td><td class="fw-bold text-end"><?= $data['tekanan_darah'] ?> mmHg</td></tr>
                            <tr><td>Gula Darah (GDS)</td><td class="fw-bold text-end"><?= $data['gula_darah'] ?> mg/dL</td></tr>
                            <tr><td>LiLA</td><td class="fw-bold text-end"><?= $data['lila'] ?> cm</td></tr>
                            <tr class="border-top">
                                <td>Keluhan</td>
                                <td class="text-end small text-danger italic"><?= $data['keluhan_klinis'] ?: 'Tidak ada' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <h5 class="text-warning fw-bold border-bottom pb-2">D - DIAGNOSIS GIZI</h5>
                <div class="p-4 bg-warning bg-opacity-10 border-start border-warning border-5 rounded mt-3 shadow-sm">
                    <div class="lh-base text-dark fs-5 italic">
                        <?= getDiagnosisGizi($data['hasil_bmi'], $data['gula_darah'], $data['tekanan_darah']) ?>
                    </div>
                </div>
            </div>

            <h5 class="text-success fw-bold border-bottom pb-2">I - INTERVENSI GIZI (Nutritional Prescription)</h5>
            <div class="row align-items-center mt-3 g-4">
                <div class="col-md-4 text-center border-end">
                    <div class="display-4 fw-bold text-success mb-0"><?= number_format($total_kalori, 0, ',', '.') ?></div>
                    <p class="fw-bold text-muted mb-2">Total Energi (kkal/hari)</p>
                    <div class="d-flex flex-wrap justify-content-center gap-1">
                        <?php 
                        $diets = explode(', ', getRekomendasiDiet($data['hasil_bmi'], $data['gula_darah'], $data['tekanan_darah']));
                        foreach($diets as $d): ?>
                            <span class="badge bg-success"><?= $d ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <h6 class="small fw-bold text-muted mb-3 text-center text-md-start">TARGET MAKRONUTRISI</h6>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="p-3 border rounded text-center bg-white shadow-sm border-top border-4 border-warning">
                                <small class="text-muted d-block fw-bold small">PROTEIN</small>
                                <span class="h5 fw-bold"><?= number_format($protein_g, 1) ?>g</span>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">(15%)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 border rounded text-center bg-white shadow-sm border-top border-4 border-danger">
                                <small class="text-muted d-block fw-bold small">LEMAK</small>
                                <span class="h5 fw-bold"><?= number_format($lemak_g, 1) ?>g</span>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">(25%)</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 border rounded text-center bg-white shadow-sm border-top border-4 border-success">
                                <small class="text-muted d-block fw-bold small">KARBOHIDRAT</small>
                                <span class="h5 fw-bold"><?= number_format($karbo_g, 1) ?>g</span>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">(60%)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center d-print-none">
                <p class="text-muted small mb-0">* Laporan ini dibuat secara otomatis oleh sistem NutriSync.</p>
                <a href="pilih-menu.php?asm_id=<?= $data['id'] ?>" class="btn btn-primary btn-lg px-4 shadow">
                    Pilih Menu Makanan <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="card-footer bg-white p-5 d-none d-print-block border-0">
            <div class="row">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    <p class="mb-5">Ahli Gizi (Dietisien),</p>
                    <br>
                    <p class="fw-bold mb-0">__________________________</p>
                    <small>Petugas NutriSync</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>