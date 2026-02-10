<?php
require_once '../config/database.php';
include '../includes/header.php';

$search = isset($_GET['q']) ? $_GET['q'] : '';
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Database Gizi Pangan Indonesia</h5>
    </div>
    <div class="card-body">
        <form action="" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Cari makanan (contoh: Soto, Ikan, Nasi...)" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary" type="submit">Cari Data</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Bahan / Masakan</th>
                        <th>Porsi</th>
                        <th>Energi (kkal)</th>
                        <th>Protein (g)</th>
                        <th>Karbo (g)</th>
                        <th>Lemak (g)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($search != '') {
                        $stmt = $conn->prepare("SELECT * FROM foods WHERE name LIKE ? LIMIT 50");
                        $stmt->execute(["%$search%"]);
                        while ($row = $stmt->fetch()) {
                            echo "<tr>
                                    <td class='fw-bold'>{$row['name']} <br><small class='text-muted'>{$row['manufacturer']}</small></td>
                                    <td><span class='badge bg-light text-dark border'>{$row['serving_size']}</span></td>
                                    <td class='text-success fw-bold'>{$row['energy_kcal']}</td>
                                    <td>{$row['protein_g']}</td>
                                    <td>{$row['carbohydrate_g']}</td>
                                    <td>{$row['fat_g']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted py-5'>Masukkan nama makanan untuk melihat detail gizi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>