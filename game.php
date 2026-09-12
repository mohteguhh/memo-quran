<?php
session_start();

// Pastikan pengguna sudah login
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

// Ambil nama pengguna dari session
$username = $_SESSION['username'];

// Ambil progres pengguna dari database
$sql = "SELECT points FROM user_progress WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($points);
$stmt->fetch();
$stmt->close();

// Inisialisasi variabel jika belum ada
$points = isset($points) ? $points : 0;
$bantuan_used = isset($_SESSION['bantuan_used']) ? $_SESSION['bantuan_used'] : false;

// Inisialisasi array untuk menyimpan pertanyaan yang telah dijawab
if (!isset($_SESSION['answered_questions'])) {
    $_SESSION['answered_questions'] = [];
}

// Ambil semua pertanyaan dari database
$sql = "SELECT * FROM questions";
$result = $conn->query($sql);
$questions = [];

while ($row = $result->fetch_assoc()) {
    $questions[] = [
        "question" => $row['question'],
        "options" => [
            "A" => $row['option_a'],
            "B" => $row['option_b'],
            "C" => $row['option_c'],
            "D" => $row['option_d']
        ],
        "answer" => $row['answer'],
        "bantuan" => $row['bantuan']
    ];
}

$total_questions = count($questions);

// Ambil pertanyaan berdasarkan indeks yang belum dijawab
$current_question_index = count($_SESSION['answered_questions']);
$current_question = $questions[$current_question_index] ?? null;

// Cek apakah kuis sudah selesai
$quiz_completed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer'])) {
        $user_answer = strtoupper(trim($_POST['answer']));
        if ($user_answer === $current_question['answer']) {
            $points += 10; // Tambah poin jika jawaban benar
            $message = "Jawaban benar!"; // Pesan untuk jawaban benar
            $message_class = "success"; // Kelas untuk pesan sukses
        } else {
            $points -= 10; // Kurangi poin jika jawaban salah
            $message = "Jawaban salah! Coba lagi."; // Pesan untuk jawaban salah
            $message_class = "error"; // Kelas untuk pesan error
        }

        // Tandai pertanyaan sebagai telah dijawab
        $_SESSION['answered_questions'][] = $current_question_index;

        // Simpan progres ke database
        $sql = "INSERT INTO user_progress (username, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE points = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $username, $points, $points);
        $stmt->execute();
        $stmt->close();

        // Cek apakah kuis sudah selesai
        if (count($_SESSION['answered_questions']) >= $total_questions) {
            $quiz_completed = true; // Set status kuis selesai
        } else {
            // Jika belum selesai, redirect untuk memuat pertanyaan berikutnya
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    if (isset($_POST['bantuan_used']) && $_POST['bantuan_used'] == '1') {
        $points -= 10; // Kurangi poin sebesar 10 saat bantuan digunakan
        $bantuan_used = true; // Tandai bahwa bantuan telah digunakan
        $_SESSION['bantuan_used'] = true; // Simpan status bantuan dalam session

        // Simpan progres ke database setelah menggunakan bantuan
        $sql = "UPDATE user_progress SET points = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $points, $username);
        $stmt->execute();
        $stmt->close();
    }
}

// Reset variabel bantuan_used jika pertanyaan baru dimuat
if (!isset($_POST['answer'])) {
    $bantuan_used = false;
    unset($_SESSION['bantuan_used']); // Hapus status bantuan dari session
}

// Cek apakah kuis sudah selesai
if ($quiz_completed) {
    // Hapus session terkait kuis
    unset($_SESSION['bantuan_used']);
}

