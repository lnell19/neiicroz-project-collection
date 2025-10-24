<?php
include "../koneksi.php"; // koneksi database
$result = mysqli_query($conn, "SELECT * FROM lagu ORDER BY id_lagu ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lagu Daerah - Budaya Sunda</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
  background: #f8fafc;
  color: #333;
  padding-bottom: 90px;
}

/* Header */
header {
  background: linear-gradient(90deg, var(--blue-600), var(--blue-700));
  color: #fff;
  text-align: center;
  font-size: 22px;
  font-weight: 700;
  padding: 16px 0;
  letter-spacing: 1px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Navbar */
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
.nav-tab.active {
  color: var(--blue-600);
  font-weight: 600;
}
.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0;
  width: 100%; height: 3px;
  background: var(--blue-600);
}

/* Lagu list */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 30px 20px;
}
.section-title {
  text-align: center;
  color: var(--blue-600);
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 25px;
}
.song-item {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  padding: 16px;
  margin-bottom: 15px;
  transition: 0.3s ease;
}
.song-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(37,99,235,0.15);
}
.song-number {
  background: var(--blue-600);
  color: white;
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  margin-right: 16px;
  font-weight: bold;
}
.song-image {
  width: 90px; height: 90px;
  border-radius: 10px;
  object-fit: cover;
  margin-right: 20px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.song-info {
  flex: 1;
}
.song-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--blue-700);
  margin-bottom: 5px;
}
.song-artist {
  font-size: 1rem;
  color: #555;
}
.song-duration {
  font-size: 0.9rem;
  color: #666;
  margin-top: 4px;
}
.play-button {
  background: var(--blue-600);
  color: white;
  width: 48px; height: 48px;
  border-radius: 50%;
  border: none;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  margin-left: 15px;
  transition: all 0.2s ease;
  cursor: pointer;
}
.play-button:hover { background: var(--blue-700); transform: scale(1.05); }
.song-item.playing { background: #e8f2ff; border-left: 5px solid var(--blue-600); }

/* Player Bar */
.player-bar {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  background: linear-gradient(90deg, var(--blue-600), var(--blue-700));
  color: white;
  padding: 15px 30px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.25);
  z-index: 999;
}
.player-info {
  display: flex;
  align-items: center;
  width: 30%;
}
.player-icon {
  width: 50px; height: 50px;
  border-radius: 50%;
  background: rgba(255,255,255,0.15);
  display: flex; align-items: center; justify-content: center;
  margin-right: 15px;
  font-size: 1.2rem;
}
.player-details h4 {
  font-size: 16px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.player-details p {
  font-size: 14px;
  opacity: 0.8;
}
.player-controls {
  width: 30%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.player-control-button {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  opacity: 0.85;
  margin: 0 10px;
  transition: transform .2s;
}
.player-control-button:hover { transform: scale(1.1); opacity: 1; }
.player-main-control {
  background: white;
  color: var(--blue-700);
  width: 42px; height: 42px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
}
.player-volume {
  width: 30%;
  display: flex; align-items: center; justify-content: flex-end;
}
.volume-slider {
  width: 100px;
  margin-left: 8px;
}

/* Responsive */
@media (max-width: 768px) {
  .song-item { flex-direction: column; align-items: flex-start; }
  .song-image { width: 100%; height: 200px; margin-bottom: 10px; }
  .play-button { align-self: center; margin-top: 10px; }
  .player-bar { flex-direction: column; gap: 10px; }
  .player-info, .player-controls, .player-volume { width: 100%; justify-content: center; }
}
</style>
</head>
<body>

<header>Budaya Sunda</header>

<div class="nav-container">
  <nav class="nav-tabs">
    <a href="tari.php" class="nav-tab">Tari Tradisional</a>
    <a href="kuliner.php" class="nav-tab">Kuliner</a>
    <a href="alatmusik.php" class="nav-tab">Alat Musik</a>
    <a href="lagu.php" class="nav-tab active">Lagu</a>
    <a href="destinasi.php" class="nav-tab">Destinasi Wisata</a>
  </nav>
</div>

<div class="container">
  <h2 class="section-title">Lagu Daerah Sunda</h2>

  <?php 
  $no = 1;
  while ($row = mysqli_fetch_assoc($result)): 
    $gambar = 'uploads/lagu/gambar/' . ($row['gambar'] ?: 'default.png');
    if (!file_exists($gambar)) $gambar = 'uploads/lagu/gambar/default.png';
  ?>
  <div class="song-item" data-src="uploads/lagu/audio/<?= htmlspecialchars($row['audio']); ?>">
    <div class="song-number"><?= $no; ?></div>
    <img src="<?= htmlspecialchars($gambar); ?>" alt="<?= htmlspecialchars($row['nama']); ?>" class="song-image">
    <div class="song-info">
      <div class="song-title"><?= htmlspecialchars($row['nama']); ?></div>
      <div class="song-artist"><?= htmlspecialchars($row['penyanyi'] ?? 'Seniman Sunda'); ?></div>
      <div class="song-duration" id="duration-<?= $no; ?>"><i class="fa fa-clock"></i> --:--</div>
    </div>
    <button class="play-button"><i class="fa fa-play"></i></button>
    <audio id="audio-<?= $no; ?>" src="uploads/lagu/audio/<?= htmlspecialchars($row['audio']); ?>"></audio>
  </div>
  <?php $no++; endwhile; ?>
</div>

<!-- Player Bar -->
<div class="player-bar">
  <div class="player-info">
    <div class="player-icon"><i class="fa fa-music"></i></div>
    <div class="player-details">
      <h4 id="player-title">Pilih Lagu</h4>
      <p id="player-artist">Seniman Sunda</p>
    </div>
  </div>

  <div class="player-controls">
    <button class="player-control-button" id="prevBtn"><i class="fa fa-backward"></i></button>
    <button class="player-control-button player-main-control" id="playPauseBtn"><i class="fa fa-play"></i></button>
    <button class="player-control-button" id="nextBtn"><i class="fa fa-forward"></i></button>
  </div>

  <div class="player-volume">
    <i class="fa fa-volume-up"></i>
    <input type="range" id="volumeSlider" class="volume-slider" min="0" max="1" step="0.01" value="1">
  </div>
</div>

<script>
const songs = document.querySelectorAll('.song-item');
const playButtons = document.querySelectorAll('.play-button');
const playerTitle = document.getElementById('player-title');
const playerArtist = document.getElementById('player-artist');
const playPauseBtn = document.getElementById('playPauseBtn');
const volumeSlider = document.getElementById('volumeSlider');
let currentAudio = new Audio();
let currentIndex = -1;

// Hitung durasi
const audios = document.querySelectorAll('audio');
audios.forEach((a, i) => {
  a.addEventListener('loadedmetadata', () => {
    let m = Math.floor(a.duration / 60);
    let s = Math.floor(a.duration % 60);
    if (s < 10) s = "0" + s;
    document.getElementById(`duration-${i+1}`).innerHTML = `<i class='fa fa-clock'></i> ${m}:${s}`;
  });
});

function playSong(index) {
  if (currentIndex !== -1) {
    playButtons[currentIndex].innerHTML = '<i class="fa fa-play"></i>';
    songs[currentIndex].classList.remove('playing');
  }
  currentIndex = index;
  const song = songs[index];
  currentAudio.src = song.dataset.src;
  currentAudio.play();
  song.classList.add('playing');
  playButtons[index].innerHTML = '<i class="fa fa-pause"></i>';
  playerTitle.textContent = song.querySelector('.song-title').textContent;
  playerArtist.textContent = song.querySelector('.song-artist').textContent;
  playPauseBtn.innerHTML = '<i class="fa fa-pause"></i>';
}

playButtons.forEach((btn, i) => {
  btn.addEventListener('click', () => {
    if (currentIndex === i && !currentAudio.paused) {
      currentAudio.pause();
      btn.innerHTML = '<i class="fa fa-play"></i>';
      songs[i].classList.remove('playing');
      playPauseBtn.innerHTML = '<i class="fa fa-play"></i>';
    } else {
      playSong(i);
    }
  });
});

playPauseBtn.addEventListener('click', () => {
  if (currentIndex === -1) return;
  if (currentAudio.paused) {
    currentAudio.play();
    playButtons[currentIndex].innerHTML = '<i class="fa fa-pause"></i>';
    playPauseBtn.innerHTML = '<i class="fa fa-pause"></i>';
  } else {
    currentAudio.pause();
    playButtons[currentIndex].innerHTML = '<i class="fa fa-play"></i>';
    playPauseBtn.innerHTML = '<i class="fa fa-play"></i>';
  }
});

document.getElementById('nextBtn').addEventListener('click', () => {
  if (currentIndex < songs.length - 1) playSong(currentIndex + 1);
});
document.getElementById('prevBtn').addEventListener('click', () => {
  if (currentIndex > 0) playSong(currentIndex - 1);
});
volumeSlider.addEventListener('input', () => { currentAudio.volume = volumeSlider.value; });
</script>

</body>
</html>
