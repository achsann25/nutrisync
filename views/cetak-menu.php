<?php 
require_once '../config/database.php'; 
include '../includes/header.php'; 

$asm_id = $_GET['asm_id'];

// Ambil data assessment & pasien
$stmt = $conn->prepare("SELECT a.*, p.nama_pasien, p.no_rm FROM assessments a JOIN patients p ON a.patient_id = p.id WHERE a.id = ?");
$stmt->execute([$asm_id]);
$asm = $stmt->fetch();

// Ambil daftar menu
$stmt_list = $conn->prepare("
    SELECT m.*, f.name, f.energy_kcal, f.protein_g, f.carbohydrate_g, f.fat_g 
    FROM patient_meals m 
    JOIN foods f ON m.food_id = f.id 
    WHERE m.assessment_id = ?
    ORDER BY FIELD(waktu_makan, 'Pagi', 'Selingan Pagi', 'Siang', 'Selingan Sore', 'Malam')
");
$stmt_list->execute([$asm_id]);
$menus = $stmt_list->fetchAll();
?>

<style>
    @media print {
        .d-print-none { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        body { background: white; }
    }
    .table-print { border: 2px solid #000; }
    .table-print th { background-color: #f8f9fa !important; border-bottom: 2px solid #000; }
</style>

<div class="container mt-3">
    <div class="d-print-none mb-4">
        <button onclick="window.print()" class="btn btn-dark"><i class="bi bi-printer"></i> Print Sekarang</button>
        <a href="pilih-menu.php?asm_id=<?= $asm_id ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-0">RENCANA DIET PASIEN (DIETETICS CARE)</h3>
            <p class="text-muted">Instalasi Gizi NutriSync Hospital</p>
            <hr style="border: 2px solid #000;">
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-borderless table-sm">
                    <tr><td>Nama Pasien</td><td>: <strong><?= $asm['nama_pasien'] ?></strong></td></tr>
                    <tr><td>No. RM</td><td>: <?= $asm['no_rm'] ?></td></tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <table class="table table-borderless table-sm">
                    <tr><td>Target Energi</td><td>: <strong><?= number_format($asm['hasil_tee'], 0) ?> kkal</strong></td></tr>
                    <tr><td>Tanggal</td><td>: <?= date('d/m/Y') ?></td></tr>
                </table>
            </div>
        </div>

        <table class="table table-bordered table-print align-middle">
            <thead>
                <tr class="text-center">
                    <th>Waktu Makan</th>
                    <th>Menu Makanan</th>
                    <th>Energi (kkal)</th>
                    <th>P/L/K (gram)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_kkal = 0;
                foreach($menus as $m): 
                    $total_kkal += $m['energy_kcal'];
                ?>
                <tr>
                    <td class="fw-bold text-center"><?= $m['waktu_makan'] ?></td>
                    <td><?= $m['name'] ?></td>
                    <td class="text-center"><?= number_format($m['energy_kcal'], 0) ?></td>
                    <td class="text-center small">
                        P: <?= $m['protein_g'] ?>g | L: <?= $m['fat_g'] ?>g | K: <?= $m['carbohydrate_g'] ?>g
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="2" class="text-end">Total Asupan Menu:</td>
                    <td class="text-center"><?= number_format($total_kkal, 0) ?> kkal</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5">
            <div class="col-8">
                <p class="small">* Catatan: Harap dikonsumsi sesuai dengan jam makan yang ditentukan untuk menjaga kestabilan metabolisme.</p>
            </div>
            <div class="col-4 text-center">
                <p>Ahli Gizi,</p>
                <br><br>
                <p><strong>( ________________ )</strong></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>