$conn->close(); // Tutup koneksi database
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kuis Hafalan Alquran</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9; /* Light gray background */
            color: #333;
            padding: 20px;
            margin: 0;
            text-align: center;
            overflow: auto; /* Enable scrollbars if needed */
        }

        h1 {
            color: #2c3e50; /* Dark blue */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        /* Quiz Container */
        .quiz-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .quiz-container:hover {
            transform: translateY(-5px);
        }

        /* Points Display */
        .quiz-container p {
            color: #777;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        /* Question Styles */
        .quiz-container p:first-of-type {
            font-size: 1.4em;
            font-weight: bold;
            margin-bottom: 30px;
            color: #34495e; /* Darker blue */
        }

        /* Options */
        .options {
            margin: 20px 0;
        }

        .option {
            display: block;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            color: #555;
        }

        .option:hover {
            background-color: #e0e0e0;
            transform: translateX(5px);
        }

        input[type="radio"] {
            margin-right: 10px;
            vertical-align: middle;
        }

        /* Buttons */
        button, .home-button {
            background-color: #4CAF50; /* Hijau */
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        button:hover, .home-button:hover {
            background-color: #45a049; /* Hijau lebih gelap */
            transform: scale(1.05);
        }

        .btn-bantuan {
            background-color: #f44336; /* Merah */
            color: white;
        }

        .btn-bantuan:hover {
            background-color: #d32f2f; /* Merah lebih gelap */
        }

        .btn-bantuan:disabled {
            background-color: #ddd;
            cursor: not-allowed;
            color: #999;
        }

        /* Messages */
        .message {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            transition: all 0.5s ease;
        }

        .success {
            color: #27ae60; /* Green */
            animation: fadeIn 0.5s;
        }

        .error {
            color: #e74c3c; /* Red */
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Congratulations Animation */
        .congratulations {
            font-size: 2.5em;
            color: #f1c40f; /* Yellow */
            animation: pulse 2s infinite, fadeIn 1s ease-in-out;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Bantuan */
        .bantuan {
            font-style: italic;
            color: #888;
            display: none;
            margin-top: 20px;
        }

        /* Font untuk teks Arab */
        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 1.2em;
            line-height: 1.8;
        }

        /* Tambahkan style untuk pertanyaan dan opsi yang mengandung teks Arab */
        p, .option {
            font-size: 1.2em; /* Ukuran font dasar */
        }

        /* Style khusus untuk teks Arab dalam pertanyaan */
        p.arabic-question {
            font-family: 'Amiri', serif;
            font-size: 1.5em; /* Ukuran font lebih besar untuk pertanyaan */
            line-height: 2; /* Spasi baris lebih besar */
            color: #34495e; /* Warna teks lebih gelap */
        }

        /* Style khusus untuk teks Arab dalam opsi */
        .option.arabic-option {
            font-family: 'Amiri', serif;
            font-size: 1.3em; /* Ukuran font lebih besar untuk opsi */
            line-height: 1.6; /* Spasi baris lebih besar */
        }
    </style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Amiri&display=swap">
</head>
<body>
    <div class="quiz-container">
        <div style="text-align: right; margin-bottom: 20px;">
            <form action="index.php" method="get" style="display: inline;">
                <button type="submit" class="home-button">Beranda</button>
            </form>
            <form action="logout.php" method="post" style="display: inline;">
                <button type="submit" class="btn-bantuan">Logout</button>
            </form>
        </div>

        <?php if ($quiz_completed): ?>
            <h1 class="congratulations">Selamat! Anda telah menyelesaikan semua pertanyaan!</h1>
            <p>Total poin: <?php echo $points; ?></p>
            <a href="index.php" class="home-button">Kembali ke Beranda</a>
        <?php else: ?>

            <h1>Kuis Hafalan Alquran</h1>
            <p>Poin: <?php echo $points; ?></p>

            <?php if ($current_question): ?>
                <form method="POST">
                    <p class="arabic-question"><?php echo $current_question['question']; ?></p>
                    <div class="options">
                        <?php foreach ($current_question['options'] as $key => $option): ?>
                            <label class="option arabic-option">
                                <input type="radio" name="answer" value="<?php echo $key; ?>" required />
                                <?php echo $key . ". " . $option; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="bantuan_used" name="bantuan_used" value="0">
                    <button type="submit">Kirim Jawaban</button>
                </form>
                <p class="bantuan" id="bantuan-text"><?php echo $current_question['bantuan']; ?></p>
            <?php else: ?>
                <p>Selamat! Anda telah menyelesaikan semua pertanyaan.</p>
            <?php endif; ?>

            <?php if (isset($message)): ?>
                <div class="message <?php echo $message_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <button class="btn-bantuan" id="bantuan-button" onclick="showBantuan()">Gunakan Bantuan</button>

        <?php endif; ?>

    </div>
    <script>
        function showBantuan() {
            document.getElementById('bantuan-text').style.display = 'block';
            document.getElementById('bantuan-button').disabled = true;
            document.getElementById('bantuan_used').value = '0';
        }
    </script>
</body>
</html>
