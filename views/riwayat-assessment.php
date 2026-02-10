<?php 
require_once '../config/database.php'; 
require_once '../includes/functions.php'; 
include '../includes/header.php'; 

$patient_id = $_GET['patient_id'];

// 1. Ambil data pasien
$stmt_p = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt_p->execute([$patient_id]);
$patient = $stmt_p->fetch();

// 2. Ambil semua riwayat untuk Tabel & Grafik (Urutkan dari terlama untuk grafik)
$stmt_h = $conn->prepare("SELECT * FROM assessments WHERE patient_id = ? ORDER BY tgl_periksa ASC");
$stmt_h->execute([$patient_id]);
$history = $stmt_h->fetchAll();

// 3. Siapkan data untuk Chart.js
$labels = [];
$bb_data = [];
foreach ($history as $h) {
    $labels[] = date('d M', strtotime($h['tgl_periksa']));
    $bb_data[] = $h['bb_kg'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Monitoring Gizi</h2>
            <p class="text-muted">Pasien: <strong><?= $patient['nama_pasien']; ?></strong></p>
        </div>
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold">Grafik Tren Berat Badan (kg)</div>
        <div class="card-body">
            <canvas id="weightChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-bold">Log Pemeriksaan Gizi</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>BB/TB</th>
                            <th>Status IMT</th>
                            <th>Target Energi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Balik urutan untuk tabel (terbaru di atas)
                        $history_rev = array_reverse($history);
                        foreach ($history_rev as $h): 
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($h['tgl_periksa'])); ?></td>
                            <td><?= $h['bb_kg']; ?>kg / <?= $h['tb_cm']; ?>cm</td>
                            <td>
                                <span class="badge bg-info text-dark"><?= getStatusIMT($h['hasil_bmi']); ?></span>
                            </td>
                            <td class="fw-bold text-success"><?= number_format($h['hasil_tee'], 0); ?> kkal</td>
                            <td class="text-center">
                                <a href="hasil-kalkulasi.php?id=<?= $h['id']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="cetak-menu.php?asm_id=<?= $h['id']; ?>" target="_blank" class="btn btn-sm btn-outline-success">Cetak</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('weightChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Berat Badan (kg)',
            data: <?= json_encode($bb_data); ?>,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointRadius: 5,
            pointBackgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: false }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>