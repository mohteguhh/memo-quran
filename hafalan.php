<?php
session_start(); // Memastikan sesi dimulai

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Konfigurasi database
$host = 'localhost';
$db = 'memf3243_penghafal_alquran';
$user = 'memf3243';
$pass = 'jWNxKV6GdcqE87';

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];
$bookmarks = [];

// Query untuk mengambil bookmark berdasarkan user_id
$sql = "SELECT surah_number, ayat_number FROM bookmarks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil hasil dan simpan ke dalam array
while ($row = $result->fetch_assoc()) {
    $bookmarks[] = $row;
}

// Simpan bookmark ke dalam JavaScript
echo "<script>var bookmarks = " . json_encode($bookmarks) . ";</script>";

// Tutup koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Hafalan Alquran</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet" />
    <style>
        :root {
            --color-bg: #eafaf1; /* Background nuansa Islami */
            --color-text-primary: #333;
            --color-text-secondary: #555;
            --color-primary: #4CAF50; /* Hijau tua */
            --color-primary-hover: #388E3C; /* Hijau lebih gelap */
            --color-card-bg: #ffffff;
            --color-radius: 0.5rem;
            --color-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --color-transition: 0.3s ease-in-out;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            --font-arabic: 'Amiri', serif; /* Mengganti font Arabic dengan Amiri */
        }

        /* Reset */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: var(--font-sans);
            background-color: var(--color-bg);
            color: var(--color-text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2rem;
        }

        header {
            background: var(--color-primary);
            color: white;
            padding: 1rem;
            text-align: center;
            border-radius: var(--color-radius);
            box-shadow: var(--color-shadow);
            margin-bottom: 2rem;
            position: relative; /* Tambahkan posisi relatif untuk penempatan tombol */
        }

        h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .lead {
            font-size: 1.125rem;
            margin: 0.5rem 0 1.5rem;
        }

        .logout-button {
            background: red; /* Warna merah untuk tombol Logout */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem 1rem; /* Padding untuk tombol */
            border-radius: var(--color-radius); /* Radius border */
            margin-top: 1rem; /* Jarak atas untuk tombol Logout */
        }

        .home-button {
            background: white; /* Warna putih untuk tombol Beranda */
            color: green; /* Tulisan hijau untuk tombol Beranda */
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem 1rem; /* Padding untuk tombol */
            border-radius: var(--color-radius); /* Radius border */
            margin-top: 1rem; /* Jarak atas untuk tombol Beranda */
        }

        .controls {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .control-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        select, button {
            padding: 0.75rem;
            border-radius: var(--color-radius);
            border: 1px solid #e5e7eb;
            font-size: 1rem;
            transition: border-color var(--color-transition);
        }

        select {
            background-color: #f9f9f9;
            color: var(--color-text-primary);
        }

        select:hover, select:focus {
            border-color: var(--color-primary);
            outline: none;
        }

        button {
            background-color: var(--color-primary);
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color var(--color-transition);
        }

        button:hover {
            background-color: var(--color-primary-hover);
        }

        .card {
            background-color: var(--color-card-bg);
            border-radius: var(--color-radius);
            box-shadow: var(--color-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .ayah-text {
            font-family: var(--font-arabic); /* Menggunakan font Amiri untuk teks ayat */
            font-size: 2.5rem;
            line-height: 2;
            direction: rtl;
            text-align: center;
            color: black; /* Mengubah warna teks menjadi hitam */
            margin: 0 0 1rem;
        }

        .bookmarks-container {
            background: var(--color-card-bg);
            border-radius: var(--color-radius);
            box-shadow: var(--color-shadow);
            padding: 1rem;
        }

        .bookmark-list {
            font-family: var(--font-arabic); /* Menggunakan font Amiri untuk daftar bookmark */
            max-height: 200px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .bookmark-item {
            background: #f9f9f9;
            border-radius: var(--color-radius);
            padding: 0.7rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bookmark-header {
            font-weight: 600;
            color: var(--color-primary);
        }

        .btn-remove-bookmark {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .btn-remove-bookmark img {
            width: 20px; /* Ukuran ikon gambar sampah */
            height: 20px;
        }

        .btn-remove-bookmark:hover {
            opacity: 0.7;
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            h1 {
                font-size: 2rem;
            }
            .ayah-text {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>Hafalan Alquran</h1>
    <p class="lead">Tampilkan ayat Alquran dan pilih berdasarkan Surat atau Ayat.</p>
    <!-- Tombol Beranda dan Logout dipindahkan ke sini -->
    <button class="home-button" onclick="goHome()">Beranda</button>
    <button class="logout-button" onclick="logout()">Logout</button>
</header>
<main>
    
    <section class="controls">
        <div class="control-group">
            <label for="surah-select">Surat</label>
            <select id="surah-select" aria-label="Pilih Surat Alquran">
                <option value="">-- Surat --</option>
            </select>
        </div>
        <div class="control-group">
            <label for="ayat-select">Ayat</label>
            <select id="ayat-select" aria-label="Pilih Ayat" disabled>
                <option value="">-- Ayat --</option>
            </select>
        </div>
    </section>

    <section class="card">
        <h2>Ayat Dipilih</h2>
        <p class="ayah-text" id="selected-ayah-text" lang="ar" aria-live="polite" aria-atomic="true" tabindex="0">Belum memilih ayat.</p>
        <button id="btn-toggle-bookmark" class="btn-bookmark" type="button" aria-pressed="false" disabled>Tandai Bookmark</button>
    </section>

    <section class="bookmarks-container">
        <h2>Bookmark Ayat</h2>
        <div class="bookmark-list" id="bookmark-list" tabindex="0" aria-live="polite" aria-atomic="true">
            <p style="font-style: italic; color: var(--color-text-secondary); padding: 1rem 0;">Belum ada bookmark.</p>
        </div>
    </section>

    
</main>

<script>
    (() => {
        // Elements
        const surahSelect = document.getElementById('surah-select');
        const ayatSelect = document.getElementById('ayat-select');
        const selectedAyahText = document.getElementById('selected-ayah-text');
        const btnToggleBookmark = document.getElementById('btn-toggle-bookmark');
        const bookmarkListEl = document.getElementById('bookmark-list');

        // Data holders
        let surahList = [];
        let bookmarks = [];
        let selectedAyah = null; // {surahNumber, ayatNumber, text}

        // API URLs
        const SURAH_LIST_API = 'https://api.alquran.cloud/v1/surah';
        const AYAH_API = (surahNum, ayahNum) => `https://api.alquran.cloud/v1/ayah/${surahNum}:${ayahNum}/quran-uthmani`;

        // Initialization
        async function init() {
            try {
                const res = await fetch(SURAH_LIST_API);
                const data = await res.json();
                if (data.code === 200 && data.data) {
                    surahList = data.data;
                    populateSurahSelect(surahSelect);
                } else {
                    throw new Error('Gagal memuat daftar surat.');
                }
            } catch {
                alert("Gagal memuat daftar surat, mohon coba muat ulang.");
            }
            loadBookmarks();
            renderBookmarkList();
            resetSelectedAyah();
        }

        // Populate surah selects
        function populateSurahSelect(selectEl) {
            selectEl.innerHTML = '<option value="">-- Surat --</option>';
            surahList.forEach(surah => {
                const opt = document.createElement('option');
                opt.value = surah.number;
                opt.textContent = `${surah.number}. ${surah.englishName} (${surah.name})`;
                selectEl.appendChild(opt);
            });
        }

        // Update ayat select based on selected surah
        surahSelect.addEventListener('change', async (e) => {
            const surahNum = parseInt(e.target.value);
            if (!surahNum) {
                ayatSelect.innerHTML = '<option value="">-- Ayat --</option>';
                ayatSelect.disabled = true;
                return;
            }
            const surah = surahList.find(s => s.number === surahNum);
            if (!surah) {
                ayatSelect.innerHTML = '<option value="">-- Ayat --</option>';
                ayatSelect.disabled = true;
                return;
            }
            ayatSelect.disabled = false;
            ayatSelect.innerHTML = '';
            for (let i = 1; i <= surah.numberOfAyahs; i++) {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = i;
                ayatSelect.appendChild(opt);
            }
            // Automatically load the first ayah
            await loadAndDisplayAyah();
        });

        // Load and display ayah
        async function loadAndDisplayAyah() {
            if (!surahSelect.value || !ayatSelect.value) return;

            const surahNum = Number(surahSelect.value);
            const ayatNum = Number(ayatSelect.value);

            const res = await fetch(AYAH_API(surahNum, ayatNum));
            const data = await res.json();
            if (data.code === 200 && data.data && typeof data.data.text === 'string') {
                selectedAyah = {
                    surahNumber: surahNum,
                    ayatNumber: ayatNum,
                    text: data.data.text
                };
                renderSelectedAyah();
            } else {
                selectedAyahText.textContent = 'Gagal memuat ayat yang dipilih.';
            }
        }

        // Render selected ayah text and bookmark button state
        function renderSelectedAyah() {
            if (!selectedAyah) {
                selectedAyahText.textContent = 'Belum memilih ayat.';
                btnToggleBookmark.disabled = true;
                btnToggleBookmark.setAttribute('aria-pressed', 'false');
                btnToggleBookmark.textContent = 'Tandai Bookmark';
                return;
            }
            selectedAyahText.textContent = selectedAyah.text;
            btnToggleBookmark.disabled = false;
            toggleBookmarkBtnState(isBookmarked(selectedAyah));
        }

        // Reset selected ayah display and button
        function resetSelectedAyah() {
            selectedAyah = null;
            renderSelectedAyah();
        }

        function isBookmarked(ayah) {
            return bookmarks.some(b => b.surahNumber === ayah.surahNumber && b.ayatNumber === ayah.ayatNumber);
        }

        function addBookmark(ayah) {
            if (!isBookmarked(ayah)) {
                bookmarks.push(ayah);
                saveBookmarks();
            }
        }

        function removeBookmark(ayah) {
            bookmarks = bookmarks.filter(b => !(b.surahNumber === ayah.surahNumber && b.ayatNumber === ayah.ayatNumber));
            saveBookmarks();
        }

        function toggleBookmarkBtnState(isOn) {
            btnToggleBookmark.setAttribute('aria-pressed', isOn);
            btnToggleBookmark.textContent = isOn ? 'Hapus Bookmark' : 'Tandai Bookmark';
        }

        btnToggleBookmark.addEventListener('click', () => {
            if (!selectedAyah) return;

            const surahNumber = selectedAyah.surahNumber;
            const ayatNumber = selectedAyah.ayatNumber;

            // Mengirim data ke server untuk menyimpan bookmark
            fetch('save_bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ surah_number: surahNumber, ayat_number: ayatNumber })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.message); // Tampilkan pesan dari server
                // Tambahkan bookmark ke array lokal jika berhasil
                if (data.message === "Bookmark berhasil disimpan!") {
                    addBookmark(selectedAyah);
                }
                renderBookmarkList(); // Perbarui daftar bookmark
            })
            .catch(error => console.error('Error:', error));
        });

        function loadBookmarks() {
            try {
                const stored = localStorage.getItem('hafalanBookmarks');
                bookmarks = stored ? JSON.parse(stored) : [];
            } catch {
                bookmarks = [];
            }
        }

        function saveBookmarks() {
            localStorage.setItem('hafalanBookmarks', JSON.stringify(bookmarks));
        }

        // Render bookmark list panel
        function renderBookmarkList() {
            bookmarkListEl.innerHTML = '';
            if (bookmarks.length === 0) {
                bookmarkListEl.innerHTML = '<p style="font-style: italic; color: var(--color-text-secondary); padding: 1rem 0;">Belum ada bookmark.</p>';
                return;
            }
            bookmarks.forEach(bm => {
                const item = document.createElement('div');
                item.className = 'bookmark-item';

                const header = document.createElement('div');
                header.className = 'bookmark-header';

                const title = document.createElement('span');
                title.textContent = `${bm.surahNumber}. ${getSurahName(bm.surahNumber)} : Ayat ${bm.ayatNumber}`;
                title.addEventListener('click', () => {
                    selectedAyah = bm;
                    renderSelectedAyah();
                });

                const btnDel = document.createElement('button');
                btnDel.className = 'btn-remove-bookmark';
                btnDel.setAttribute('aria-label', `Hapus bookmark Surat ${getSurahName(bm.surahNumber)} Ayat ${bm.ayatNumber}`);
                btnDel.type = 'button';
                btnDel.innerHTML = '<img src="https://img.icons8.com/ios-filled/50/000000/trash.png" alt="Hapus" />'; // Ganti dengan ikon gambar sampah
                btnDel.addEventListener('click', () => {
                    removeBookmark(bm);
                    renderBookmarkList();
                    if (selectedAyah && selectedAyah.surahNumber === bm.surahNumber && selectedAyah.ayatNumber === bm.ayatNumber) {
                        toggleBookmarkBtnState(isBookmarked(selectedAyah));
                    }
                });

                header.appendChild(title);
                header.appendChild(btnDel);

                item.appendChild(header);
                bookmarkListEl.appendChild(item);
            });
        }

        function getSurahName(surahNumber) {
            const s = surahList.find(s => s.number === surahNumber);
            return s ? `${s.englishName} (${s.name})` : surahNumber;
        }

        init();

        // Load ayah when ayat is selected
        ayatSelect.addEventListener('change', loadAndDisplayAyah);

        // Logout function
        window.logout = function() {
            // Hapus sesi pengguna
            window.location.href = 'login.php'; // Arahkan ke halaman login
        };

        // Go to home function
       
        window.goHome = function() {
            window.location.href = 'index.php'; // Arahkan ke halaman beranda
        };
    })();
</script>
</body>
</html>
