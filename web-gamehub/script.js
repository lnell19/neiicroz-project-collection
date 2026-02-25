// ============================================================
// GameHub — script.js  (FIXED)
// ============================================================
'use strict';

// ── Konfigurasi Supabase ──────────────────────────────────
const SUPABASE_URL = 'https://yytczccpjvbflinbogwe.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inl5dGN6Y2NwanZiZmxpbmJvZ3dlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzE3NjYxMTksImV4cCI6MjA4NzM0MjExOX0.fJ3at2jQBXkLPnVcZcbPM5z7RIWsKk17qLY_B469tpQ';

// ── Supabase client ───────────────────────────────────────
const { createClient } = supabase;
const db = createClient(SUPABASE_URL, SUPABASE_KEY);

// ── State ─────────────────────────────────────────────────
let allGames     = [];
let activeFilter = 'all';
let searchQuery  = '';

// ── Fetch semua game dari Supabase ────────────────────────
async function fetchGames() {
  showLoading(true);
  try {
    const { data, error } = await db
      .from('games')
      .select('*')
      .order('rating', { ascending: false });

    if (error) throw error;

    allGames = data || [];
    updateStatCount(allGames.length);
    applyFilters();

    // FIX: renderNewReleases sekarang ada dan dipanggil dengan benar
    const newGames = allGames.filter(g => g.is_new);
    renderNewReleases(newGames);

  } catch (err) {
    console.error('Database error:', err.message);
    showError('Gagal memuat game. Cek koneksi internet.');
  } finally {
    showLoading(false);
  }
}

// ── Fetch game by ID untuk modal ──────────────────────────
async function fetchGameById(id) {
  const { data, error } = await db
    .from('games')
    .select('*')
    .eq('id', id)
    .single();

  if (error) return null;
  return data;
}

// ── Filter lokal ──────────────────────────────────────────
function getFilteredGames() {
  return allGames.filter(game => {
    const matchFilter = activeFilter === 'all' || game.genre === activeFilter;

    // FIX: Tambah guard agar tidak error jika genre_label undefined
    const title       = (game.title       || '').toLowerCase();
    const genreLabel  = (game.genre_label || '').toLowerCase();
    const query       = searchQuery.toLowerCase();

    const matchSearch = title.includes(query) || genreLabel.includes(query);
    return matchFilter && matchSearch;
  });
}

function applyFilters() {
  const filtered = getFilteredGames();
  renderGames(filtered);
}

// ── Filter buttons ────────────────────────────────────────
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeFilter = btn.dataset.filter;
    applyFilters();
    document.getElementById('popular').scrollIntoView({ behavior: 'smooth' });
  });
});

// ── Search real-time ──────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', e => {
  searchQuery = e.target.value.trim();
  applyFilters();
});

// ── Hamburger Menu ────────────────────────────────────────
// FIX: Hamburger sebelumnya tidak punya event listener sama sekali
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  navLinks.classList.toggle('open');
});

// Tutup nav saat klik link di mobile
navLinks.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    hamburger.classList.remove('open');
    navLinks.classList.remove('open');
  });
});

// ── Navbar scroll effect ──────────────────────────────────
window.addEventListener('scroll', () => {
  const navbar = document.getElementById('navbar');
  navbar.classList.toggle('scrolled', window.scrollY > 50);
});

// ── Render bintang ────────────────────────────────────────
function renderStars(rating) {
  const num  = parseFloat(rating) || 0;
  const full = Math.floor(num);
  let stars  = '★'.repeat(full);
  if (num % 1 >= 0.5) stars += '½';
  return stars;
}

// ── Render game cards ke grid ─────────────────────────────
function renderGames(games) {
  const grid  = document.getElementById('gamesGrid');
  const noRes = document.getElementById('noResults');

  if (!games || games.length === 0) {
    grid.innerHTML   = '';
    noRes.style.display = 'block';
    return;
  }
  noRes.style.display = 'none';
  grid.innerHTML = games.map(g => buildCard(g)).join('');
  observeFadeIns();
}

// FIX: renderNewReleases sekarang ada (sebelumnya dipanggil tapi tidak didefinisikan)
function renderNewReleases(games) {
  const grid   = document.getElementById('newReleaseGrid');
  const noNew  = document.getElementById('noNewRelease');

  if (!grid) return;

  if (!games || games.length === 0) {
    grid.innerHTML        = '';
    if (noNew) noNew.style.display = 'block';
    return;
  }
  if (noNew) noNew.style.display = 'none';
  grid.innerHTML = games.map(g => buildCard(g)).join('');
  observeFadeIns();
}

// ── Helper: build card HTML ───────────────────────────────
function buildCard(g) {
  // FIX: Gunakan image_url dengan fallback ke image_seed, lalu placeholder
  const imgSrc = g.image_url || g.image_seed || 'images/placeholder.png';

  return `
    <div class='game-card fade-in' data-id='${g.id}'>
      <div class='card-img-wrap'>
        <img src='${imgSrc}'
             alt='${g.title || 'Game'}' loading='lazy'
             onerror="this.src='images/placeholder.png'" />
        <span class='card-genre-badge'>${g.genre_label || g.genre || ''}</span>
        ${g.is_new ? "<span class='card-new-badge'>NEW</span>" : ''}
      </div>
      <div class='card-body'>
        <h3 class='card-title'>${g.title || 'Untitled'}</h3>
        <div class='card-meta'>
          <span class='card-genre-text'>${g.genre_label || ''}</span>
          <span class='card-stars' title='${g.rating}/5'>${renderStars(g.rating)}</span>
        </div>
        <div class='card-actions'>
          <button class='btn-play'    data-id='${g.id}'>▶ Play</button>
          <button class='btn-details' data-id='${g.id}'>Details</button>
        </div>
      </div>
    </div>
  `;
}

