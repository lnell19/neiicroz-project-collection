<?php
include "../koneksi.php"; // koneksi ke database

// Ambil semua data tari dari tabel
$query = "SELECT * FROM tari ORDER BY id_tari DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tari Tradisional - Budaya Sunda</title>
<style>
  * {
    margin: 0; padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', Arial, sans-serif;
  }
  body {
    background-color: #f8fafc;
    color: #333;
  }

  /* Header */
  header {
    background: linear-gradient(90deg, #2563eb, #1e40af);
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    text-align: center;
    padding: 16px 0;
    letter-spacing: 1px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }

  /* Navigation Tabs */
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
    transition: all .3s ease;
    position: relative;
  }
  .nav-tab:hover {
    background: #e0ecff;
    color: #2563eb;
  }
  .nav-tab.active {
    color: #2563eb;
    font-weight: 600;
  }
  .nav-tab.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: #2563eb;
  }

  /* Container */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
  }

  .section-title {
    font-size: 1.8rem;
    color: #2563eb;
    text-align: center;
    margin-bottom: 25px;
    font-weight: 700;
  }

  /* Cards Section */
  .cards-container {
    display: flex;
    overflow-x: auto;
    gap: 24px;
    padding: 10px;
    scroll-behavior: smooth;
    scrollbar-width: none;
  }
  .cards-container::-webkit-scrollbar { display: none; }

  .card {
    flex: 0 0 300px;
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
  }
  .card:nth-child(1) { animation-delay: .1s; }
  .card:nth-child(2) { animation-delay: .2s; }
  .card:nth-child(3) { animation-delay: .3s; }
  .card:nth-child(4) { animation-delay: .4s; }

  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 20px rgba(37,99,235,0.2);
  }

  .card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .card-content {
    padding: 18px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }

  .card-title {
    font-size: 1.3rem;
    color: #1e3a8a;
    margin-bottom: 8px;
    font-weight: 600;
  }

  .card-description {
    flex-grow: 1;
    font-size: 14px;
    color: #555;
    line-height: 1.5;
    margin-bottom: 12px;
  }

  .card-link {
    text-decoration: none;
    background: #2563eb;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
    transition: background .3s;
    align-self: flex-start;
  }
  .card-link:hover {
    background: #1e3a8a;
  }

  /* Scroll Button */
  .scroll-hint {
    position: absolute;
    right: 15px;
    top: 45%;
    background-color: #2563eb;
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: transform .2s ease;
  }
  .scroll-hint:hover { transform: scale(1.1); }

  /* Description Section */
  .description-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-top: 40px;
    animation: fadeIn 1s ease;
  }
  .description-title {
    font-size: 1.5rem;
    color: #2563eb;
    margin-bottom: 15px;
    font-weight: 600;
  }
  .description-text {
    line-height: 1.7;
    color: #444;
    margin-bottom: 15px;
    text-align: justify;
  }

  /* Animations */
  @keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
</style>
</head>
<body>

<!-- Header -->
<header>Budaya Sunda</header>

<!-- Nav Tabs -->
<div class="nav-container">
  <nav class="nav-tabs">
    <a href="tari.php" class="nav-tab active">Tari Tradisional</a>
    <a href="kuliner.php" class="nav-tab">Kuliner</a>
    <a href="alatmusik.php" class="nav-tab">Alat Musik</a>
    <a href="lagu.php" class="nav-tab">Lagu</a>
    <a href="destinasi.php" class="nav-tab">Destinasi Wisata</a>
  </nav>
</div>

<!-- Container -->
<div class="container">
  <h2 class="section-title">Tari Tradisional Sunda</h2>

  <div class="scroll-container" style="position:relative;">
    <div class="scroll-hint" id="scrollBtn">→</div>
    <div class="cards-container" id="danceCards">
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php 
        $gambar = 'uploads/tari/' . ($row['gambar'] ?: 'default.png');
        if (!file_exists($gambar)) $gambar = 'uploads/tari/default.png';
      ?>
      <div class="card">
        <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
        <div class="card-content">
          <h3 class="card-title"><?= htmlspecialchars($row['nama']) ?></h3>
          <p class="card-description"><?= htmlspecialchars($row['deskripsi']) ?></p>
          <?php if (!empty($row['link_video'])): ?>
            <a href="<?= htmlspecialchars($row['link_video']) ?>" target="_blank" class="card-link">Tonton Video</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Deskripsi Umum -->
  <div class="description-section">
    <h3 class="description-title">Keunikan Tari Tradisional Sunda</h3>
    <p class="description-text">
      Tari tradisional Sunda memiliki gerakan yang lemah lembut namun kuat dalam makna. Setiap tarian menggambarkan cerita tentang kehidupan masyarakat Sunda, mulai dari keindahan alam, kesederhanaan, hingga nilai-nilai moral.
    </p>
    <p class="description-text">
      Iringan musiknya biasanya menggunakan gamelan, kendang, suling, dan rebab yang menghasilkan harmoni khas Sunda. Kini, tarian tradisional Sunda terus berkembang menjadi bentuk seni yang dapat dinikmati oleh generasi modern tanpa kehilangan jati dirinya.
    </p>
  </div>
</div>

<script>
  // Scroll panah kanan
  const scrollBtn = document.getElementById('scrollBtn');
  const container = document.getElementById('danceCards');
  scrollBtn.addEventListener('click', () => {
    container.scrollBy({ left: 320, behavior: 'smooth' });
  });

  // Hilangkan tombol saat scroll habis
  container.addEventListener('scroll', () => {
    if (container.scrollWidth - container.clientWidth <= container.scrollLeft + 10)
      scrollBtn.style.display = 'none';
    else scrollBtn.style.display = 'flex';
  });
</script>

</body>
</html>
