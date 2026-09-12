<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Memo Qur'an - Homepage</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=KFGQPC+Hafs+Uthmanic+Script&display=swap');

    :root {
      --color-bg: #eafaf1; /* Background nuansa Islami */
      --color-text-primary: #111827;
      --color-text-secondary: #6b7280;
      --color-accent: #4CAF50; /* Hijau tua */
      --color-accent-hover: #388E3C; /* Hijau lebih gelap */
      --border-radius: 0.75rem;
      --shadow-light: rgba(0,0,0,0.05);
      --shadow-medium: rgba(0,0,0,0.1);
      --font-family: 'Poppins', sans-serif;
      --font-arabic: 'KFGQPC+Hafs+Uthmanic+Script', serif;
      --transition-speed: 0.3s;
    }

    /* Reset and base */
    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      background-color: var(--color-bg);
      color: var(--color-text-secondary);
      font-family: var(--font-family);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    a {
      color: inherit;
      text-decoration: none;
      cursor: pointer;
    }

    header {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: var(--color-bg);
      box-shadow: 0 1px 5px var(--shadow-light);
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--color-text-primary);
      user-select: none;
      cursor: default;
    }

    .logout-button {
      padding: 0.5rem 1.5rem;
      font-size: 1rem;
      font-weight: 600;
      background-color: red;
      color: white;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: background-color var(--transition-speed), transform var(--transition-speed);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .logout-button:hover {
      background-color: var(--color-accent-hover);
      transform: translateY(-2px);
    }

    main {
      flex: 1;
      max-width: 1200px;
      margin: 4rem auto 6rem auto;
      width: 90%;
      text-align: center;
    }

    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      color: var(--color-text-primary);
      margin-bottom: 0.5rem;
      line-height: 1.1;
    }

    .hero-subtitle {
      font-size: 1.25rem;
      max-width: 550px;
      margin: 0 auto 3rem auto;
      color: var(--color-text-secondary);
      font-weight: 500;
    }

    .menu-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr); /* Dua kolom untuk tampilan desktop */
      gap: 3rem;
      margin-top: 3rem;
      justify-items: center;
    }

    .menu-card {
      background: var(--color-bg);
      box-shadow: 0 8px 24px var(--shadow-light);
      border-radius: var(--border-radius);
      padding: 2.5rem 2rem;
      width: 220px;
      user-select: none;
      transition: transform var(--transition-speed), box-shadow var(--transition-speed);
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--color-text-primary);
      cursor: pointer;
      text-align: center;
    }

    .menu-card:hover,
    .menu-card:focus {
      transform: translateY(-6px);
      box-shadow: 0 14px 40px rgba(37, 99, 235, 0.3);
      outline: none;
      color: var(--color-accent);
      text-decoration: none;
    }

    .menu-icon {
      width: 80px;
      height: 80px;
      margin-bottom: 1.25rem;
      stroke: var(--color-text-secondary);
      stroke-width: 1.5;
      fill: none;
      transition: stroke var(--transition-speed);
      flex-shrink: 0;
    }

    .menu-card:hover .menu-icon,
    .menu-card:focus .menu-icon {
      stroke: var(--color-accent);
    }

    .menu-label {
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: 0.02em;
    }

    footer {
      padding: 2rem 1rem;
      text-align: center;
      color: var(--color-text-secondary);
      font-size: 0.875rem;
      user-select: none;
    }

    /* Modal Styles */
    .modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1000; /* Sit on top */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    .modal-content {
      background-color: #fefefe;
      margin: 15% auto; /* 15% from the top and centered */
      padding: 20px;
      border: 1px solid #888;
      width: 80%; /* Could be more or less, depending on screen size */
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    /* Tombol Download Styles */
    .download-button {
      display: inline-block;
      background-color: var(--color-accent);
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: var(--border-radius);
      font-weight: bold;
      transition: background-color var(--transition-speed);
      margin: 30px 0;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .download-button:hover {
      background-color: var(--color-accent-hover);
      transform: translateY(-2px);
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
      .menu-grid {
        grid-template-columns: repeat(2, 1fr); /* Dua kolom untuk tampilan smartphone */
      }
      .hero-title {
        font-size: 2.5rem;
      }
      .menu-card {
        width: 100%;
        max-width: 320px;
        padding: 2rem 1.5rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <nav role="navigation" aria-label="Main navigation">
      <div class="logo" tabindex="0">Memo Qur'an</div>
      <form action="logout.php" method="post">
        <button type="submit" class="logout-button">Logout</button>
      </form>
    </nav>
  </header>

  <main>
    <img src="logodepanbaru.png" alt="Gambar FKIP" style="max-width: 100%; height: auto; margin-bottom: 20px;" />
        
    <h2 class="hero-title" tabindex="0">Mulai Perjalanan Menghafal Alquran Anda</h2>
    <p class="hero-subtitle" tabindex="0">
      Memo Qur'an: Merupakan Hasil Penelitian yang didanai oleh DPPM Bima Kemdiktisaintek 2025. Dengan mengembangkan inovasi media Tahsin Tahfidz Yang dilakukan oleh Tim Dosen Peneliti dari Universitas Nahdlatul Ulama Sunan Giri. Aya Mamlu'ah, S.Sos.I., M.P.d.I (Ketua) dan Ulva Badi' Rohmawati (Anggota).
    </p>
    
    <!-- Tombol Download -->
    <a href="download.php" class="download-button">Download Aplikasi Memo Qur'an</a>
    
    <div class="menu-grid" role="list">
      <a href="hafalan.php" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Hafalan - Klik untuk mulai mencatat hafalan">
        <img src="https://memoquran.my.id/logo_menghafal.png" alt="Ikon Hafalan" class="menu-icon" style="width: 100px; height: 100px; margin-bottom: 1.25rem;" />
        <span class="menu-label">Hafalan</span>
      </a>

      <a href="game.php" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Kuis - Klik untuk mulai kuis Alquran">
        <img src="https://memoquran.my.id/logo_kuis.png" alt="Ikon Hafalan" class="menu-icon" style="width: 100px; height: 100px; margin-bottom: 1.25rem;" />
        <span class="menu-label">Kuis</span>
      </a>

      <a href="E-Learning.php" class="menu-card" role="listitem" tabindex="0" aria-label="Menu E-Learning - Klik untuk belajar tajwid">
        <img src="https://memoquran.my.id/logo_e_learning.png" alt="Ikon Hafalan" class="menu-icon" style="width: 100px; height: 100px; margin-bottom: 1.25rem;" />
        <span class="menu-label">E-Learning</span>
      </a>

      <a href="https://tanzil.net/" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Al-Qur'an - Klik untuk mengakses Al-Qur'an online">
        <img src="https://memoquran.my.id/logo_quran.png" alt="Ikon Hafalan" class="menu-icon" style="width: 100px; height: 100px; margin-bottom: 1.25rem;" />
        <span class="menu-label">Al-Qur'an</span>
      </a>

      <a href="#" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Tutorial - Klik untuk melihat tutorial" id="tutorial-button">
        <img src="https://memoquran.my.id/logo_tutorial.png" alt="Ikon Tutorial" class="menu-icon" style="width: 100px; height: 100px; margin-bottom: 1.25rem;" />
        <span class="menu-label">Tutorial</span>
      </a>
    </div>
  </main>

  <footer>
    &copy; 2025 Memo Qur'an — Dibuat dengan semangat edukasi dan teknologi.
  </footer>

  <!-- Modal untuk Video YouTube -->
  <div id="videoModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <iframe id="youtube-video" width="100%" height="315" src="https://www.youtube.com/embed/0iYqiO4iKEM" frameborder="0" allowfullscreen></iframe>
    </div>
  </div>

  <script>
    // Mendapatkan modal
    var modal = document.getElementById("videoModal");

    // Mendapatkan tombol yang membuka modal
    var btn = document.getElementById("tutorial-button");

    // Mendapatkan elemen <span> yang menutup modal
    var span = document.getElementsByClassName("close")[0];

    // Ketika pengguna mengklik tombol, buka modal
    btn.onclick = function() {
      modal.style.display = "block";
    }

    // Ketika pengguna mengklik <span> (x), tutup modal dan hentikan video
    span.onclick = function() {
      modal.style.display = "none";
      document.getElementById("youtube-video").src = "https://www.youtube.com/embed/0iYqiO4iKEM"; // Hentikan video
    }

    // Ketika pengguna mengklik di luar modal, tutup modal dan hentikan video
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
        document.getElementById("youtube-video").src = "https://www.youtube.com/embed/0iYqiO4iKEM"; // Hentikan video
      }
    }
  </script>
</body>
</html>