<?php
session_start();
include "../koneksi.php";

// --- Tahun sekarang ---
$tahun_sekarang = date("Y");

// --- Ambil filter tahun ---
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : $tahun_sekarang;

// --- Ambil data tamu terakhir ---
$tamu_terakhir = mysqli_query($conn, "SELECT * FROM tamu ORDER BY tanggal DESC LIMIT 1");
$tamu = mysqli_fetch_assoc($tamu_terakhir);

// --- Ambil data grafik per bulan ---
$grafik = mysqli_query($conn, "
    SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah
    FROM tamu
    WHERE YEAR(tanggal) = '$tahun_filter'
    GROUP BY MONTH(tanggal)
    ORDER BY MONTH(tanggal)
");

$data_grafik = [];
while ($row = mysqli_fetch_assoc($grafik)) {
    $data_grafik[] = $row;
}

// Nama bulan
$nama_bulan = [
    1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
    5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
    9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tamu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: auto; }
        h2, h5 { text-align: center; margin-bottom: 20px; }
        .card { border-radius: 12px; box-shadow: 0px 2px 6px rgba(0,0,0,0.1); }
        .btn-custom { border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">

    <h2>Laporan Tamu</h2>

    <!-- Filter Tahun -->
    <div class="text-center mb-4">
        <form method="GET" action="" class="d-inline-block">
            <div class="input-group">
                <label class="input-group-text bg-primary text-white fw-bold" for="tahun">Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php for ($t = 2019; $t <= $tahun_sekarang; $t++): ?>
                        <option value="<?= $t ?>" <?= ($t == $tahun_filter) ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Tamu Terakhir -->
    <h5 class="fw-bold">👤 Tamu Terakhir Datang</h5>
    <?php if ($tamu): ?>
        <p><strong>Nama:</strong> <?= $tamu['nama'] ?></p>
        <p><strong>Tanggal:</strong> <?= $tamu['tanggal'] ?></p>
    <?php else: ?>
        <p class="text-muted">Belum ada tamu.</p>
    <?php endif; ?>

    <!-- Grafik -->
    <div class="card p-3 mb-4">
        <h5 class="fw-bold">📊 Grafik Tamu per Bulan Tahun <?= $tahun_filter ?></h5>
        <canvas id="grafikTamu"></canvas>
    </div>

    <!-- Tombol Aksi -->
    <div class="text-center">
        <a href="../admin/dashboard.php" class="btn btn-secondary btn-custom me-2">⬅ Kembali</a>
        <button class="btn btn-success btn-custom" onclick="downloadChart()">⬇ Download Grafik</button>
    </div>
</div>

<script>
    const dataGrafik = <?= json_encode($data_grafik) ?>;
    const namaBulan = <?= json_encode($nama_bulan) ?>;

    const labels = [];
    const dataJumlah = [];

    for (let i = 1; i <= 12; i++) {
        labels.push(namaBulan[i]);
        const found = dataGrafik.find(item => item.bulan == i);
        dataJumlah.push(found ? parseInt(found.jumlah) : 0);
    }

    const ctx = document.getElementById('grafikTamu').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Tamu',
                data: dataJumlah,
                backgroundColor: 'rgba(0,123,255,0.7)',
                borderColor: '#0056b3',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true }}
        }
    });

    // Fungsi download grafik sebagai PNG
    function downloadChart() {
        const link = document.createElement('a');
        link.href = chart.toBase64Image();
        link.download = "laporan_tamu_<?= $tahun_filter ?>.png";
        link.click();
    }
</script>
</body>
</html>