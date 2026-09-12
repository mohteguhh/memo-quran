<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Learning Tajwid</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=KFGQPC+Uthman+Taha+Naskh&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #4CAF50; /* Hijau tua */
            color: white;
            padding: 1rem 0;
            text-align: center;
            position: relative;
        }

        .logout-button, .home-button {
            position: center;
            right: 20px;
            top: 15px;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-button {
            background-color: #f44336; /* Merah */
            color: white;
        }

        .logout-button:hover {
            background-color: #d32f2f; /* Merah lebih gelap */
        }

        .home-button {
            background-color: white; /* Putih */
            color: #4CAF50; /* Hijau tua */
            right: 100px; /* Menggeser tombol home ke kiri dari tombol logout */
        }

        .home-button:hover {
            background-color: #eafaf1; /* Warna latar belakang saat hover */
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .card {
            background: #eafaf1; /* Background nuansa Islami */
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        h2 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #4CAF50; /* Hijau tua */
        }

        p {
            line-height: 1.6;
        }

        .arabic {
            font-family: 'KFGQPC Uthman Taha Naskh', serif;
            font-size: 1.5rem;
            color: #4CAF50; /* Hijau tua */
        }

        footer {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem 0;
            background-color: #4CAF50; /* Hijau tua */
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>E-Learning Tajwid</h1>
        <form action="index.php" method="get" style="display: inline;">
            <button type="submit" class="home-button">Beranda</button>
        </form>
        <form action="logout.php" method="post" style="display: inline;">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </header>

    <div class="container">
        <div class="card">
            <h2>Pengenalan Tajwid</h2>
            <p>Tajwid adalah ilmu yang mempelajari cara membaca Al-Qur'an dengan baik dan benar. Dalam tajwid, terdapat aturan-aturan yang harus diikuti agar bacaan Al-Qur'an sesuai dengan kaidah yang telah ditetapkan. Tujuan utama dari tajwid adalah untuk menjaga keaslian bacaan Al-Qur'an dan menghindari kesalahan dalam pelafalan.</p>
        </div>

        <div class="card">
            <h2>Hukum Bacaan</h2>
            <p>Dalam tajwid, terdapat beberapa hukum bacaan yang perlu dipahami, antara lain:</p>
            <ul>
                <li><strong>Idgham:</strong> Menggabungkan dua huruf menjadi satu. Terdapat dua jenis idgham:
                    <ul>
                        <li><strong>Idgham Mutamathilain:</strong> Dua huruf yang sama. Contoh:
                            <span class="arabic">نّ</span> (ن + ن) dalam kata <span class="arabic">مِنّ</span> (minna), <span class="arabic">مَنّ</span> (manna), <span class="arabic">مِنّ</span> (minna).</li>
                        <li><strong>Idgham Mutajanisain:</strong> Dua huruf yang berbeda tetapi sejenis. Contoh:
                            <span class="arabic">لّ</span> (ل + ر) dalam kata <span class="arabic">مَلّ</span> (malla), <span class="arabic">مَلّ</span> (malla), <span class="arabic">مَلّ</span> (malla).</li>
                    </ul>
                </li>
                <li><strong>Iqlab:</strong> Mengubah huruf ن (nun) menjadi م (mim) ketika bertemu dengan huruf ب (ba). Contoh:
                    <span class="arabic">مِن بَعْدِهِ</span> (min ba'dihi), <span class="arabic">مِن بَابٍ</span> (min bābin), <span class="arabic">مِن بَارِدٍ</span> (min bāridin).</li>
                <li><strong>Ikhfa:</strong> Menyembunyikan bunyi huruf ن (nun) ketika bertemu dengan huruf-huruf tertentu. Contoh:
                    <span class="arabic">مِن صَابِرٍ</span> (min sâbirin), <span class="arabic">مِنْ شَارِبٍ</span> (min shâribin), <span class="arabic">مِنْ جَاهِلٍ</span> (min jāhilin).</li>
                <li><strong>Qalqalah:</strong> Memantulkan suara pada huruf-huruf tertentu (ق, ط, ب, ج, د) ketika berada di akhir kata. Contoh:
                    <span class="arabic">جَدّ</span> (jadd), <span class="arabic">قَدْ</span> (qadd), <span class="arabic">بِدَارٍ</span> (bidārin).</li>
                <li><strong>Ghunna:</strong> Suara dengung yang dihasilkan saat membaca ن (nun) atau م (mim) dengan hukum ikhfa. Contoh:
                    <span class="arabic">مَّا</span> (mâ), <span class="arabic">مِنْ</span> (min), <span class="arabic">مُنْتَظِرٍ</span> (muntazhirin).</li>
                <li><strong>Mad:</strong> Memanjangkan bunyi huruf tertentu. Terdapat beberapa jenis mad, seperti:
                    <ul>
                        <li><strong>Mad Wajib Muttasil:</strong> Memanjangkan 4-5 harakat ketika huruf mad bertemu dengan huruf hamzah. Contoh:
                            <span class="arabic">مَآ أَشْهَدُ</span> (mā ashhidu), <span class="arabic">قَالَ أَحَدٌ</span> (qāla aḥadun), <span class="arabic">مَآ أَفْعَلُ</span> (mā af'alu).</li>
                        <li><strong>Mad Jaiz Munfasil:</strong> Memanjangkan 4-5 harakat ketika huruf mad dan hamzah terpisah. Contoh:
                            <span class="arabic">مَـٰنَـٰمَ</span> (māna), <span class="arabic">مَـٰنَـٰمَ</span> (māna), <span class="arabic">مَـٰنَـٰمَ</span> (māna).</li>
                        <li><strong>Mad Aridh Lisukuun:</strong> Memanjangkan 2 harakat ketika huruf mad diakhiri dengan sukun. Contoh:
                            <span class="arabic">مَـٰنَـٰمَ</span> (māna), <span class="arabic">مَـٰنَـٰمَ</span> (māna), <span class="arabic">مَـٰنَـٰمَ</span> (māna).</li>
                        <li><strong>Mad Badal:</strong> Memanjangkan 2 harakat ketika huruf mad menggantikan huruf lain. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad ‘Iwad:</strong> Memanjangkan 2 harakat ketika menggantikan huruf yang hilang. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Lazim Mutsaqqol Kalimi:</strong> Memanjangkan 6 harakat pada huruf yang memiliki dua harakat. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Lazim Mukhoffaf Kalimi:</strong> Memanjangkan 6 harakat pada huruf yang memiliki satu harakat. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Layyin:</strong> Memanjangkan 2 harakat pada huruf yang lemah. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Shilah Qashiroh:</strong> Memanjangkan 2 harakat pada huruf yang diakhiri dengan huruf lemah. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Shilah Thowilah:</strong> Memanjangkan 4-5 harakat pada huruf yang diakhiri dengan huruf lemah. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                        <li><strong>Mad Farqu:</strong> Memanjangkan 2 harakat pada huruf yang berbeda. Contoh:
                            <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda), <span class="arabic">مَـٰدَ</span> (mâda).</li>
                    </ul>
                </li>
                <li><strong>Ikhfa Syafawi:</strong> Menyembunyikan bunyi م (mim) ketika bertemu dengan huruf ب (ba). Contoh:
                    <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma).</li>
                <li><strong>Idgham Mimi:</strong> Menggabungkan dua huruf م (mim) menjadi satu. Contoh:
                    <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma).</li>
                <li><strong>Izhar Syafawi:</strong> Menyatakan bunyi م (mim) ketika bertemu dengan huruf lain. Contoh:
                    <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma), <span class="arabic">مَـٰمَ</span> (māma).</li>
                <li><strong>Qalqalah Kubra:</strong> Memantulkan suara pada huruf ق, ط, ب, ج, د ketika berada di akhir kata. Contoh:
                    <span class="arabic">جَدّ</span> (jadd), <span class="arabic">قَدْ</span> (qadd), <span class="arabic">بِدَارٍ</span> (bidārin).</li>
                <li><strong>Qalqalah Sugra:</strong> Memantulkan suara pada huruf ق, ط, ب, ج, د ketika berada di tengah kata. Contoh:
                    <span class="arabic">جَدّ</span> (jadd), <span class="arabic">قَدْ</span> (qadd), <span class="arabic">بِدَارٍ</span> (bidārin).</li>
                <li><strong>Al Syamsiyah:</strong> Huruf yang tidak dibaca ketika bertemu dengan huruf ل (lam). Contoh:
                    <span class="arabic">الشَّمْسِ</span> (ash-shamsi), <span class="arabic">الْقَمَرِ</span> (al-qamari), <span class="arabic">الْكِتَابِ</span> (al-kitābi).</li>
                <li><strong>Al Qamariyah:</strong> Huruf yang dibaca ketika bertemu dengan huruf ل (lam). Contoh:
                    <span class="arabic">الْقَمَرِ</span> (al-qamari), <span class="arabic">الْكِتَابِ</span> (al-kitābi), <span class="arabic">الْبَحْرِ</span> (al-baḥri).</li>
            </ul>
        </div>

        <div class="card">
            <h2>Tanda-Tanda Wakaf</h2>
            <p>Tanda wakaf digunakan untuk menunjukkan tempat berhenti dalam membaca Al-Qur'an. Berikut adalah beberapa tanda wakaf beserta contohnya:</p>
            <ul>
                <li><strong>م:</strong> Wakaf Lazim (harus berhenti). Contoh:
                    <span class="arabic">وَأَنتُمُ الأَعْلَوْنَ</span> (wa antum al-a'lawn).</li>
                <li><strong>ط:</strong> Wakaf Mutlak (boleh berhenti atau melanjutkan). Contoh:
                    <span class="arabic">إِنَّ اللَّهَ غَفُورٌ رَّحِيمٌ</span> (inna Allāh ghafūrur raḥīm).</li>
                <li><strong>لا:</strong> Tidak boleh berhenti. Contoh:
                    <span class="arabic">وَإِنَّكَ لَعَلَى خُلُقٍ عَظِيمٍ</span> (wa innaka la'ala khuluqin 'azīm).</li>
                <li><strong>ج:</strong> Wakaf Jaiz (boleh berhenti). Contoh:
                    <span class="arabic">وَإِنَّكَ لَعَلَى خُلُقٍ عَظِيمٍ</span> (wa innaka la'ala khuluqin 'azīm).</li>
            </ul>
        </div>

        <div class="card">
            <h2>Makharijul Huruf</h2>
            <p>Makharijul huruf adalah tempat keluarnya huruf-huruf hijaiyah. Memahami makharijul huruf sangat penting untuk melafalkan huruf dengan benar. Terdapat 17 makhraj yang dibagi menjadi beberapa kategori, seperti:</p>
            <ul>
                <li><strong>Huruf dari tenggorokan:</strong> ع, ح, غ, خ</li>
                <li><strong>Huruf dari lidah:</strong> ل, ر, ن, ت, د, ط, ص, ز, س, ش, ذ,
                <li><strong>Huruf dari bibir:</strong> ب, م, ف</li>
            </ul>
        </div>

        <div class="card">
            <h2>Contoh Bacaan</h2>
            <p>Berikut adalah beberapa contoh bacaan yang benar sesuai dengan tajwid:</p>
            <ul>
                <li>Contoh bacaan Al-Fatihah dengan tajwid yang benar.</li>
                <li>Contoh bacaan surat-surat pendek seperti Al-Ikhlas, Al-Falaq, dan An-Nas.</li>
                <li>Latihan membaca dengan tajwid yang benar menggunakan aplikasi atau video tutorial.</li>
            </ul>
        </div>

        <div class="card">
            <h2>Latihan dan Quiz</h2>
            <p>Setelah mempelajari materi, Anda dapat melakukan latihan dan quiz untuk menguji pemahaman Anda tentang tajwid. Banyak aplikasi dan situs web yang menyediakan latihan interaktif dan quiz untuk membantu Anda belajar tajwid dengan lebih efektif.</p>
        </div>
    </div>

    <footer>
        &copy; 2025 Memo Qur'an
    </footer>
</body>
</html>
