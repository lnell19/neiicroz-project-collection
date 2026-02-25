// === VARIABEL GLOBAL ===
let playerName1 = "Player 1";
let playerName2 = "Player 2";
let score1 = 0;
let score2 = 0;
let timeLeft = 60;
let timerInterval = null;
let currentQuestion = null;
let remainingQuestions = [];
let isHandUp1 = false;
let isHandUp2 = false;
let handRaisedBy = null;
let answerTimer = null;
let gamepadConnected = false;
let useDualJoysticks = false;
let hasAnsweredThisQuestion = false;
let lastInputTime = 0;
const INPUT_COOLDOWN = 200;
let isFeedbackActive = false;
let isQuestionLocked = false;
let isPaused = false;
let isSoundEnabled = true;
let isMusicEnabled = true;
let pausedTime = 0;
let wasMusicPlayingBeforePause = false;
let selectedCategory = "semua";

// === ELEMEN DOM ===
const introScreen = document.getElementById('introScreen');
const gameContainer = document.getElementById('game-container');
const startButton = document.getElementById('startButton');
const player1Input = document.getElementById('player1Name');
const player2Input = document.getElementById('player2Name');
const countdownOverlay = document.getElementById('countdownOverlay');
const countdownText = document.getElementById('countdownText');
const timerDisplay = document.getElementById('timerDisplay');
const char1 = document.getElementById("character1");
const char2 = document.getElementById("character2");
const activeQuestion = document.getElementById("activeQuestion");
const questionText = document.getElementById("questionText");
const questionOptions = document.getElementById("questionOptions");
const score1El = document.getElementById("score1");
const score2El = document.getElementById("score2");
const rewardModal = document.getElementById("rewardModal");
const rewardText = document.getElementById("rewardText");
const timeUpModal = document.getElementById("timeUpModal");
const finalScoreText = document.getElementById("finalScoreText");
const closeReward = document.getElementById("closeReward");
const closeTimeUp = document.getElementById("closeTimeUp");
const backgroundMusic = document.getElementById('backgroundMusic');
const pauseButton = document.getElementById('pauseButton');
const pauseOverlay = document.getElementById('pauseOverlay');
const pauseMenu = document.getElementById('pauseMenu');
const controlsDisplay = document.getElementById('controlsDisplay');
const resumeButton = document.getElementById('resumeButton');
const showControlsButton = document.getElementById('showControlsButton');
const backFromControls = document.getElementById('backFromControls');
const mainMenuButton = document.getElementById('mainMenuButton');
const soundToggle = document.getElementById('soundToggle');
const musicToggle = document.getElementById('musicToggle');
const instructionsEl = document.querySelector('.instructions');
const feedbackOverlay = document.getElementById('feedbackOverlay');
const categoryBadge = document.getElementById('categoryBadge');

const imgNormal1 = "siswa.png";
const imgHandUp1 = "siswa_handup.png";
const imgNormal2 = "siswi.png";
const imgHandUp2 = "siswi_handup.png";

