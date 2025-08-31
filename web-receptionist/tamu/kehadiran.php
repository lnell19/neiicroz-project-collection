<?php
include '../koneksi.php';

// Ambil tahun filter
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date("Y");

// Pencarian nama
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$sql_count = "SELECT COUNT(*) as total FROM tamu WHERE YEAR(`tanggal`) = $tahun_filter";
if (!empty($search)) {
    $sql_count .= " AND nama LIKE '%$search%'";
}
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data
$sql_data = "SELECT * FROM tamu WHERE YEAR(`tanggal`) = $tahun_filter";
if (!empty($search)) {
    $sql_data .= " AND nama LIKE '%$search%'";
}
$sql_data .= " ORDER BY `tanggal` DESC LIMIT $limit OFFSET $offset";
$result_data = mysqli_query($conn, $sql_data);

// Download CSV
if (isset($_GET['download']) && $_GET['download'] == 'png') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="kehadiran_'.$tahun_filter.'.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['No', 'Nama', 'Instansi', 'Keperluan', 'Tanggal/Waktu']);

    $no = 1;
    $sql_all = "SELECT * FROM tamu WHERE YEAR(`tanggal/waktu`) = $tahun_filter";
    if (!empty($search)) {
        $sql_all .= " AND nama LIKE '%$search%'";
    }
    $sql_all .= " ORDER BY `tanggal/waktu` DESC";
    $result_all = mysqli_query($conn, $sql_all);

    while ($row = mysqli_fetch_assoc($result_all)) {
        fputcsv($output, [
            $no++,
            $row['nama'],
            $row['instansi'],
            $row['keperluan'],
            $row['tanggal/waktu']
        ]);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Rekap Kehadiran Tamu <?= $tahun_filter ?></h2>

    <!-- Filter tahun & pencarian -->
    <form method="get" class="row mb-3">
        <div class="col-md-3">
            <label for="tahun" class="form-label">Pilih Tahun:</label>
            <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                <?php for ($tahun = 2000; $tahun <= date("Y"); $tahun++): ?>
                <option value="<?= $tahun ?>" <?= $tahun == $tahun_filter ? 'selected' : '' ?>><?= $tahun ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label for="search" class="form-label">Cari Nama:</label>
            <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari nama...">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&download=csv" class="btn btn-success w-100">
                &#128190; Download CSV
            </a>
        </div>
    </form>

    <!-- Tabel -->
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Instansi</th>
                    <th>Keperluan</th>
                    <th>Tanggal/Waktu</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result_data) > 0): ?>
                <?php $no = $offset + 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result_data)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['instansi']) ?></td>
                    <td><?= htmlspecialchars($row['keperluan']) ?></td>
                    <td><?= $row['tanggal'] ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                <a class="page-link" href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&page=<?= $p ?>">
                    <?= $p ?>
                </a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <div class="mt-4">
        <a href="../admin/dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
</div>
<br>
<br>
<br>
</body>
</html>