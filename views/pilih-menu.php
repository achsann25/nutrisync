<?php 
require_once '../config/database.php'; 
include '../includes/header.php'; 

if (!isset($_GET['asm_id'])) {
    header("Location: ../index.php");
    exit();
}

$asm_id = $_GET['asm_id'];

// 1. Ambil data target kalori dari assessment & nama pasien
$stmt = $conn->prepare("SELECT a.*, p.nama_pasien FROM assessments a JOIN patients p ON a.patient_id = p.id WHERE a.id = ?");
$stmt->execute([$asm_id]);
$asm = $stmt->fetch();

if (!$asm) {
    die("Data assessment tidak ditemukan!");
}

// 2. Hitung total kalori yang sudah dipilih (akumulasi dari dataset CSV)
$stmt_sum = $conn->prepare("
    SELECT SUM(f.energy_kcal * m.qty) as total 
    FROM patient_meals m 
    JOIN foods f ON m.food_id = f.id 
    WHERE m.assessment_id = ?
");
$stmt_sum->execute([$asm_id]);
$terpakai = $stmt_sum->fetch()['total'] ?? 0;
$target = $asm['hasil_tee'];
$sisa = $target - $terpakai;
$persen = ($target > 0) ? ($terpakai / $target) * 100 : 0;
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-speedometer2"></i> Status Energi Pasien
            </div>
            <div class="card-body">
                <h6 class="text-muted small mb-1">Nama Pasien:</h6>
                <h5 class="fw-bold mb-3"><?= htmlspecialchars($asm['nama_pasien']) ?></h5>
                
                <hr>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">Progres Target</span>
                        <span class="small fw-bold"><?= number_format($persen, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar <?= $persen > 100 ? 'bg-danger' : 'bg-success' ?> progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: <?= min($persen, 100) ?>%"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Target Harian:</span>
                        <span class="fw-bold text-primary"><?= number_format($target, 0, ',', '.') ?> kkal</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Terpilih:</span>
                        <span class="fw-bold text-success"><?= number_format($terpakai, 0, ',', '.') ?> kkal</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Sisa Kuota:</span>
                        <span class="fw-bold <?= $sisa < 0 ? 'text-danger' : 'text-dark' ?>">
                            <?= number_format($sisa, 0, ',', '.') ?> kkal
                        </span>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="cetak-menu.php?asm_id=<?= $asm_id ?>" target="_blank" class="btn btn-success">
                        <i class="bi bi-printer"></i> Cetak Menu Final
                    </a>
                    <a href="hasil-kalkulasi.php?id=<?= $asm_id ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali ke Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-plus-circle"></i> Tambah Menu dari Database
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">Cari Makanan (Dataset 1.600+ Item)</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="keyword" class="form-control" placeholder="Ketik nama makanan atau masakan...">
                    </div>
                    <div id="searchResults" class="mt-2" style="max-height: 350px; overflow-y: auto;"></div>
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Jadwal Makan Pasien:</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Waktu</th>
                                <th>Menu Makanan</th>
                                <th>Energi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query list menu yang sudah disimpan
                            $stmt_list = $conn->prepare("
                                SELECT m.*, f.name, f.energy_kcal 
                                FROM patient_meals m 
                                JOIN foods f ON m.food_id = f.id 
                                WHERE m.assessment_id = ?
                                ORDER BY FIELD(waktu_makan, 'Pagi', 'Selingan Pagi', 'Siang', 'Selingan Sore', 'Malam')
                            ");
                            $stmt_list->execute([$asm_id]);
                            $rows = $stmt_list->fetchAll();
                            
                            if (count($rows) > 0):
                                foreach($rows as $m): ?>
                                    <tr>
                                        <td class="text-center"><span class="badge bg-secondary"><?= $m['waktu_makan'] ?></span></td>
                                        <td><?= htmlspecialchars($m['name']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($m['energy_kcal'] * $m['qty'], 0) ?> kkal</td>
                                        <td class="text-center">
                                            <a href="hapus-menu.php?id=<?= $m['id'] ?>&asm_id=<?= $asm_id ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Hapus menu ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada menu yang disusun. Silakan cari makanan di atas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('keyword').addEventListener('input', function() {
    let q = this.value;
    if(q.length < 3) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }
    
    fetch('ajax-search-food.php?q=' + q)
        .then(res => res.json())
        .then(data => {
            let html = '<div class="list-group shadow-sm">';
            if(data.length === 0) {
                html += '<div class="list-group-item text-muted">Data makanan tidak ditemukan.</div>';
            }
            data.forEach(item => {
                html += `
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectFood(${item.id}, '${item.name.replace(/'/g, "\\'")}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>${item.name}</strong> <br><small class="text-muted">Porsi: ${item.serving_size}</small></span>
                            <span class="badge bg-primary rounded-pill">${item.energy_kcal} kkal</span>
                        </div>
                    </button>`;
            });
            html += '</div>';
            document.getElementById('searchResults').innerHTML = html;
        });
});

function selectFood(id, name) {
    let waktuHtml = `
        <div class="card p-3 mt-2 border-primary bg-primary bg-opacity-10 shadow-sm">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-clock"></i> Tentukan Waktu Makan: <br><span class="text-dark">${name}</span></h6>
            <form action="proses-simpan-menu.php" method="GET">
                <input type="hidden" name="asm_id" value="<?= $asm_id ?>">
                <input type="hidden" name="food_id" value="${id}">
                <div class="row g-2">
                    <div class="col-md-8">
                        <select name="waktu" class="form-select border-primary" required>
                            <option value="Pagi">Pagi</option>
                            <option value="Selingan Pagi">Selingan Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Selingan Sore">Selingan Sore</option>
                            <option value="Malam">Malam</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    `;
    document.getElementById('searchResults').innerHTML = waktuHtml;
}
</script>

<?php include '../includes/footer.php'; ?>