// === SOAL DENGAN KATEGORI (30 soal per kategori) ===
const allQuestions = [
  // =========================
  // BAHASA INDONESIA (30)
  // =========================
  { q: "Kalimat yang mengandung makna kiasan disebut?", correct: "A", options: ["Metafora", "Fakta", "Deskripsi"], category: "bahasa_indonesia" },
  { q: "Antonim dari 'optimis' adalah?", correct: "B", options: ["Semangat", "Pesimis", "Rajin"], category: "bahasa_indonesia" },
  { q: "Sinonim dari 'cermat' adalah?", correct: "A", options: ["Teliti", "Lalai", "Cepat"], category: "bahasa_indonesia" },
  { q: "Paragraf dengan gagasan utama di akhir disebut?", correct: "C", options: ["Deduktif", "Campuran", "Induktif"], category: "bahasa_indonesia" },
  { q: "Kata baku dari 'resiko' adalah?", correct: "B", options: ["Resiko", "Risiko", "Resikko"], category: "bahasa_indonesia" },
  { q: "Teks berisi langkah-langkah disebut?", correct: "B", options: ["Narasi", "Prosedur", "Deskripsi"], category: "bahasa_indonesia" },
  { q: "Majas yang melebih-lebihkan disebut?", correct: "C", options: ["Personifikasi", "Litotes", "Hiperbola"], category: "bahasa_indonesia" },
  { q: "Kata hubung disebut juga?", correct: "A", options: ["Konjungsi", "Verba", "Nomina"], category: "bahasa_indonesia" },
  { q: "Pantun terdiri dari berapa baris?", correct: "B", options: ["2", "4", "6"], category: "bahasa_indonesia" },
  { q: "Kalimat perintah disebut?", correct: "B", options: ["Deklaratif", "Imperatif", "Interogatif"], category: "bahasa_indonesia" },
  { q: "Cerpen adalah singkatan dari?", correct: "A", options: ["Cerita pendek", "Cerita panjang", "Cerita penuh"], category: "bahasa_indonesia" },
  { q: "Kata kerja disebut?", correct: "A", options: ["Verba", "Nomina", "Adjektiva"], category: "bahasa_indonesia" },
  { q: "Teks laporan bersifat?", correct: "B", options: ["Fiksi", "Objektif", "Khayalan"], category: "bahasa_indonesia" },
  { q: "Kalimat tanya diakhiri tanda?", correct: "C", options: ["!", ".", "?"], category: "bahasa_indonesia" },
  { q: "Gagasan utama disebut juga?", correct: "A", options: ["Ide pokok", "Paragraf", "Kalimat"], category: "bahasa_indonesia" },
  { q: "Surat resmi menggunakan bahasa?", correct: "B", options: ["Gaul", "Baku", "Santai"], category: "bahasa_indonesia" },
  { q: "Drama disajikan dalam bentuk?", correct: "B", options: ["Narasi", "Dialog", "Puisi"], category: "bahasa_indonesia" },
  { q: "Sinonim 'indah' adalah?", correct: "A", options: ["Cantik", "Rusak", "Jelek"], category: "bahasa_indonesia" },
  { q: "Awalan me- termasuk?", correct: "A", options: ["Prefiks", "Sufiks", "Infiks"], category: "bahasa_indonesia" },
  { q: "Akhiran -kan termasuk?", correct: "B", options: ["Prefiks", "Sufiks", "Konjungsi"], category: "bahasa_indonesia" },
  { q: "Cerita rakyat termasuk karya?", correct: "B", options: ["Nonfiksi", "Fiksi", "Ilmiah"], category: "bahasa_indonesia" },
  { q: "Lawan kata modern adalah?", correct: "A", options: ["Kuno", "Baru", "Maju"], category: "bahasa_indonesia" },
  { q: "Teks eksposisi bertujuan untuk?", correct: "B", options: ["Menghibur", "Menjelaskan", "Mengkritik"], category: "bahasa_indonesia" },
  { q: "Kata sifat disebut?", correct: "B", options: ["Verba", "Adjektiva", "Numeralia"], category: "bahasa_indonesia" },
  { q: "Kata ulang penuh contohnya?", correct: "B", options: ["Berlari-lari", "Buku-buku", "Berbuku"], category: "bahasa_indonesia" },
  { q: "Kalimat berita disebut?", correct: "A", options: ["Deklaratif", "Imperatif", "Interogatif"], category: "bahasa_indonesia" },
  { q: "Tanda baca untuk perintah kuat adalah?", correct: "A", options: ["!", ".", "?"], category: "bahasa_indonesia" },
  { q: "Puisi biasanya memiliki?", correct: "B", options: ["Paragraf panjang", "Rima", "Data statistik"], category: "bahasa_indonesia" },
  { q: "Kata depan yang benar adalah?", correct: "A", options: ["di rumah", "dirumah", "kerumah"], category: "bahasa_indonesia" },
  { q: "Paragraf deduktif memiliki ide pokok di?", correct: "A", options: ["Awal paragraf", "Tengah paragraf", "Akhir paragraf"], category: "bahasa_indonesia" },

  // =========================
  // MATEMATIKA (30)
  // =========================
  { q: "7 x 8 = ?", correct: "B", options: ["54", "56", "64"], category: "matematika" },
  { q: "100 ÷ 4 = ?", correct: "B", options: ["20", "25", "30"], category: "matematika" },
  { q: "15 + 27 = ?", correct: "C", options: ["40", "41", "42"], category: "matematika" },
  { q: "Akar dari 81 adalah?", correct: "A", options: ["9", "8", "7"], category: "matematika" },
  { q: "12² = ?", correct: "C", options: ["124", "140", "144"], category: "matematika" },
  { q: "Keliling persegi dengan sisi 5 cm adalah?", correct: "B", options: ["20 cm", "20", "25 cm"], category: "matematika" },
  { q: "Luas persegi sisi 6 cm adalah?", correct: "A", options: ["36 cm²", "24 cm²", "30 cm²"], category: "matematika" },
  { q: "Bilangan prima terkecil adalah?", correct: "B", options: ["1", "2", "3"], category: "matematika" },
  { q: "10% dari 200 adalah?", correct: "C", options: ["10", "15", "20"], category: "matematika" },
  { q: "Rumus luas segitiga adalah?", correct: "A", options: ["½ × alas × tinggi", "alas × tinggi", "sisi × sisi"], category: "matematika" },
  { q: "5³ = ?", correct: "B", options: ["15", "125", "25"], category: "matematika" },
  { q: "Hasil 9 + 6 ÷ 3 adalah?", correct: "A", options: ["11", "5", "15"], category: "matematika" },
  { q: "Sudut siku-siku besarnya?", correct: "C", options: ["45°", "60°", "90°"], category: "matematika" },
  { q: "1 km = ... meter", correct: "B", options: ["100", "1000", "10.000"], category: "matematika" },
  { q: "Pecahan 1/2 sama dengan?", correct: "A", options: ["0,5", "0,2", "0,25"], category: "matematika" },
  { q: "20 - 7 = ?", correct: "C", options: ["11", "12", "13"], category: "matematika" },
  { q: "Bilangan genap adalah?", correct: "A", options: ["8", "9", "7"], category: "matematika" },
  { q: "Bangun ruang dengan 6 sisi sama disebut?", correct: "B", options: ["Balok", "Kubus", "Prisma"], category: "matematika" },
  { q: "3/4 dalam persen adalah?", correct: "C", options: ["50%", "60%", "75%"], category: "matematika" },
  { q: "Hasil 45 ÷ 5 adalah?", correct: "A", options: ["9", "8", "7"], category: "matematika" },
  { q: "π dibulatkan menjadi?", correct: "B", options: ["3", "3,14", "3,41"], category: "matematika" },
  { q: "Kelipatan 5 adalah?", correct: "C", options: ["12", "18", "25"], category: "matematika" },
  { q: "FPB dari 8 dan 12 adalah?", correct: "A", options: ["4", "2", "6"], category: "matematika" },
  { q: "KPK dari 4 dan 6 adalah?", correct: "C", options: ["8", "10", "12"], category: "matematika" },
  { q: "Hasil 6² adalah?", correct: "B", options: ["12", "36", "18"], category: "matematika" },
  { q: "Segitiga memiliki berapa sisi?", correct: "A", options: ["3", "4", "5"], category: "matematika" },
  { q: "Rumus keliling lingkaran adalah?", correct: "B", options: ["πr²", "2πr", "πd²"], category: "matematika" },
  { q: "25% dari 80 adalah?", correct: "C", options: ["15", "10", "20"], category: "matematika" },
  { q: "Hasil 100 - 45 adalah?", correct: "A", options: ["55", "65", "45"], category: "matematika" },
  { q: "Bangun datar dengan 4 sisi sama panjang disebut?", correct: "B", options: ["Persegi panjang", "Persegi", "Trapesium"], category: "matematika" },

  // =========================
  // BAHASA INGGRIS (30)
  // =========================
  { q: "What is the synonym of 'happy'?", correct: "A", options: ["Glad", "Sad", "Angry"], category: "bahasa_inggris" },
  { q: "Past tense of 'go' is?", correct: "B", options: ["Goed", "Went", "Gone"], category: "bahasa_inggris" },
  { q: "She ___ to school every day.", correct: "C", options: ["go", "going", "goes"], category: "bahasa_inggris" },
  { q: "Opposite of 'big' is?", correct: "A", options: ["Small", "Tall", "Wide"], category: "bahasa_inggris" },
  { q: "Plural of 'child' is?", correct: "B", options: ["Childs", "Children", "Childes"], category: "bahasa_inggris" },
  { q: "Meaning of 'beautiful' is?", correct: "A", options: ["Cantik", "Cepat", "Besar"], category: "bahasa_inggris" },
  { q: "He is ___ honest man.", correct: "C", options: ["a", "an a", "an"], category: "bahasa_inggris" },
  { q: "We ___ playing football now.", correct: "B", options: ["is", "are", "am"], category: "bahasa_inggris" },
  { q: "Antonym of 'hot'?", correct: "A", options: ["Cold", "Warm", "Cooler"], category: "bahasa_inggris" },
  { q: "What is the capital of England?", correct: "C", options: ["Paris", "Rome", "London"], category: "bahasa_inggris" },
  { q: "Verb 3 of 'eat'?", correct: "B", options: ["Ate", "Eaten", "Eat"], category: "bahasa_inggris" },
  { q: "She has ___ apple.", correct: "C", options: ["a", "the", "an"], category: "bahasa_inggris" },
  { q: "Meaning of 'teacher'?", correct: "A", options: ["Guru", "Murid", "Dokter"], category: "bahasa_inggris" },
  { q: "We use 'am' with?", correct: "A", options: ["I", "You", "They"], category: "bahasa_inggris" },
  { q: "Comparative of 'fast'?", correct: "B", options: ["Most fast", "Faster", "Fastest"], category: "bahasa_inggris" },
  { q: "Opposite of 'clean'?", correct: "C", options: ["Clear", "Bright", "Dirty"], category: "bahasa_inggris" },
  { q: "He ___ TV yesterday.", correct: "A", options: ["watched", "watch", "watching"], category: "bahasa_inggris" },
  { q: "Month after June?", correct: "B", options: ["May", "July", "August"], category: "bahasa_inggris" },
  { q: "Meaning of 'library'?", correct: "C", options: ["Laboratorium", "Kantin", "Perpustakaan"], category: "bahasa_inggris" },
  { q: "Superlative of 'good'?", correct: "A", options: ["Best", "Better", "Goodest"], category: "bahasa_inggris" },
  { q: "They ___ students.", correct: "B", options: ["is", "are", "am"], category: "bahasa_inggris" },
  { q: "Opposite of 'early'?", correct: "C", options: ["Fast", "Soon", "Late"], category: "bahasa_inggris" },
  { q: "Meaning of 'expensive'?", correct: "A", options: ["Mahal", "Murah", "Gratis"], category: "bahasa_inggris" },
  { q: "Past tense of 'see'?", correct: "B", options: ["Seen", "Saw", "See"], category: "bahasa_inggris" },
  { q: "We ___ dinner at 7 pm.", correct: "C", options: ["eats", "eating", "eat"], category: "bahasa_inggris" },
  { q: "Synonym of 'smart'?", correct: "A", options: ["Clever", "Lazy", "Slow"], category: "bahasa_inggris" },
  { q: "Plural of 'mouse'?", correct: "B", options: ["Mouses", "Mice", "Mousees"], category: "bahasa_inggris" },
  { q: "Opposite of 'strong'?", correct: "C", options: ["Big", "Tall", "Weak"], category: "bahasa_inggris" },
  { q: "Meaning of 'hospital'?", correct: "A", options: ["Rumah sakit", "Sekolah", "Bandara"], category: "bahasa_inggris" },
  { q: "He ___ finished his work.", correct: "B", options: ["have", "has", "had"], category: "bahasa_inggris" },

  // =========================
  // KIMIA (30)
  // =========================
  { q: "Unsur dengan nomor atom 1 adalah?", correct: "A", options: ["Hidrogen", "Helium", "Oksigen"], category: "kimia" },
  { q: "Rumus kimia karbon monoksida adalah?", correct: "B", options: ["CO2", "CO", "C2O"], category: "kimia" },
  { q: "Larutan dengan pH kurang dari 7 bersifat?", correct: "C", options: ["Netral", "Basa", "Asam"], category: "kimia" },
  { q: "Proses perkaratan termasuk perubahan?", correct: "A", options: ["Kimia", "Fisik", "Mekanik"], category: "kimia" },
  { q: "Simbol unsur perak adalah?", correct: "B", options: ["Au", "Ag", "Pb"], category: "kimia" },
  { q: "Air tersusun dari unsur?", correct: "C", options: ["H dan C", "O dan C", "H dan O"], category: "kimia" },
  { q: "Tabel periodik disusun berdasarkan?", correct: "A", options: ["Nomor atom", "Massa", "Warna"], category: "kimia" },
  { q: "Zat yang menerima ion H+ disebut?", correct: "B", options: ["Asam", "Basa", "Garam"], category: "kimia" },
  { q: "Reaksi pembakaran memerlukan?", correct: "C", options: ["Air", "Tanah", "Oksigen"], category: "kimia" },
  { q: "Unsur gas mulia adalah?", correct: "A", options: ["Neon", "Natrium", "Nitrogen"], category: "kimia" },
  { q: "Rumus kimia amonia adalah?", correct: "B", options: ["NH4", "NH3", "NO3"], category: "kimia" },
  { q: "Unsur dengan simbol K adalah?", correct: "C", options: ["Kalsium", "Karbon", "Kalium"], category: "kimia" },
  { q: "Ion bermuatan negatif disebut?", correct: "A", options: ["Anion", "Kation", "Proton"], category: "kimia" },
  { q: "pH air murni adalah?", correct: "B", options: ["6", "7", "8"], category: "kimia" },
  { q: "Zat padat berubah menjadi gas disebut?", correct: "C", options: ["Mencair", "Membeku", "Menyublim"], category: "kimia" },
  { q: "Unsur utama penyusun udara adalah?", correct: "A", options: ["Nitrogen", "Oksigen", "Karbon"], category: "kimia" },
  { q: "Simbol unsur timbal adalah?", correct: "B", options: ["Ti", "Pb", "Tb"], category: "kimia" },
  { q: "Molekul O2 terdiri dari ... atom oksigen", correct: "C", options: ["1", "3", "2"], category: "kimia" },
  { q: "Unsur logam bersifat?", correct: "A", options: ["Mengkilap", "Rapuh", "Lunak sekali"], category: "kimia" },
  { q: "Garam dapur memiliki rasa?", correct: "B", options: ["Manis", "Asin", "Pahit"], category: "kimia" },
  { q: "Unsur dengan simbol Fe adalah?", correct: "C", options: ["Fluor", "Fosfor", "Besi"], category: "kimia" },
  { q: "Reaksi kimia yang melepaskan panas disebut?", correct: "A", options: ["Eksoterm", "Endoterm", "Netral"], category: "kimia" },
  { q: "Larutan gula termasuk campuran?", correct: "B", options: ["Heterogen", "Homogen", "Suspensi"], category: "kimia" },
  { q: "Bilangan oksidasi oksigen dalam H2O adalah?", correct: "C", options: ["0", "+1", "-2"], category: "kimia" },
  { q: "Senyawa NaCl dikenal sebagai?", correct: "A", options: ["Garam dapur", "Gula", "Cuka"], category: "kimia" },
  { q: "Asam sulfat memiliki rumus kimia?", correct: "B", options: ["HCl", "H2SO4", "HNO3"], category: "kimia" },
  { q: "Unsur yang paling melimpah di kerak bumi?", correct: "C", options: ["Besi", "Kalsium", "Oksigen"], category: "kimia" },
  { q: "Proses pembentukan senyawa dari unsur-unsurnya disebut?", correct: "A", options: ["Sintesis", "Analisis", "Dekomposisi"], category: "kimia" },
  { q: "pH 14 menunjukkan larutan bersifat?", correct: "B", options: ["Asam kuat", "Basa kuat", "Netral"], category: "kimia" },
  { q: "Unsur radioaktif alami pertama yang ditemukan?", correct: "C", options: ["Plutonium", "Uranium", "Polonium"], category: "kimia" },

  // =========================
  // FISIKA (30)
  // =========================
  { q: "Satuan gaya adalah?", correct: "A", options: ["Newton", "Joule", "Watt"], category: "fisika" },
  { q: "Alat untuk mengukur suhu adalah?", correct: "B", options: ["Barometer", "Termometer", "Higrometer"], category: "fisika" },
  { q: "Kecepatan adalah jarak dibagi?", correct: "C", options: ["Massa", "Gaya", "Waktu"], category: "fisika" },
  { q: "Satuan energi adalah?", correct: "A", options: ["Joule", "Newton", "Pascal"], category: "fisika" },
  { q: "Gaya gravitasi ditemukan oleh?", correct: "B", options: ["Einstein", "Newton", "Galileo"], category: "fisika" },
  { q: "Benda diam memiliki kecepatan?", correct: "C", options: ["1 m/s", "Tidak tetap", "0"], category: "fisika" },
  { q: "Satuan arus listrik adalah?", correct: "A", options: ["Ampere", "Volt", "Ohm"], category: "fisika" },
  { q: "Cahaya merambat secara?", correct: "B", options: ["Melengkung", "Lurus", "Acak"], category: "fisika" },
  { q: "Alat untuk mengukur tekanan udara?", correct: "C", options: ["Termometer", "Amperemeter", "Barometer"], category: "fisika" },
  { q: "Satuan daya adalah?", correct: "A", options: ["Watt", "Joule", "Newton"], category: "fisika" },
  { q: "Hukum aksi reaksi adalah hukum?", correct: "B", options: ["I Newton", "III Newton", "II Newton"], category: "fisika" },
  { q: "Gelombang bunyi tidak dapat merambat di?", correct: "C", options: ["Air", "Besi", "Ruang hampa"], category: "fisika" },
  { q: "Satuan tegangan listrik?", correct: "A", options: ["Volt", "Ampere", "Watt"], category: "fisika" },
  { q: "Energi kinetik dipengaruhi oleh?", correct: "B", options: ["Warna", "Massa dan kecepatan", "Bentuk"], category: "fisika" },
  { q: "Cermin datar menghasilkan bayangan?", correct: "C", options: ["Terbalik", "Kecil", "Sama besar"], category: "fisika" },
  { q: "Satuan frekuensi?", correct: "A", options: ["Hertz", "Newton", "Tesla"], category: "fisika" },
  { q: "Alat ukur massa?", correct: "B", options: ["Penggaris", "Neraca", "Termometer"], category: "fisika" },
  { q: "Bunyi termasuk gelombang?", correct: "C", options: ["Elektromagnetik", "Cahaya", "Mekanik"], category: "fisika" },
  { q: "Rumus gaya adalah?", correct: "A", options: ["F = m × a", "E = m × c²", "V = s/t"], category: "fisika" },
  { q: "Satuan hambatan listrik?", correct: "B", options: ["Volt", "Ohm", "Ampere"], category: "fisika" },
  { q: "Benda jatuh dipercepat oleh?", correct: "C", options: ["Angin", "Magnet", "Gravitasi"], category: "fisika" },
  { q: "Energi potensial dipengaruhi oleh?", correct: "A", options: ["Ketinggian", "Warna", "Bentuk"], category: "fisika" },
  { q: "Cahaya termasuk gelombang?", correct: "B", options: ["Mekanik", "Elektromagnetik", "Air"], category: "fisika" },
  { q: "Alat ukur waktu?", correct: "C", options: ["Meteran", "Neraca", "Stopwatch"], category: "fisika" },
  { q: "Satuan tekanan?", correct: "A", options: ["Pascal", "Watt", "Joule"], category: "fisika" },
  { q: "Hukum Ohm berbunyi?", correct: "B", options: ["F=m.a", "V=I.R", "E=m.c2"], category: "fisika" },
  { q: "Energi tidak dapat diciptakan disebut hukum?", correct: "C", options: ["Gravitasi", "Newton", "Kekekalan energi"], category: "fisika" },
  { q: "Benda mengapung jika massa jenisnya?", correct: "A", options: ["Lebih kecil", "Lebih besar", "Sama"], category: "fisika" },
  { q: "Satuan percepatan?", correct: "B", options: ["m/s", "m/s²", "km"], category: "fisika" },
  { q: "Contoh energi kimia?", correct: "C", options: ["Cahaya", "Gerak", "Baterai"], category: "fisika" },

  // =========================
  // UMUM (30)
  // =========================
  { q: "Ibu kota Indonesia adalah?", correct: "A", options: ["Jakarta", "Bandung", "Surabaya"], category: "umum" },
  { q: "Presiden pertama Indonesia?", correct: "B", options: ["Soeharto", "Soekarno", "Habibie"], category: "umum" },
  { q: "Benua terbesar di dunia?", correct: "C", options: ["Afrika", "Eropa", "Asia"], category: "umum" },
  { q: "Gunung tertinggi di dunia?", correct: "A", options: ["Everest", "K2", "Fuji"], category: "umum" },
  { q: "Samudra terbesar?", correct: "B", options: ["Atlantik", "Pasifik", "Hindia"], category: "umum" },
  { q: "Mata uang Jepang?", correct: "C", options: ["Won", "Dollar", "Yen"], category: "umum" },
  { q: "Planet terdekat dari Matahari?", correct: "A", options: ["Merkurius", "Venus", "Bumi"], category: "umum" },
  { q: "Hewan tercepat di darat?", correct: "B", options: ["Singa", "Cheetah", "Kuda"], category: "umum" },
  { q: "Bahasa resmi Brazil?", correct: "C", options: ["Spanyol", "Inggris", "Portugis"], category: "umum" },
  { q: "Negara dengan populasi terbesar?", correct: "A", options: ["China", "India", "USA"], category: "umum" },
  { q: "Warna bendera Indonesia?", correct: "B", options: ["Merah-Biru", "Merah-Putih", "Putih-Biru"], category: "umum" },
  { q: "Alat musik tradisional Jawa?", correct: "C", options: ["Angklung", "Sasando", "Gamelan"], category: "umum" },
  { q: "Ibukota Perancis?", correct: "A", options: ["Paris", "Roma", "Berlin"], category: "umum" },
  { q: "Benua terkecil?", correct: "B", options: ["Eropa", "Australia", "Antartika"], category: "umum" },
  { q: "Sungai terpanjang di dunia?", correct: "C", options: ["Amazon", "Yangtze", "Nil"], category: "umum" },
  { q: "Organ pernapasan manusia?", correct: "A", options: ["Paru-paru", "Jantung", "Hati"], category: "umum" },
  { q: "Hari kemerdekaan Indonesia?", correct: "B", options: ["1 Juni", "17 Agustus", "10 November"], category: "umum" },
  { q: "Ibukota Amerika Serikat?", correct: "C", options: ["New York", "Los Angeles", "Washington D.C."], category: "umum" },
  { q: "Planet terbesar?", correct: "A", options: ["Jupiter", "Saturnus", "Neptunus"], category: "umum" },
  { q: "Lambang negara Indonesia?", correct: "B", options: ["Harimau", "Garuda", "Elang"], category: "umum" },
  { q: "Bahan bakar kendaraan?", correct: "C", options: ["Air", "Minyak tanah", "Bensin"], category: "umum" },
  { q: "Ibukota Italia?", correct: "A", options: ["Roma", "Milan", "Napoli"], category: "umum" },
  { q: "Hewan mamalia?", correct: "B", options: ["Ayam", "Paus", "Ikan"], category: "umum" },
  { q: "Alat komunikasi jarak jauh?", correct: "C", options: ["Radio", "Televisi", "Telepon"], category: "umum" },
  { q: "Negara piramida?", correct: "A", options: ["Mesir", "India", "Yunani"], category: "umum" },
  { q: "Warna daun karena?", correct: "B", options: ["Air", "Klorofil", "Tanah"], category: "umum" },
  { q: "Ibukota Jerman?", correct: "C", options: ["Munich", "Hamburg", "Berlin"], category: "umum" },
  { q: "Alat ukur panjang?", correct: "A", options: ["Penggaris", "Timbangan", "Termometer"], category: "umum" },
  { q: "Pulau terbesar di Indonesia?", correct: "B", options: ["Jawa", "Kalimantan", "Sumatra"], category: "umum" },
  { q: "Hewan berkaki delapan?", correct: "C", options: ["Semut", "Kecoa", "Laba-laba"], category: "umum" },

  // =========================
  // ASTRONOMI (30)
  // =========================
  { q: "Bintang pusat tata surya?", correct: "A", options: ["Matahari", "Sirius", "Polaris"], category: "astronomi" },
  { q: "Planet bercincin terkenal?", correct: "B", options: ["Mars", "Saturnus", "Venus"], category: "astronomi" },
  { q: "Galaksi tempat kita berada?", correct: "C", options: ["Andromeda", "Sombrero", "Bima Sakti"], category: "astronomi" },
  { q: "Planet merah?", correct: "A", options: ["Mars", "Merkurius", "Jupiter"], category: "astronomi" },
  { q: "Satelit alami Bumi?", correct: "B", options: ["Phobos", "Bulan", "Europa"], category: "astronomi" },
  { q: "Planet terbesar?", correct: "C", options: ["Saturnus", "Neptunus", "Jupiter"], category: "astronomi" },
  { q: "Fenomena bintang jatuh disebut?", correct: "A", options: ["Meteor", "Asteroid", "Komet"], category: "astronomi" },
  { q: "Planet terdekat dari Matahari?", correct: "B", options: ["Venus", "Merkurius", "Bumi"], category: "astronomi" },
  { q: "Alat untuk melihat benda langit?", correct: "C", options: ["Mikroskop", "Periskop", "Teleskop"], category: "astronomi" },
  { q: "Gerhana matahari terjadi saat?", correct: "A", options: ["Bulan di antara Matahari dan Bumi", "Bumi di antara Matahari dan Bulan", "Matahari di antara Bumi dan Bulan"], category: "astronomi" },
  { q: "Planet dengan hari terpanjang?", correct: "B", options: ["Mars", "Venus", "Bumi"], category: "astronomi" },
  { q: "Kumpulan bintang disebut?", correct: "C", options: ["Planet", "Orbit", "Rasi bintang"], category: "astronomi" },
  { q: "Planet biru?", correct: "A", options: ["Bumi", "Neptunus", "Uranus"], category: "astronomi" },
  { q: "Sabuk asteroid berada di antara?", correct: "B", options: ["Bumi-Mars", "Mars-Jupiter", "Jupiter-Saturnus"], category: "astronomi" },
  { q: "Komet tersusun dari?", correct: "C", options: ["Batu panas", "Gas saja", "Es dan debu"], category: "astronomi" },
  { q: "Planet terpanas?", correct: "A", options: ["Venus", "Merkurius", "Mars"], category: "astronomi" },
  { q: "Neptunus berwarna?", correct: "B", options: ["Merah", "Biru", "Hijau"], category: "astronomi" },
  { q: "Rotasi bumi menyebabkan?", correct: "C", options: ["Musim", "Gerhana", "Siang dan malam"], category: "astronomi" },
  { q: "Revolusi bumi menyebabkan?", correct: "A", options: ["Pergantian musim", "Gempa", "Tsunami"], category: "astronomi" },
  { q: "Planet dengan cincin terbesar?", correct: "B", options: ["Jupiter", "Saturnus", "Mars"], category: "astronomi" },
  { q: "Asteroid adalah?", correct: "C", options: ["Bintang", "Planet kecil", "Batuan luar angkasa"], category: "astronomi" },
  { q: "Waktu bumi mengelilingi matahari?", correct: "A", options: ["365 hari", "30 hari", "24 jam"], category: "astronomi" },
  { q: "Planet tanpa satelit alami?", correct: "B", options: ["Mars", "Merkurius", "Jupiter"], category: "astronomi" },
  { q: "Lubang hitam memiliki gravitasi?", correct: "C", options: ["Lemah", "Normal", "Sangat kuat"], category: "astronomi" },
  { q: "Planet terbesar kedua?", correct: "A", options: ["Saturnus", "Neptunus", "Uranus"], category: "astronomi" },
  { q: "Cahaya bulan berasal dari?", correct: "B", options: ["Api", "Pantulan matahari", "Listrik"], category: "astronomi" },
  { q: "Bumi berada di urutan ke?", correct: "C", options: ["2", "4", "3"], category: "astronomi" },
  { q: "Nama galaksi tetangga terdekat?", correct: "A", options: ["Andromeda", "Triangulum", "Whirlpool"], category: "astronomi" },
  { q: "Planet gas raksasa?", correct: "B", options: ["Mars", "Jupiter", "Bumi"], category: "astronomi" },
  { q: "Fenomena aurora terjadi di?", correct: "C", options: ["Gurun", "Khatulistiwa", "Kutub"], category: "astronomi" },

  // =========================
  // SEJARAH INDONESIA (30)
  // =========================
  { q: "Proklamasi kemerdekaan Indonesia dibacakan pada tanggal?", correct: "A", options: ["17 Agustus 1945", "10 November 1945", "1 Juni 1945"], category: "sejarah_indonesia" },
  { q: "Sumpah Pemuda terjadi pada tahun?", correct: "B", options: ["1926", "1928", "1930"], category: "sejarah_indonesia" },
  { q: "Pendiri organisasi Budi Utomo?", correct: "C", options: ["Sukarno", "Mohammad Hatta", "Dr. Wahidin Sudirohusodo"], category: "sejarah_indonesia" },
  { q: "Hari Pahlawan diperingati setiap tanggal?", correct: "A", options: ["10 November", "17 Agustus", "1 Desember"], category: "sejarah_indonesia" },
  { q: "Revolusi Nasional Indonesia terjadi setelah?", correct: "B", options: ["Perang Dunia I", "Proklamasi Kemerdekaan", "Perjanjian Linggarjati"], category: "sejarah_indonesia" },
  { q: "Perjanjian Linggarjati antara Indonesia dan?", correct: "C", options: ["Belanda dan Inggris", "Jepang dan Belanda", "Belanda dan Indonesia"], category: "sejarah_indonesia" },
  { q: "Pahlawan nasional dari Aceh?", correct: "A", options: ["Cut Nyak Dien", "Sudirman", "Diponegoro"], category: "sejarah_indonesia" },
  { q: "Sistem pemerintahan Indonesia pasca 1945?", correct: "B", options: ["Monarki", "Republik", "Federasi"], category: "sejarah_indonesia" },
  { q: "Tragedi G30S terjadi pada tahun?", correct: "C", options: ["1963", "1964", "1965"], category: "sejarah_indonesia" },
  { q: "Proklamator Indonesia selain Soekarno?", correct: "A", options: ["Mohammad Hatta", "Sudirman", "Ahmad Yani"], category: "sejarah_indonesia" },
  { q: "Pertempuran Surabaya terjadi tahun?", correct: "B", options: ["1944", "1945", "1946"], category: "sejarah_indonesia" },
  { q: "Piagam Jakarta menjadi dasar?", correct: "C", options: ["UUD 1945", "Pancasila", "Konstitusi 1945"], category: "sejarah_indonesia" },
  { q: "Sukarno diangkat sebagai presiden pertama tahun?", correct: "A", options: ["1945", "1946", "1949"], category: "sejarah_indonesia" },
  { q: "Perjuangan R.A. Kartini berfokus pada?", correct: "B", options: ["Kemerdekaan", "Emansipasi wanita", "Pendidikan militer"], category: "sejarah_indonesia" },
  { q: "Konferensi Meja Bundar diadakan pada tahun?", correct: "C", options: ["1947", "1948", "1949"], category: "sejarah_indonesia" },
  { q: "Sultan Hasanuddin terkenal karena?", correct: "A", options: ["Melawan VOC", "Mendirikan kerajaan", "Menjadi gubernur"], category: "sejarah_indonesia" },
  { q: "Hari Kebangkitan Nasional diperingati tanggal?", correct: "B", options: ["17 Agustus", "20 Mei", "10 November"], category: "sejarah_indonesia" },
  { q: "Pemberontakan DI/TII dipimpin oleh?", correct: "C", options: ["Sudirman", "Soekarno", "Kartosoewirjo"], category: "sejarah_indonesia" },
  { q: "Runtuhnya kerajaan Majapahit sekitar tahun?", correct: "A", options: ["1500-an", "1400-an", "1600-an"], category: "sejarah_indonesia" },
  { q: "Penjajahan Belanda berlangsung selama?", correct: "B", options: ["200 tahun", "350 tahun", "100 tahun"], category: "sejarah_indonesia" },
  { q: "Hari Lahir Pancasila?", correct: "A", options: ["1 Juni", "17 Agustus", "10 November"], category: "sejarah_indonesia" },
  { q: "Tokoh Pergerakan Nasional asal Sumatera Barat?", correct: "A", options: ["Muhammad Yamin", "Sukarno", "Hatta"], category: "sejarah_indonesia" },
  { q: "Perang Diponegoro berlangsung selama?", correct: "B", options: ["5 tahun", "5 tahun (1825-1830)", "10 tahun"], category: "sejarah_indonesia" },
  { q: "Hari Proklamasi Kemerdekaan ditetapkan oleh?", correct: "C", options: ["BPUPKI", "PPKI", "Soekarno dan Hatta"], category: "sejarah_indonesia" },
  { q: "Budi Utomo berdiri pada tahun?", correct: "A", options: ["1908", "1912", "1918"], category: "sejarah_indonesia" },
  { q: "Reformasi Indonesia terjadi tahun?", correct: "B", options: ["1997", "1998", "1999"], category: "sejarah_indonesia" },
  { q: "Peristiwa Rengasdengklok terkait?", correct: "C", options: ["Proklamasi", "Perang Kemerdekaan", "Pemindahan Soekarno-Hatta"], category: "sejarah_indonesia" },
  { q: "Hari Pahlawan nasional diambil dari pertempuran?", correct: "A", options: ["Surabaya", "Jakarta", "Medan"], category: "sejarah_indonesia" },
  { q: "Negara pertama mengakui kemerdekaan Indonesia?", correct: "B", options: ["Belanda", "Mesir", "Amerika"], category: "sejarah_indonesia" },
  { q: "Peran Mohammad Hatta selain proklamator?", correct: "C", options: ["Pahlawan militer", "Gubernur", "Wakil Presiden"], category: "sejarah_indonesia" },

  // =========================
  // SOSIAL (30)
  // =========================
  { q: "Negara dengan populasi terbanyak?", correct: "A", options: ["China", "India", "Amerika Serikat"], category: "sosial" },
  { q: "Organisasi dunia PBB dibentuk pada tahun?", correct: "B", options: ["1919", "1945", "1950"], category: "sosial" },
  { q: "Bahasa resmi PBB?", correct: "C", options: ["Inggris", "Spanyol", "Semua jawaban benar"], category: "sosial" },
  { q: "Negara terluas di dunia?", correct: "A", options: ["Rusia", "Kanada", "China"], category: "sosial" },
  { q: "Negara terkecil menurut luas?", correct: "B", options: ["Monako", "Vatican", "San Marino"], category: "sosial" },
  { q: "Benua terkaya berdasarkan PDB?", correct: "C", options: ["Afrika", "Asia", "Eropa"], category: "sosial" },
  { q: "Benua paling padat penduduk?", correct: "A", options: ["Asia", "Eropa", "Afrika"], category: "sosial" },
  { q: "Negara dengan GDP tertinggi?", correct: "B", options: ["China", "Amerika Serikat", "Jepang"], category: "sosial" },
  { q: "Negara termiskin di dunia?", correct: "C", options: ["Haiti", "Somalia", "Burundi"], category: "sosial" },
  { q: "Ibukota Inggris?", correct: "A", options: ["London", "Manchester", "Birmingham"], category: "sosial" },
  { q: "Ibukota Jepang?", correct: "B", options: ["Kyoto", "Tokyo", "Osaka"], category: "sosial" },
  { q: "Ibukota Australia?", correct: "C", options: ["Sydney", "Melbourne", "Canberra"], category: "sosial" },
  { q: "Mata uang Amerika Serikat?", correct: "A", options: ["Dollar", "Euro", "Yen"], category: "sosial" },
  { q: "Mata uang Inggris?", correct: "B", options: ["Euro", "Pound Sterling", "Dollar"], category: "sosial" },
  { q: "Negara berkembang adalah?", correct: "C", options: ["Jerman", "Brazil", "Indonesia"], category: "sosial" },
  { q: "Negara maju di Asia?", correct: "A", options: ["Jepang", "Nepal", "Laos"], category: "sosial" },
  { q: "Negara terpadat di Afrika?", correct: "B", options: ["Nigeria", "Mesir", "Ethiopia"], category: "sosial" },
  { q: "Benua terkecil berdasarkan luas?", correct: "C", options: ["Australia", "Eropa", "Antartika"], category: "sosial" },
  { q: "Negara dengan kemerdekaan terakhir di dunia?", correct: "A", options: ["Timor Leste", "Kosovo", "Sudan Selatan"], category: "sosial" },
  { q: "Negara termuda di dunia?", correct: "B", options: ["Montenegro", "Timor Leste", "Kosovo"], category: "sosial" },
  { q: "Organisasi ASEAN berdiri tahun?", correct: "C", options: ["1965", "1966", "1967"], category: "sosial" },
  { q: "Benua paling padat kedua?", correct: "A", options: ["Afrika", "Eropa", "Amerika"], category: "sosial" },
  { q: "Negara dengan peringkat literasi tinggi?", correct: "B", options: ["Norwegia", "Finlandia", "Swedia"], category: "sosial" },
  { q: "Negara penghasil minyak terbesar?", correct: "C", options: ["Irak", "Saudi Arabia", "Amerika Serikat"], category: "sosial" },
  { q: "Negara terkecil di Asia?", correct: "A", options: ["Maladewa", "Brunei", "Singapura"], category: "sosial" },
  { q: "Negara tanpa tentara?", correct: "B", options: ["Liechtenstein", "Monako", "Vatican"], category: "sosial" },
  { q: "Negara dengan jumlah pulau terbanyak?", correct: "C", options: ["Filipina", "Norwegia", "Indonesia"], category: "sosial" },
  { q: "Negara dengan indeks pembangunan manusia tinggi?", correct: "A", options: ["Norwegia", "Nigeria", "Pakistan"], category: "sosial" },
  { q: "Negara pertama menggunakan sistem demokrasi modern?", correct: "B", options: ["Prancis", "Amerika Serikat", "Inggris"], category: "sosial" },
  { q: "Ibukota Rusia?", correct: "A", options: ["Moskow", "St. Petersburg", "Sochi"], category: "sosial" },

  // =========================
  // BIOLOGI (30)
  // =========================
  { q: "Organel sel yang berfungsi menghasilkan energi disebut:", correct: "B", options: ["Ribosom", "Mitokondria", "Nukleus"], category: "biologi" },
  { q: "Proses fotosintesis terjadi di:", correct: "A", options: ["Kloroplas", "Mitokondria", "Sitoplasma"], category: "biologi" },
  { q: "DNA ditemukan di:", correct: "C", options: ["Ribosom", "Sitoplasma", "Nukleus"], category: "biologi" },
  { q: "Fungsi ribosom dalam sel adalah:", correct: "A", options: ["Sintesis protein", "Menghasilkan energi", "Transportasi zat"], category: "biologi" },
  { q: "Bagian tumbuhan yang melakukan fotosintesis adalah:", correct: "B", options: ["Akar", "Daun", "Batang"], category: "biologi" },
  { q: "Sistem pernapasan manusia dimulai dari:", correct: "C", options: ["Paru-paru", "Bronkus", "Hidung"], category: "biologi" },
  { q: "Enzim yang memecah amilum menjadi glukosa adalah:", correct: "A", options: ["Amilase", "Lipase", "Protease"], category: "biologi" },
  { q: "Jumlah kromosom manusia normal adalah:", correct: "B", options: ["23", "46", "92"], category: "biologi" },
  { q: "Proses pembelahan sel menjadi dua sel identik disebut:", correct: "C", options: ["Meiosis", "Gametogenesis", "Mitosis"], category: "biologi" },
  { q: "Hormon yang mengatur kadar gula darah adalah:", correct: "A", options: ["Insulin", "Adrenalin", "Tiroksin"], category: "biologi" },
  { q: "Bagian jantung yang memompa darah ke seluruh tubuh adalah:", correct: "B", options: ["Atrium kiri", "Ventrikel kiri", "Ventrikel kanan"], category: "biologi" },
  { q: "Vitamin yang dihasilkan oleh bakteri di usus besar adalah:", correct: "C", options: ["Vitamin A", "Vitamin C", "Vitamin K"], category: "biologi" },
  { q: "Proses pengeluaran zat sisa metabolisme disebut:", correct: "A", options: ["Ekskresi", "Sekresi", "Defekasi"], category: "biologi" },
  { q: "Sel darah merah berfungsi mengangkut:", correct: "B", options: ["Nutrisi", "Oksigen", "Hormon"], category: "biologi" },
  { q: "Bagian otak yang mengatur keseimbangan tubuh adalah:", correct: "C", options: ["Serebrum", "Medula oblongata", "Serebelum"], category: "biologi" },
  { q: "Proses pembentukan sperma disebut:", correct: "A", options: ["Spermatogenesis", "Oogenesis", "Fertilisasi"], category: "biologi" },
  { q: "Enzim pencernaan yang dihasilkan lambung adalah:", correct: "B", options: ["Tripsin", "Pepsin", "Amilase"], category: "biologi" },
  { q: "Jaringan yang menghubungkan tulang dengan tulang adalah:", correct: "C", options: ["Otot", "Tendon", "Ligamen"], category: "biologi" },
  { q: "Pigmen yang memberi warna hijau pada daun adalah:", correct: "A", options: ["Klorofil", "Karoten", "Xantofil"], category: "biologi" },
  { q: "Bagian bunga yang menjadi buah setelah pembuahan adalah:", correct: "B", options: ["Kelopak", "Ovarium", "Benang sari"], category: "biologi" },
  { q: "Sistem saraf pusat terdiri dari:", correct: "C", options: ["Otak dan sumsum tulang belakang", "Otak dan saraf tepi", "Otak dan medula spinalis"], category: "biologi" },
  { q: "Proses penyerapan nutrisi terjadi di:", correct: "A", options: ["Usus halus", "Lambung", "Usus besar"], category: "biologi" },
  { q: "Hormon pertumbuhan dihasilkan oleh:", correct: "B", options: ["Tiroid", "Hipofisis", "Pankreas"], category: "biologi" },
  { q: "Bagian mata yang mengatur jumlah cahaya masuk adalah:", correct: "C", options: ["Kornea", "Retina", "Iris"], category: "biologi" },
  { q: "Proses pembentukan urine terjadi di:", correct: "A", options: ["Ginjal", "Hati", "Kandung kemih"], category: "biologi" },
  { q: "Enzim yang mencerna lemak adalah:", correct: "B", options: ["Amilase", "Lipase", "Pepsin"], category: "biologi" },
  { q: "Jumlah ruang jantung manusia adalah:", correct: "C", options: ["2", "3", "4"], category: "biologi" },
  { q: "Bagian telinga yang berfungsi menjaga keseimbangan adalah:", correct: "A", options: ["Labirin", "Gendang telinga", "Koklea"], category: "biologi" },
  { q: "Proses pembuahan pada manusia terjadi di:", correct: "B", options: ["Rahim", "Tuba falopi", "Ovarium"], category: "biologi" },
  { q: "Vitamin yang larut dalam lemak adalah:", correct: "C", options: ["Vitamin B", "Vitamin C", "Vitamin A"], category: "biologi" },

  // =========================
  // OLAHRAGA (30)
  // =========================
  { q: "Berapa pemain dalam tim sepak bola?", correct: "C", options: ["9", "10", "11"], category: "olahraga" },
  { q: "Panjang lintasan lari 1 putaran standar?", correct: "B", options: ["300 meter", "400 meter", "500 meter"], category: "olahraga" },
  { q: "Tinggi net voli putra?", correct: "A", options: ["2,43 meter", "2,24 meter", "2,10 meter"], category: "olahraga" },
  { q: "Jumlah pemain dalam tim basket?", correct: "B", options: ["4", "5", "6"], category: "olahraga" },
  { q: "Durasi 1 babak tinju profesional?", correct: "C", options: ["2 menit", "2,5 menit", "3 menit"], category: "olahraga" },
  { q: "Jarak marathon standar?", correct: "A", options: ["42,195 km", "40 km", "45 km"], category: "olahraga" },
  { q: "Berat bola basket standar pria?", correct: "B", options: ["567 gram", "600 gram", "650 gram"], category: "olahraga" },
  { q: "Jumlah set dalam bulu tangkis?", correct: "C", options: ["1", "2", "3"], category: "olahraga" },
  { q: "Tinggi ring basket dari lantai?", correct: "A", options: ["3,05 meter", "3,10 meter", "2,95 meter"], category: "olahraga" },
  { q: "Durasi pertandingan futsal?", correct: "B", options: ["2 x 15 menit", "2 x 20 menit", "2 x 25 menit"], category: "olahraga" },
  { q: "Jumlah pemain dalam tim voli?", correct: "C", options: ["5", "6", "7"], category: "olahraga" },
  { q: "Skor maksimal dalam tenis meja?", correct: "A", options: ["11", "15", "21"], category: "olahraga" },
  { q: "Jarak lempar cakram putra?", correct: "B", options: ["2 kg", "2,135 kg", "2,5 kg"], category: "olahraga" },
  { q: "Durasi istirahat antar babak bola basket?", correct: "C", options: ["1 menit", "1,5 menit", "2 menit"], category: "olahraga" },
  { q: "Jumlah wasit dalam pertandingan sepak bola?", correct: "A", options: ["4", "5", "6"], category: "olahraga" },
  { q: "Panjang kolam renang olimpiade?", correct: "B", options: ["25 meter", "50 meter", "100 meter"], category: "olahraga" },
  { q: "Berat bola voli standar?", correct: "C", options: ["250-270 gram", "260-280 gram", "270-290 gram"], category: "olahraga" },
  { q: "Jumlah ronde dalam tinju amatir?", correct: "A", options: ["3", "4", "5"], category: "olahraga" },
  { q: "Tinggi net bulu tangkis putra?", correct: "B", options: ["1,524 meter", "1,55 meter", "1,60 meter"], category: "olahraga" },
  { q: "Durasi pertandingan bola tangan?", correct: "C", options: ["2 x 25 menit", "2 x 28 menit", "2 x 30 menit"], category: "olahraga" },
  { q: "Jarak lempar lembing putra?", correct: "A", options: ["800 gram", "900 gram", "1000 gram"], category: "olahraga" },
  { q: "Skor kemenangan dalam bulu tangkis?", correct: "B", options: ["15", "21", "25"], category: "olahraga" },
  { q: "Jumlah pemain dalam tim rugby?", correct: "C", options: ["13", "14", "15"], category: "olahraga" },
  { q: "Durasi pertandingan hoki lapangan?", correct: "A", options: ["2 x 35 menit", "2 x 40 menit", "2 x 45 menit"], category: "olahraga" },
  { q: "Tinggi gawang sepak bola?", correct: "B", options: ["2,40 meter", "2,44 meter", "2,50 meter"], category: "olahraga" },
  { q: "Jarak lompat jangkit?", correct: ["20 meter", "25 meter", "30 meter"], category: "olahraga" },
  { q: "Berat cakram putri?", correct: "C", options: ["0,5 kg", "0,75 kg", "1 kg"], category: "olahraga" },
  { q: "Durasi pertandingan polo air?", correct: "A", options: ["4 x 8 menit", "4 x 10 menit", "4 x 12 menit"], category: "olahraga" },
  { q: "Jumlah pemain dalam tim softball?", correct: "B", options: ["8", "9", "10"], category: "olahraga" },
  { q: "Tinggi net tenis?", correct: "C", options: ["0,914 meter", "1,0 meter", "1,07 meter"], category: "olahraga" }
];

