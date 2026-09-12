<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Alihkan ke halaman login jika belum login
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Penghafal Alquran - Homepage</title>

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
    }

    nav {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--color-text-primary);
      user-select: none;
      cursor: default;
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

    .btn-primary {
      display: inline-block;
      padding: 1rem 2.5rem;
      font-size: 1.125rem;
      font-weight: 700;
      background-color: var(--color-accent);
      color: white;
      border-radius: var(--border-radius);
      box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
      cursor: pointer;
      transition: background-color var(--transition-speed), box-shadow var(--transition-speed);
      user-select: none;
      border: none;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .btn-primary:hover,
    .btn-primary:focus {
      background-color: var(--color-accent-hover);
      box-shadow: 0 12px 36px rgba(29, 78, 216, 0.6);
      outline: none;
    }

    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
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
      font-size: 1.25rem;
      letter-spacing: 0.02em;
    }

    footer {
      padding: 2rem 1rem;
      text-align: center;
      color: var(--color-text-secondary);
      font-size: 0.875rem;
      user-select: none;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
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
      <div class="logo" tabindex="0">Penghafal Alquran</div>
    </nav>
  </header>

  <main>
    <h1 class="hero-title" tabindex="0">Mulai Perjalanan Menghafal Alquran Anda</h1>
    <p class="hero-subtitle" tabindex="0">
      Aplikasi sederhana dan futuristik untuk membantu Anda mencatat hafalan dan menguji pengetahuan Alquran.
    </p>
    
    <div class="menu-grid" role="list">
      <a href="hafalan.php" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Hafalan - Klik untuk mulai mencatat hafalan">
        <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
          <rect x="12" y="10" width="40" height="44" rx="6" ry="6" />
          <path d="M20 20h24v4H20zM20 30h24v4H20zM20 40h24v4H20z" />
          <circle cx="32" cy="54" r="4" />
        </svg>
        <span class="menu-label">Hafalan</span>
      </a>

      <a href="kuis.php" class="menu-card" role="listitem" tabindex="0" aria-label="Menu Kuis - Klik untuk mulai kuis Alquran">
        <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
          <circle cx="32" cy="32" r="28" />
          <path d="M24 38l12-6-12-6v12z" fill="none" stroke-width="2" stroke-linejoin="round" />
        </svg>
        <span class="menu-label">Kuis</span>
      </a>
    </div>
  </main>

  <footer>
    &copy; 2025 Penghafal Alquran — Dibuat dengan semangat edukasi dan teknologi.
  </footer>

</body>
</html>
