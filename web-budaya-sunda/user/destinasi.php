<?php
include "../koneksi.php"; // koneksi ke database

// Ambil data destinasi dari database
$query = "SELECT * FROM destinasi ORDER BY id_destinasi ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Destinasi Wisata - Budaya Sunda</title>
  <style>
    :root {
      --blue-600: #2563eb;
      --blue-700: #1e40af;
      --accent: #facc15;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', Arial, sans-serif;
    }

    body {
      background-color: #f8fafc;
      color: #333;
    }

    header {
      background: linear-gradient(90deg, var(--blue-600), var(--blue-700));
      color: white;
      text-align: center;
      font-size: 22px;
      font-weight: 700;
      padding: 16px 0;
      letter-spacing: 1px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .nav-container {
      background: #f1f5f9;
      border-bottom: 1px solid #e2e8f0;
    }

    .nav-tabs {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
    }

    .nav-tab {
      padding: 14px 25px;
      color: #334155;
      text-decoration: none;
      font-weight: 500;
      transition: all .3s;
      position: relative;
    }

    .nav-tab:hover { background: #e0ecff; color: var(--blue-600); }
    .nav-tab.active { color: var(--blue-600); font-weight: 600; }
    .nav-tab.active::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 100%; height: 3px;
      background: var(--blue-600);
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .section-title {
      text-align: center;
      color: var(--blue-600);
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .destinations {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
    }

    .card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 18px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeUp 0.6s ease forwards;
      opacity: 0;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 24px rgba(37,99,235,0.2);
    }

    .card img {
      width: 100%;
      height: 190px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover img { transform: scale(1.05); }

    .card-content {
      padding: 18px 20px;
    }

    .card-content h3 {
      font-size: 1.3rem;
      font-weight: 600;
      color: var(--blue-700);
      margin-bottom: 8px;
    }

    .card-content p {
      font-size: 0.95rem;
      line-height: 1.5;
      color: #444;
      text-align: justify;
      margin-bottom: 10px;
    }

    .card-content .lokasi a {
      color: var(--blue-600);
      font-weight: 600;
      text-decoration: none;
    }

    .card-content .lokasi a:hover { text-decoration: underline; }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<header>Budaya Sunda</header>

<div class="nav-container">
  <nav class="nav-tabs">
    <a href="tari.php" class="nav-tab">Tarian Tradisional</a>
    <a href="kuliner.php" class="nav-tab">Kuliner</a>
    <a href="alatmusik.php" class="nav-tab">Alat Musik</a>
    <a href="lagu.php" class="nav-tab">Lagu Daerah</a>
    <a href="destinasi.php" class="nav-tab active">Destinasi Wisata</a>
  </nav>
</div>

<div class="container">
  <h2 class="section-title">Destinasi Wisata Sunda</h2>

  <div class="destinations">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php
        $gambar = 'uploads/destinasi/' . ($row['gambar'] ?: 'default.png');
        if (!file_exists($gambar)) $gambar = 'uploads/destinasi/default.png';
      ?>
      <div class="card">
        <img src="<?= htmlspecialchars($gambar); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
        <div class="card-content">
          <h3><?= htmlspecialchars($row['nama']); ?></h3>
          <p><?= htmlspecialchars($row['deskripsi']); ?></p>
          <?php if (!empty($row['link__maps'])): ?>
            <div class="lokasi">
              <a href="<?= htmlspecialchars($row['link__maps']); ?>" target="_blank"><?= htmlspecialchars($row['lokasi']); ?></a>
            </div>
          <?php else: ?>
            <div class="lokasi"><?= htmlspecialchars($row['lokasi']); ?></div>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</body>
</html>