// === FUNGSI UTILITAS ===
function shuffle(array) {
  return [...array].sort(() => Math.random() - 0.5);
}

function updateChar1() {
  if (char1) {
    char1.style.backgroundImage = isHandUp1 ? `url("${imgHandUp1}")` : `url("${imgNormal1}")`;
    char1.style.transform = isHandUp1 ? "translateY(-10px)" : "translateY(0)";
  }
}

function updateChar2() {
  if (char2) {
    char2.style.backgroundImage = isHandUp2 ? `url("${imgHandUp2}")` : `url("${imgNormal2}")`;
    char2.style.transform = isHandUp2 ? "translateY(-10px)" : "translateY(0)";
  }
}

// === FUNGSI KATEGORI ===
function getSelectedCategory() {
  const selectedRadio = document.querySelector('input[name="category"]:checked');
  return selectedRadio ? selectedRadio.value : "semua";
}

function getCategoryName(categoryValue) {
  const names = {
    "bahasa_indonesia": "📖 Bahasa Indonesia",
    "matematika": "➗ Matematika",
    "bahasa_inggris": "🔤 Bahasa Inggris",
    "kimia": "🧪 Kimia",
    "fisika": "⚡ Fisika",
    "umum": "🌍 Umum",
    "astronomi": "🪐 Astronomi",
    "sejarah_indonesia": "📜 Sejarah Indonesia",
    "sosial": "🗺️ Sosial",
    "biologi": "🧬 Biologi",
    "olahraga": "⚽ Olahraga",
    "semua": "🎲 Semua Soal"
  };
  return names[categoryValue] || categoryValue;
}

