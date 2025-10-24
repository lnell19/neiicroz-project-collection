<?php
include '../koneksi.php';

// Ambil ID alat musik dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data alat musik
$query = mysqli_query($conn, "SELECT * FROM alatmusik WHERE id_alatmusik = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<h2>Data tidak ditemukan.</h2>";
    exit;
}

// Cek gambar
$gambar = 'uploads/alatmusik/' . ($data['gambar'] ?: 'default.png');
if (!file_exists($gambar)) $gambar = 'uploads/alatmusik/default.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($data['nama']); ?> - Budaya Sunda</title>
<style>
:root {
  --blue-600: #2563eb;
  --blue-700: #1e40af;
  --accent: #facc15;
}
* {
  margin: 0; padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', Arial, sans-serif;
}
body {
  background: #f3f6fb;
  color: #333;
}

/* Header */
header {
  background: linear-gradient(90deg, var(--blue-600), var(--blue-700));
  color: white;
  text-align: center;
  padding: 16px 0;
  font-size: 22px;
  font-weight: 700;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Nav Tabs */
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
  color: var(--blue-600);
}
.nav-tab.active {
  color: var(--blue-600);
  font-weight: 600;
}
.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: var(--blue-600);
}

/* Main Content */
.container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 20px;
}
.content {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  min-height: 430px;
}
.content::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url('<?= htmlspecialchars($gambar) ?>') center/cover no-repeat;
  filter: blur(18px) brightness(0.6);
  transform: scale(1.1);
  z-index: 0;
}
.content::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.35);
  z-index: 0;
}
.image-container, .detail-container {
  position: relative;
  z-index: 2;
}
.image-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 30px;
  background: rgba(255,255,255,0.05);
}
.food-image {
  width: 90%;
  max-width: 480px;
  height: auto;
  border-radius: 18px;
  border: 6px solid white;
  box-shadow: 0 20px 40px rgba(0,0,0,0.25);
  cursor: zoom-in;
  transition: transform .3s ease, box-shadow .3s ease;
}
.food-image:hover {
  transform: scale(1.03);
  box-shadow: 0 24px 48px rgba(0,0,0,0.35);
}

.detail-container {
  padding: 40px;
  background: linear-gradient(180deg, rgba(37,99,235,0.95), rgba(30,64,175,0.9));
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 15px;
}
.detail-title {
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 10px;
}
.detail-section h3 {
  color: var(--accent);
  margin-bottom: 5px;
  font-size: 1.1rem;
}
.detail-section p {
  line-height: 1.6;
  text-align: justify;
  font-size: 0.95rem;
  color: rgba(255,255,255,0.95);
}
.button-group {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}
.btn {
  background: white;
  color: var(--blue-700);
  padding: 10px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
.btn:hover {
  transform: translateY(-2px);
  background: var(--accent);
  color: #000;
}

/* Zoom modal */
.zoom-modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.85);
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
.zoom-modal img {
  max-width: 95%;
  max-height: 90%;
  border-radius: 10px;
  box-shadow: 0 0 40px rgba(0,0,0,0.6);
}

/* Responsive */
@media (max-width: 900px) {
  .content {
    grid-template-columns: 1fr;
  }
  .detail-container {
    padding: 25px;
  }
}
</style>
</head>
<body>

<header>Budaya Sunda</header>

<div class="nav-container">
  <nav class="nav-tabs">
    <a href="tari.php" class="nav-tab">Tarian Tradisional</a>
    <a href="kuliner.php" class="nav-tab">Kuliner</a>
    <a href="alatmusik.php" class="nav-tab active">Alat Musik</a>
    <a href="lagu.php" class="nav-tab">Lagu</a>
    <a href="destinasi.php" class="nav-tab">Destinasi Wisata</a>
  </nav>
</div>

<div class="container">
  <div class="content">
    <div class="image-container">
      <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= htmlspecialchars($data['nama']) ?>" class="food-image zoomable">
    </div>
    <div class="detail-container">
      <h2 class="detail-title"><?= htmlspecialchars($data['nama']) ?></h2>

      <div class="detail-section">
        <h3>Asal Usul</h3>
        <p><?= nl2br(htmlspecialchars($data['asal_usul2'] ?? 'Tidak tersedia.')) ?></p>
      </div>

      <div class="detail-section">
        <h3>Cara Pembuatan</h3>
        <p><?= nl2br(htmlspecialchars($data['cara_pembuatan'] ?? 'Tidak tersedia.')) ?></p>
      </div>

      <div class="detail-section">
        <h3>Cara Penggunaan</h3>
        <p><?= nl2br(htmlspecialchars($data['cara_permainan'] ?? 'Tidak tersedia.')) ?></p>
      </div>

      <div class="button-group">
        <a href="alatmusik.php" class="btn">⬅ Kembali</a>
        <?php if (!empty($data['link_video'])): ?>
          <a href="<?= htmlspecialchars($data['link_video']) ?>" target="_blank" class="btn">🎵 Lihat Video</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Zoom Modal -->
<div class="zoom-modal" id="zoomModal">
  <img id="zoomImg" src="">
</div>

<script>
const zoomables = document.querySelectorAll('.zoomable');
const modal = document.getElementById('zoomModal');
const zoomImg = document.getElementById('zoomImg');

zoomables.forEach(img => {
  img.addEventListener('click', () => {
    zoomImg.src = img.src;
    modal.style.display = 'flex';
  });
});
modal.addEventListener('click', () => modal.style.display = 'none');
document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.style.display = 'none'; });
</script>

</body>
</html>