// ── Buka Modal Detail ─────────────────────────────────────
async function openModal(id) {
  const game = allGames.find(g => g.id === id) || await fetchGameById(id);
  if (!game) return;

  // FIX: Gunakan image_url dengan fallback ke image_seed
  const imgSrc = game.image_url || game.image_seed || 'images/placeholder.png';
  const tags   = Array.isArray(game.tags) ? game.tags : [];

  document.getElementById('modalContent').innerHTML = `
    <img class='modal-img' src='${imgSrc}' alt='${game.title}'
         onerror="this.src='images/placeholder.png'" />
    <div class='modal-body'>
      <span class='modal-tag'>${game.genre_label || game.genre || ''}</span>
      <h2 class='modal-title'>${game.title || 'Untitled'}</h2>
      <p class='modal-rating'>
        <span class='modal-stars'>${renderStars(game.rating)}</span>
        ${game.rating || 'N/A'}/5.0
        ${game.players ? `&nbsp;·&nbsp; ${game.players} players` : ''}
      </p>
      <p class='modal-desc'>${game.description || 'Tidak ada deskripsi.'}</p>
      <div class='modal-meta'>
        ${game.developer ? `
        <div class='modal-meta-item'>
          <span class='modal-meta-label'>Developer</span>
          <span class='modal-meta-value'>${game.developer}</span>
        </div>` : ''}
        ${game.year ? `
        <div class='modal-meta-item'>
          <span class='modal-meta-label'>Tahun Rilis</span>
          <span class='modal-meta-value'>${game.year}</span>
        </div>` : ''}
        ${tags.length > 0 ? `
        <div class='modal-meta-item'>
          <span class='modal-meta-label'>Tags</span>
          <span class='modal-meta-value'>${tags.join(' · ')}</span>
        </div>` : ''}
      </div>
      <div class='modal-actions'>
        <button class='btn-play btn-play-modal' id='modalPlayBtn'>
          ▶ Play Now
        </button>
      </div>
    </div>
  `;
  const playBtn = document.getElementById('modalPlayBtn');

  if (playBtn) {
    playBtn.addEventListener('click', () => {
      if (game.play_url) {
        window.open(game.play_url, '_blank');
      } else {
        alert('Game belum memiliki link play.');
      }
    });
  }
  document.getElementById('modalOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// ── Event delegation untuk card buttons ───────────────────
document.getElementById('gamesGrid').addEventListener('click', e => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const id = parseInt(btn.dataset.id, 10);
  if (isNaN(id)) return;
  if (btn.classList.contains('btn-details')) {
  openModal(id);
}

if (btn.classList.contains('btn-play')) {
  const game = allGames.find(g => g.id === id);
  if (game?.play_url) {
    window.open(game.play_url, '_blank');
  } else {
    alert('Game belum memiliki link play.');
  }
}
});

// FIX: Tambah event delegation untuk grid new release juga
document.getElementById('newReleaseGrid').addEventListener('click', e => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const id = parseInt(btn.dataset.id, 10);
  if (isNaN(id)) return;
  if (btn.classList.contains('btn-details') || btn.classList.contains('btn-play')) {
    openModal(id);
  }
});

// ── Tutup modal ───────────────────────────────────────────
function closeModal() {
  document.getElementById('modalOverlay').classList.remove('active');
  document.body.style.overflow = '';
}

document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalOverlay').addEventListener('click', e => {
  if (e.target.id === 'modalOverlay') closeModal();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeModal();
});

// ── Active nav link saat scroll ───────────────────────────
const sections  = document.querySelectorAll('section[id]');
const navAnchors = document.querySelectorAll('.nav-link');

function updateActiveNav() {
  let current = '';
  sections.forEach(sec => {
    if (window.scrollY >= sec.offsetTop - 100) current = sec.id;
  });
  navAnchors.forEach(a => {
    a.classList.toggle('active', a.getAttribute('href') === '#' + current);
  });
}
window.addEventListener('scroll', updateActiveNav, { passive: true });

// ── Utility functions ─────────────────────────────────────
function showLoading(show) {
  document.getElementById('loadingState').style.display = show ? 'flex' : 'none';
}

function showError(msg) {
  const el = document.getElementById('loadingState');
  el.innerHTML = `<p class="error-msg">⚠ ${msg}</p>`;
  el.style.display = 'flex';
}

function updateStatCount(n) {
  const el = document.getElementById('statGames');
  if (el) el.textContent = n + '+';
}

// ── Fade-in observer ──────────────────────────────────────
function observeFadeIns() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-in:not(.visible)').forEach(el => observer.observe(el));
}

// ── INIT ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  fetchGames();
  observeFadeIns();
});