function filterQuestionsByCategory(questions, category) {
  if (category === "semua") {
    return [...questions];
  }
  return questions.filter(q => q.category === category);
}

// === FUNGSI AUDIO ===
function playSound(audioElement) {
  if (isSoundEnabled && audioElement) {
    audioElement.currentTime = 0;
    audioElement.play().catch(e => {});
  }
}

function toggleSound() {
  isSoundEnabled = soundToggle.checked;
}

function toggleMusic() {
  isMusicEnabled = musicToggle.checked;
  
  if (isMusicEnabled) {
    if (!isPaused) {
      backgroundMusic.play().catch(() => {});
    } else {
      wasMusicPlayingBeforePause = true;
    }
  } else {
    backgroundMusic.pause();
    wasMusicPlayingBeforePause = false;
  }
}

// === PAUSE FUNCTIONALITY ===
function pauseGame() {
  if (isPaused || timeLeft <= 0) return;
  
  isPaused = true;
  if (pauseOverlay) pauseOverlay.style.display = 'flex';
  if (pauseMenu) pauseMenu.style.display = 'block';
  if (controlsDisplay) controlsDisplay.style.display = 'none';
  
  clearInterval(timerInterval);
  pausedTime = timeLeft;
  
  if (isMusicEnabled && backgroundMusic) {
    backgroundMusic.pause();
  }
  
  if (answerTimer) {
    clearTimeout(answerTimer);
    answerTimer = null;
  }
}

