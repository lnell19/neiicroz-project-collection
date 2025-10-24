<?php
include '../koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data dari database
$query = mysqli_query($conn, "SELECT * FROM kuliner WHERE id_kuliner = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<h2>Data kuliner tidak ditemukan.</h2>";
    exit;
}

// Gambar utama (fallback ke default jika tidak ada)
$gambar = 'uploads/kuliner/' . ($data['gambar'] ?: 'default.png');
if (!file_exists($gambar)) $gambar = 'uploads/kuliner/default.png';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?= htmlspecialchars($data['nama']) ?> — Budaya Sunda</title>
<style>
  :root{
    --blue-600: #2563eb;
    --blue-700: #1e40af;
    --accent: #fbbf24;
    --card-bg: rgba(255,255,255,0.95);
  }

  *{box-sizing:border-box;margin:0;padding:0;font-family:Inter, "Segoe UI", Roboto, Arial, sans-serif}
  body{background:#f3f6fb;color:#222;line-height:1.5}

  /* header */
  header{
    background: linear-gradient(90deg,var(--blue-600),var(--blue-700));
    color:#fff;
    text-align:center;
    padding:16px 12px;
    font-weight:700;
    font-size:20px;
  }

  .container{
    max-width:1200px;
    margin:40px auto;
    padding:0 16px;
  }

  /* content card with blurred background (only inside .content) */
  .content{
    position:relative;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-radius:16px;
    overflow:hidden;
    background: var(--card-bg);
    box-shadow: 0 10px 30px rgba(16,24,40,0.08);
    min-height:420px; /* memberi ruang vertikal agar gambar bisa center */
  }

  /* background image for the content (blurred) */
  .content::before{
    content:"";
    position:absolute;
    inset:0;
    background: url('<?= htmlspecialchars($gambar) ?>') center/cover no-repeat;
    filter: blur(18px) brightness(0.55);
    transform: scale(1.05);
    z-index:0;
  }
  /* dark overlay agar teks mudah dibaca */
  .content::after{
    content:"";
    position:absolute;
    inset:0;
    background: rgba(0,0,0,0.28);
    z-index:0;
  }

  /* image column */
  .image-container{
    position:relative;
    z-index:2; /* tampilkan di atas pseudo background */
    padding:32px;
    display:flex;
    align-items:center;      /* penting: vertical center */
    justify-content:center;  /* horizontal center */
    background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
    min-height:420px;
  }

  /* foto utama: jangan set fixed height terlalu besar, pakai auto untuk proporsional */
  .food-image{
    display:block;
    width:100%;
    max-width:520px;   /* batasi lebar gambar */
    height:auto;
    aspect-ratio: 4/3; /* preferensi rasio, browser modern akan menyesuaikan */
    object-fit:cover;
    border-radius:18px;
    border:6px solid white;
    box-shadow: 0 18px 35px rgba(0,0,0,0.25);
    transition: transform .35s ease, box-shadow .35s ease;
    cursor: zoom-in;
  }
  .food-image:hover{ transform: scale(1.03); box-shadow: 0 24px 48px rgba(0,0,0,0.32) }

  /* detail column */
  .detail-container{
    position:relative;
    z-index:2;
    padding:42px 48px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:18px;
    color:#fff;
    background: linear-gradient(180deg, rgba(37,99,235,0.95), rgba(30,64,175,0.95));
  }

  .detail-title{
    font-size:2.1rem;
    font-weight:800;
    text-align:center; /* kalau mau center */
    margin-bottom:6px;
  }

  .meta{
    color: rgba(255,255,255,0.9);
    text-align:center;
    font-size:0.95rem;
    margin-bottom:8px;
  }

  .section{
    background:transparent;
    padding:0;
  }
  .section h4{
    color: var(--accent);
    font-size:1.05rem;
    margin-bottom:8px;
    font-weight:700;
  }
  .section p{
    color: rgba(255,255,255,0.95);
    font-size:0.98rem;
    text-align:justify;
    white-space:pre-wrap;
  }

  .button-row{
    margin-top:8px;
    display:flex;
    gap:10px;
    justify-content:center;
    flex-wrap:wrap;
  }
  .btn{
    display:inline-block;
    padding:10px 18px;
    border-radius:10px;
    background:#fff;
    color:var(--blue-700);
    font-weight:700;
    text-decoration:none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    transition: transform .15s ease;
  }
  .btn:hover{ transform: translateY(-3px) }

  /* zoom modal */
  .zoom-modal{ display:none; position:fixed; inset:0; background: rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; padding:20px }
  .zoom-modal img{ max-width:98%; max-height:92%; border-radius:10px; box-shadow: 0 18px 40px rgba(0,0,0,0.6) }

  /* responsive: stack columns on small screens */
  @media (max-width: 920px){
    .content{ grid-template-columns: 1fr; min-height:auto; }
    .image-container{ padding:22px; }
    .detail-container{ padding:24px; }
    .detail-title{ font-size:1.6rem; text-align:left }
    .meta{ text-align:left }
    .button-row{ justify-content:flex-start }
  }
</style>
</head>
<body>

<header>Budaya Sunda</header>

<div class="container">
  <div class="content" role="region" aria-label="Detail kuliner">
    <!-- left: image (centered vertically) -->
    <div class="image-container">
      <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= htmlspecialchars($data['nama']) ?>" class="food-image zoomable">
    </div>

    <!-- right: details -->
    <div class="detail-container" aria-live="polite">
      <div>
        <div class="detail-title"><?= htmlspecialchars($data['nama']) ?></div>
        <div class="meta"><?= !empty($data['asal_usul']) ? htmlspecialchars($data['asal_usul']) : 'Asal tidak tersedia' ?></div>
      </div>

      <div class="section">
        <h4>Bahan-bahan</h4>
        <p><?= nl2br(htmlspecialchars($data['bahan'] ?: 'Belum ada data bahan.')) ?></p>
      </div>

      <div class="section">
        <h4>Cara Pembuatan</h4>
        <p><?= nl2br(htmlspecialchars($data['cara_pembuatan'] ?: 'Belum ada cara pembuatan.')) ?></p>
      </div>

      <div class="section">
        <h4>Cara Penyajian</h4>
        <p><?= nl2br(htmlspecialchars($data['cara_penyajian'] ?: '-')) ?></p>
      </div>

      <div class="button-row">
        <a class="btn" href="kuliner.php">⬅ Kembali</a>
        <?php if (!empty($data['link_video'])): ?>
          <a class="btn" href="<?= htmlspecialchars($data['link_video']) ?>" target="_blank" rel="noopener">🎥 Lihat Video</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Zoom modal -->
<div class="zoom-modal" id="zoomModal" role="dialog" aria-hidden="true">
  <img id="zoomImg" src="" alt="Gambar besar">
</div>

<script>
  // zoom image modal
  const zoomables = document.querySelectorAll('.zoomable');
  const modal = document.getElementById('zoomModal');
  const zoomImg = document.getElementById('zoomImg');

  zoomables.forEach(img=>{
    img.addEventListener('click', ()=>{
      zoomImg.src = img.src;
      modal.style.display = 'flex';
      modal.setAttribute('aria-hidden','false');
    });
  });

  modal.addEventListener('click', ()=> {
    modal.style.display='none';
    modal.setAttribute('aria-hidden','true');
  });

  document.addEventListener('keydown', (e)=>{
    if(e.key==='Escape') { modal.style.display='none'; modal.setAttribute('aria-hidden','true'); }
  });
</script>

</body>
</html>
