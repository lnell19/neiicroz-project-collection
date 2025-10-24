<?php
include '../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Budaya Sunda</title>
<style>
  :root {
    --biru: #265FD9;
    --biru-gelap: #1b4bb8;
    --abu: #f8f9fb;
    --teks: #333;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Poppins', Arial, sans-serif;
    background: var(--abu);
    color: var(--teks);
    overflow-x: hidden;
  }

  /* Header */
  header {
    background: linear-gradient(90deg, var(--biru), var(--biru-gelap));
    color: white;
    padding: 16px 24px;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 10;
  }

  /* Hero Section */
  .hero {
    position: relative;
    text-align: center;
    overflow: hidden;
  }

  .hero img {
    width: 100%;
    height: 360px;
    object-fit: cover;
    filter: brightness(75%);
    animation: fadeIn 2s ease-out;
  }

  .hero h1 {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 46px;
    font-weight: 700;
    text-shadow: 0 4px 12px rgba(0,0,0,0.4);
    opacity: 0;
    animation: slideUp 1.2s ease-out forwards;
    animation-delay: .3s;
  }

  /* Kategori */
  .kategori {
    padding: 40px 20px 20px;
    text-align: center;
    animation: fadeIn 1s ease-in;
  }

  .kategori h2 {
    font-size: 26px;
    color: var(--biru);
    font-weight: 700;
    margin-bottom: 20px;
  }

  .kategori a {
    display: inline-block;
    background: white;
    border: 2px solid var(--biru);
    color: var(--biru);
    padding: 10px 18px;
    margin: 8px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .kategori a:hover {
    background: var(--biru);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(38,95,217,0.3);
  }

  /* Budaya Populer */
  .budaya {
    padding: 40px 20px;
    background: white;
    border-radius: 20px 20px 0 0;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.06);
    animation: fadeIn 1.2s ease;
  }

  .budaya h2 {
    text-align: center;
    font-size: 26px;
    font-weight: 700;
    color: var(--biru);
    margin-bottom: 28px;
  }

  .budaya-list {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px;
  }

  .budaya-item {
    background: #fdfdfd;
    border-radius: 14px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    padding: 18px;
    text-align: center;
    width: 240px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeSlide 0.8s ease forwards;
  }

  .budaya-item:nth-child(1) { animation-delay: 0.3s; }
  .budaya-item:nth-child(2) { animation-delay: 0.5s; }
  .budaya-item:nth-child(3) { animation-delay: 0.7s; }

  .budaya-item:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 12px 25px rgba(38,95,217,0.25);
  }

  .budaya-item img {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid var(--biru);
    margin-bottom: 12px;
    transition: transform 0.3s ease;
  }

  .budaya-item:hover img {
    transform: scale(1.1);
  }

  .budaya-item h3 {
    font-size: 18px;
    color: #222;
    margin-bottom: 8px;
  }

  .budaya-item p {
    font-size: 14px;
    color: #555;
  }

  /* Footer */
  footer {
    background: var(--biru-gelap);
    color: #f0f0f0;
    text-align: center;
    padding: 18px 10px;
    font-size: 14px;
    margin-top: 40px;
    letter-spacing: .3px;
    box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
  }

  /* Animations */
  @keyframes fadeIn {
    from { opacity: 0; } to { opacity: 1; }
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translate(-50%, -40%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
  }

  @keyframes fadeSlide {
    to { opacity: 1; transform: translateY(0); }
  }
</style>
</head>
<body>

<header>Budaya Sunda</header>

<!-- Hero -->
<div class="hero">
  <img src="rumah sunda.webp" alt="Rumah Adat Sunda">
  <h1>Warisan Budaya Sunda</h1>
</div>

<!-- Kategori -->
<section class="kategori">
  <h2>Eksplorasi Kategori</h2>
  <a href="tari.php">Tari Tradisional</a>
  <a href="kuliner.php">Kuliner</a>
  <a href="lagu.php">Lagu Tradisional</a>
  <a href="alatmusik.php">Alat Musik</a>
  <a href="destinasi.php">Destinasi Wisata</a>
</section>

<!-- Budaya Populer -->
<section class="budaya">
  <h2>Budaya Populer Sunda</h2>
  <div class="budaya-list">
    <div class="budaya-item">
      <img src="images/jaipong.jpg" alt="Tari Jaipongan">
      <h3>Tari Jaipongan</h3>
      <p>Tarian khas Sunda yang energik dan penuh ekspresi, lahir di tahun 1980-an.</p>
    </div>
    <div class="budaya-item">
      <img src="images/angklung.jpg" alt="Angklung">
      <h3>Angklung</h3>
      <p>Alat musik bambu khas Sunda yang diakui UNESCO sebagai Warisan Budaya Dunia (2010).</p>
    </div>
    <div class="budaya-item">
      <img src="images/batagor.jpg" alt="Batagor">
      <h3>Batagor</h3>
      <p>Makanan khas Bandung yang berasal dari adaptasi dimsum Tiongkok bernama shumai.</p>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  &copy; <?= date('Y') ?> Budaya Sunda | Semua Hak Dilindungi.
</footer>

</body>
</html>