function resumeGame() {
  if (!isPaused) return;
  
  isPaused = false;
  if (pauseOverlay) pauseOverlay.style.display = 'none';
  
  timeLeft = pausedTime;
  startTimer();
  
  if (isMusicEnabled && backgroundMusic.paused) {
    backgroundMusic.play().catch(() => {});
  }
}

// === TIMER ===
function startTimer() {
  clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    if (isPaused) return;
    
    timeLeft--;
    const mins = Math.floor(timeLeft / 60);
    const secs = timeLeft % 60;
    if (timerDisplay) {
      timerDisplay.textContent = `Waktu: ${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }
    
    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      endGameByTime();
    }
  }, 1000);
}

function endGameByTime() {
  showFinalResult(true);
}

// === SOAL ===
function loadNextQuestion() {
  // CEK APAKAH MASIH ADA SOAL TERSISA
  if (remainingQuestions.length === 0 || timeLeft <= 0) {
    showFinalResult(false); // false = semua soal selesai, bukan waktu habis
    return;
  }

  remainingQuestions = shuffle(remainingQuestions);
  currentQuestion = remainingQuestions.pop();

  if (questionText) questionText.textContent = currentQuestion.q;
  if (questionOptions) {
    questionOptions.innerHTML = `
      A. ${currentQuestion.options[0]}<br>
      B. ${currentQuestion.options[1]}<br>
      C. ${currentQuestion.options[2]}
    `;
  }
  if (activeQuestion) activeQuestion.style.display = "block";

  isHandUp1 = false;
  isHandUp2 = false;
  handRaisedBy = null;
  hasAnsweredThisQuestion = false;
  isQuestionLocked = false;
  if (answerTimer) clearTimeout(answerTimer);
  updateChar1();
  updateChar2();
}

// === CHECK ANSWER DENGAN POPUP FEEDBACK ===
function checkAnswer(player, choice) {
  if (isPaused || isFeedbackActive || !currentQuestion || timeLeft <= 0 || handRaisedBy !== player) {
    return;
  }

  if (answerTimer) {
    clearTimeout(answerTimer);
    answerTimer = null;
  }

  const correct = currentQuestion.correct;
  const isCorrect = (choice === correct);
  const playerName = player === 1 ? playerName1 : playerName2;
  const playerChoiceIndex = ["A", "B", "C"].indexOf(choice);
  const playerChoice = playerChoiceIndex >= 0 ? 
    currentQuestion.options[playerChoiceIndex] : "Jawaban tidak valid";

  // Ambil elemen modal
  const modal = document.getElementById('autoFeedbackModal');
  const title = document.getElementById('autoFeedbackTitle');
  const msg = document.getElementById('autoFeedbackMessage');

  if (isCorrect) {
    title.textContent = "✅ BENAR!";
    title.style.color = "#2ecc71";
    msg.innerHTML = `<strong>${playerName}</strong> menjawab:<br><em>"${playerChoice}"</em>`;
    
    // Tambah skor
    if (player === 1) {
      score1++;
      if (score1El) score1El.textContent = score1;
    } else {
      score2++;
      if (score2El) score2El.textContent = score2;
    }
    playSound(document.getElementById('rewardSound'));
  } else {
    const correctIndex = ["A", "B", "C"].indexOf(correct);
    const correctAnswer = correctIndex >= 0 ? currentQuestion.options[correctIndex] : "Jawaban benar tidak tersedia";
    title.textContent = "❌ SALAH!";
    title.style.color = "#e74c3c";
    msg.innerHTML = `<strong>${playerName}</strong> menjawab:<br><em>"${playerChoice}"</em><br><br>Jawaban yang benar adalah:<br><strong>"${correctAnswer}"</strong>`;
    playSound(document.getElementById('wrongSound'));
  }

  // Tampilkan modal
  modal.style.display = 'flex';
  isFeedbackActive = true;

  // Tutup otomatis setelah 2 detik
  setTimeout(() => {
    modal.style.display = 'none';
    isFeedbackActive = false;
    finishCurrentQuestion();
  }, 2000); // 2000 ms = 2 detik
}

function finishCurrentQuestion() {
  currentQuestion = null;
  if (activeQuestion) activeQuestion.style.display = "none";
  isHandUp1 = false;
  isHandUp2 = false;
  handRaisedBy = null;
  hasAnsweredThisQuestion = false;
  isQuestionLocked = false;
  if (answerTimer) clearTimeout(answerTimer);
  answerTimer = null;
  updateChar1();
  updateChar2();

  setTimeout(() => {
    if (timeLeft > 0 && !isPaused) {
      loadNextQuestion(); // Ini akan otomatis menampilkan hasil jika tidak ada soal lagi
    }
  }, 100);
}

function startAnswerTimer(player) {
  answerTimer = setTimeout(() => {
    if (!isPaused) {
      finishCurrentQuestion();
    }
  }, 3000);
}

// === SKIP DENGAN PEMERIKSAAN KUNCI ===
function skipQuestion() {
  if (isPaused || isFeedbackActive || !currentQuestion || timeLeft <= 0 || isQuestionLocked) {
    return;
  }
  if (answerTimer) clearTimeout(answerTimer);
  finishCurrentQuestion();
}

function showFinalResult(isTimeUp) {
  clearInterval(timerInterval);
  if (answerTimer) clearTimeout(answerTimer);
  if (activeQuestion) activeQuestion.style.display = "none";
  
  if (backgroundMusic) {
    backgroundMusic.pause();
  }

  playSound(document.getElementById('gameOverSound'));

  let message = "";
  if (score1 > score2) {
    message = `Selamat! 🎉\n${playerName1} menang karena memiliki skor tertinggi!`;
  } else if (score2 > score1) {
    message = `Selamat! 🎉\n${playerName2} menang karena memiliki skor tertinggi!`;
  } else {
    message = `Seri! 🤝\n${playerName1} dan ${playerName2} memiliki skor yang sama!`;
  }

  if (isTimeUp) {
    if (finalScoreText) {
      finalScoreText.innerHTML = `<strong>WAKTU HABIS!</strong><br><strong>Skor Akhir:</strong><br>${playerName1}: ${score1}<br>${playerName2}: ${score2}<br><br>${message}`;
    }
    if (timeUpModal) timeUpModal.style.display = "block";
  } else {
    if (rewardText) rewardText.textContent = message;
    if (rewardModal) rewardModal.style.display = "block";
  }
}

// === JOYSTICK HANDLING ===
function handleGamepadInput() {
  if (!gamepadConnected || timeLeft <= 0 || isFeedbackActive || isPaused) return;

  const gamepads = navigator.getGamepads();
  const now = Date.now();

  const connectedCount = gamepads.filter(gp => gp !== null).length;
  useDualJoysticks = connectedCount >= 2;

  if (!isQuestionLocked) {
    if (gamepads[0] && gamepads[0].buttons[4] && gamepads[0].buttons[4].pressed) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        skipQuestion();
        lastInputTime = now;
        return;
      }
    }
    if (useDualJoysticks && gamepads[1] && gamepads[1].buttons[5] && gamepads[1].buttons[5].pressed) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        skipQuestion();
        lastInputTime = now;
        return;
      }
    }
  }

  if (!currentQuestion) return;

  const gp1 = gamepads[0];
  if (gp1) {
    if (gp1.buttons[0] && gp1.buttons[0].pressed && handRaisedBy === null) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        isQuestionLocked = true;
        isHandUp1 = true;
        handRaisedBy = 1;
        updateChar1();
        updateChar2();
        startAnswerTimer(1);
        lastInputTime = now;
      }
    }
    if (handRaisedBy === 1 && !hasAnsweredThisQuestion) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        if (gp1.buttons[2] && gp1.buttons[2].pressed) { checkAnswer(1, 'A'); hasAnsweredThisQuestion = true; lastInputTime = now; }
        else if (gp1.buttons[3] && gp1.buttons[3].pressed) { checkAnswer(1, 'B'); hasAnsweredThisQuestion = true; lastInputTime = now; }
        else if (gp1.buttons[1] && gp1.buttons[1].pressed) { checkAnswer(1, 'C'); hasAnsweredThisQuestion = true; lastInputTime = now; }
      }
    }
  }

  if (useDualJoysticks && gamepads[1]) {
    const gp2 = gamepads[1];
    if (gp2.buttons[0] && gp2.buttons[0].pressed && handRaisedBy === null) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        isQuestionLocked = true;
        isHandUp2 = true;
        handRaisedBy = 2;
        updateChar1();
        updateChar2();
        startAnswerTimer(2);
        lastInputTime = now;
      }
    }
    if (handRaisedBy === 2 && !hasAnsweredThisQuestion) {
      if (now - lastInputTime > INPUT_COOLDOWN) {
        if (gp2.buttons[2] && gp2.buttons[2].pressed) { checkAnswer(2, 'A'); hasAnsweredThisQuestion = true; lastInputTime = now; }
        else if (gp2.buttons[3] && gp2.buttons[3].pressed) { checkAnswer(2, 'B'); hasAnsweredThisQuestion = true; lastInputTime = now; }
        else if (gp2.buttons[1] && gp2.buttons[1].pressed) { checkAnswer(2, 'C'); hasAnsweredThisQuestion = true; lastInputTime = now; }
      }
    }
  }
}

// === KEYBOARD CONTROL ===
document.addEventListener("keydown", function(event) {
  if (timeLeft <= 0 || isFeedbackActive || gamepadConnected || isPaused) return;

  const key = event.key.toLowerCase();

  if (event.key === "Escape") {
    pauseGame();
    return;
  }

  if ((key === ' ' || event.code === 'Space') && !isQuestionLocked) {
    event.preventDefault();
    skipQuestion();
    return;
  }

  if (currentQuestion && handRaisedBy === null) {
    if (event.code === "ShiftLeft") {
      event.preventDefault();
      isQuestionLocked = true;
      isHandUp1 = true;
      handRaisedBy = 1;
      updateChar1();
      updateChar2();
      startAnswerTimer(1);
    } else if (event.code === "ShiftRight") {
      event.preventDefault();
      isQuestionLocked = true;
      isHandUp2 = true;
      handRaisedBy = 2;
      updateChar2();
      updateChar1();
      startAnswerTimer(2);
    }
  }

  if (handRaisedBy === 1) {
    if (key === 'a') { checkAnswer(1, 'A'); return; }
    if (key === 's') { checkAnswer(1, 'B'); return; }
    if (key === 'd') { checkAnswer(1, 'C'); return; }
  } else if (handRaisedBy === 2) {
    if (key === 'j') { checkAnswer(2, 'A'); return; }
    if (key === 'k') { checkAnswer(2, 'B'); return; }
    if (key === 'l') { checkAnswer(2, 'C'); return; }
  }
});

// === GAMEPAD DETECTION ===
function updateGamepadState() {
  const gamepads = navigator.getGamepads();
  const connected = gamepads.some(gp => gp !== null);
  gamepadConnected = connected;
}

window.addEventListener("gamepadconnected", (e) => {
  updateGamepadState();
});

window.addEventListener("gamepaddisconnected", (e) => {
  updateGamepadState();
});

// === GAME LOOP ===
function gameLoop() {
  if (!isPaused) {
    handleGamepadInput();
  }
  requestAnimationFrame(gameLoop);
}
gameLoop();

// === FUNGSI RESET GAME ===
function resetGameToMainMenu() {
  clearInterval(timerInterval);
  if (answerTimer) clearTimeout(answerTimer);
  isPaused = false;
  
  score1 = 0;
  score2 = 0;
  timeLeft = 60;
  
  isHandUp1 = false;
  isHandUp2 = false;
  
  currentQuestion = null;
  remainingQuestions = [];
  handRaisedBy = null;
  hasAnsweredThisQuestion = false;
  isQuestionLocked = false;
  isFeedbackActive = false;
  
  isSoundEnabled = true;
  isMusicEnabled = true;
  
  if (score1El) score1El.textContent = "0";
  if (score2El) score2El.textContent = "0";
  
  if (soundToggle) {
    soundToggle.checked = true;
    isSoundEnabled = true;
  }
  if (musicToggle) {
    musicToggle.checked = true;
    isMusicEnabled = true;
  }
  
  updateChar1();
  updateChar2();
  
  if (rewardModal) rewardModal.style.display = "none";
  if (timeUpModal) timeUpModal.style.display = "none";
  if (pauseOverlay) pauseOverlay.style.display = 'none';
  if (activeQuestion) activeQuestion.style.display = "none";
  if (feedbackOverlay) {
    feedbackOverlay.style.opacity = '0';
    feedbackOverlay.style.pointerEvents = 'none';
  }
  
  if (backgroundMusic) {
    backgroundMusic.pause();
    backgroundMusic.currentTime = 0;
  }
  
  if (gameContainer) gameContainer.style.display = 'none';
  if (introScreen) introScreen.style.display = 'flex';
}

// === EVENT LISTENERS ===
if (pauseButton) pauseButton.addEventListener('click', pauseGame);
if (resumeButton) resumeButton.addEventListener('click', resumeGame);

if (showControlsButton) showControlsButton.addEventListener('click', () => {
  if (pauseMenu) pauseMenu.style.display = 'none';
  if (controlsDisplay) controlsDisplay.style.display = 'block';
});

if (backFromControls) backFromControls.addEventListener('click', () => {
  if (controlsDisplay) controlsDisplay.style.display = 'none';
  if (pauseMenu) pauseMenu.style.display = 'block';
});

if (mainMenuButton) mainMenuButton.addEventListener('click', resetGameToMainMenu);
if (soundToggle) soundToggle.addEventListener('change', toggleSound);
if (musicToggle) musicToggle.addEventListener('change', toggleMusic);

// === START BUTTON ===
if (startButton) startButton.addEventListener('click', () => {
  selectedCategory = getSelectedCategory();
  
  playerName1 = player1Input.value.trim() || "Player 1";
  playerName2 = player2Input.value.trim() || "Player 2";
  
  if (document.getElementById('player1Label')) {
    document.getElementById('player1Label').textContent = playerName1;
  }
  if (document.getElementById('player2Label')) {
    document.getElementById('player2Label').textContent = playerName2;
  }

  updateGamepadState();

  if (introScreen) introScreen.style.display = 'none';
  if (countdownOverlay) countdownOverlay.style.display = 'flex';

  setTimeout(() => {
    if (countdownText) countdownText.textContent = "Mulai!";
    setTimeout(() => {
      if (countdownOverlay) countdownOverlay.style.display = 'none';
      if (gameContainer) gameContainer.style.display = 'block';
      
      if (isMusicEnabled && backgroundMusic) {
        backgroundMusic.play().catch(() => {
          if (musicToggle) {
            musicToggle.checked = false;
            isMusicEnabled = false;
          }
        });
      }
      
      remainingQuestions = filterQuestionsByCategory([...allQuestions], selectedCategory);
      
      if (remainingQuestions.length === 0) {
        remainingQuestions = [...allQuestions];
      }

      score1 = 0;
      score2 = 0;
      if (score1El) score1El.textContent = "0";
      if (score2El) score2El.textContent = "0";
      timeLeft = 60;
      if (timerDisplay) timerDisplay.textContent = "Waktu: 1:00";
      
      if (categoryBadge) {
        categoryBadge.textContent = getCategoryName(selectedCategory);
      }

      startTimer();
      loadNextQuestion();
    }, 1000);
  }, 1000);
});

// === MODAL CLOSE ===
if (closeReward) closeReward.addEventListener("click", resetGameToMainMenu);
if (closeTimeUp) closeTimeUp.addEventListener("click", resetGameToMainMenu);

// Inisialisasi karakter
updateChar1();
updateChar2();

// Set default sound settings
if (soundToggle) soundToggle.checked = isSoundEnabled;
if (musicToggle) musicToggle.checked = isMusicEnabled;

// === SCROLL HORIZONTAL DENGAN TOUCHPAD + MOUSE WHEEL ===
document.addEventListener('DOMContentLoaded', function() {
  const categoriesContainer = document.querySelector('.categories');
  if (!categoriesContainer) return;

  categoriesContainer.addEventListener('wheel', (e) => {
    if (categoriesContainer.scrollWidth <= categoriesContainer.clientWidth) return;
    if (e.deltaX !== 0) return;
    if (e.deltaY !== 0) {
      e.preventDefault();
      categoriesContainer.scrollLeft += e.deltaY;
    }
  }, { passive: false });
});