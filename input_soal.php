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
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_class = '';
$edit_mode = false;
$edit_question = [];

// Proses tambah soal atau hapus soal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_question_id'])) {
        // Proses hapus soal
        $delete_id = $_POST['delete_question_id'];
        $delete_sql = "DELETE FROM questions WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $delete_id);
        if ($delete_stmt->execute()) {
            $message = "Soal berhasil dihapus!";
            $message_class = "success";
        } else {
            $message = "Gagal menghapus soal: " . $delete_stmt->error;
            $message_class = "error";
        }
        $delete_stmt->close();
    } elseif (isset($_POST['edit_question_id'])) {
        // Proses tambah soal
        $question = $_POST['question'];
        $options = [
            'A' => $_POST['option_a'],
            'B' => $_POST['option_b'],
            'C' => $_POST['option_c'],
            'D' => $_POST['option_d']
        ];
        $answer = $_POST['answer'];
        $bantuan = $_POST['bantuan'];

        $sql = "INSERT INTO questions (question, option_a, option_b, option_c, option_d, answer, bantuan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $question, $options['A'], $options['B'], $options['C'], $options['D'], $answer, $bantuan);

        if ($stmt->execute()) {
            $message = "Soal berhasil ditambahkan!";
            $message_class = "success";
        } else {
            $message = "Gagal menambahkan soal: " . $stmt->error;
            $message_class = "error";
        }
        $stmt->close();
    }
}

// Ambil semua soal dari database
$sql = "SELECT * FROM questions";
$result = $conn->query($sql);
$questions = [];

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Input Soal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eafaf1;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar h2 {
            color: #4CAF50;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar li {
            margin-bottom: 10px;
            width: 100%;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-align: left;
        }

        .sidebar a:hover {
            background-color: #4CAF50;
        }

        /* Content Styles */
        .content {
            flex: 1;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px;
            overflow-x: auto;
        }

        .content h1 {
            margin-bottom: 1rem;
            font-size: 2rem;
            font-weight: 600;
            color: #4CAF50;
            text-align: center;
        }

        /* Form Styles */
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }

        .success {
            color: #27ae60;
        }

        .error {
            color: #e74c3c;
        }

        .questions-list {
            margin-top: 30px;
            text-align: left;
        }

        .question-item {
            border: 1px solid #ccc;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            position: relative;
        }

        .question-item strong {
            display: inline-block;
            min-width: 120px;
        }

        /* Tombol hapus merah */
        .btn-danger-delete {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            position: absolute;
            top: 15px;
            right: 15px;
            transition: background-color 0.3s ease;
        }

        .btn-danger-delete:hover {
            background-color: #d32f2f;
        }

        /* Logout Button Styles */
        .logout-container {
            text-align: center;
            margin-top: 1rem;
        }

        .logout-btn {
            background-color: #f44336;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                max-width: none;
                text-align: center;
            }

            .content {
                margin: 10px;
                padding: 1rem;
            }
        }
    </style>
    <script>
        function confirmDeletion() {
            return confirm('Apakah Anda yakin ingin menghapus soal ini?');
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Pengguna</a></li>
            <li><a href="admin_hafalan.php">Hafalan</a></li>
            <li><a href="admin_kuis.php">Level Kuis</a></li>
            <li><a href="input_soal.php">Input Soal</a></li>
            <li><a href="cetak_hafalan.php">Capaian Hafalan</a></li> <!-- Tombol Capaian Hafalan -->
        </ul>
        <div class="logout-container">
            <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
        </div>
    </div>

    <div class="content">
        <h1>Input Soal</h1>
        <?php if ($message): ?>
            <div class="message <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="question">Pertanyaan:</label>
            <textarea id="question" name="question" rows="4" required><?php echo $edit_mode ? htmlspecialchars($edit_question['question']) : ''; ?></textarea>

            <label for="option_a">Opsi A:</label>
            <input type="text" id="option_a" name="option_a" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['option_a']) : ''; ?>" />

            <label for="option_b">Opsi B:</label>
            <input type="text" id="option_b" name="option_b" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['option_b']) : ''; ?>" />

            <label for="option_c">Opsi C:</label>
            <input type="text" id="option_c" name="option_c" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['option_c']) : ''; ?>" />

            <label for="option_d">Opsi D:</label>
            <input type="text" id="option_d" name="option_d" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['option_d']) : ''; ?>" />

            <label for="answer">Jawaban yang benar (A/B/C/D):</label>
            <input type="text" id="answer" name="answer" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['answer']) : ''; ?>" />

            <label for="bantuan">Bantuan:</label>
            <input type="text" id="bantuan" name="bantuan" required value="<?php echo $edit_mode ? htmlspecialchars($edit_question['bantuan']) : ''; ?>" />

            <input type="hidden" name="edit_question_id" value="<?php echo $edit_mode ? $edit_question['id'] : ''; ?>" />
            <button type="submit"><?php echo $edit_mode ? 'Perbarui Soal' : 'Tambah Soal'; ?></button>
        </form>

        <div class="questions-list">
            <h2>Daftar Soal yang Sudah Ditambahkan</h2>
            <?php if (count($questions) > 0): ?>
                <?php foreach ($questions as $q): ?>
                    <div class="question-item">
                        <strong>Pertanyaan:</strong> <?php echo htmlspecialchars($q['question']); ?><br>
                        <strong>Opsi A:</strong> <?php echo htmlspecialchars($q['option_a']); ?><br>
                        <strong>Opsi B:</strong> <?php echo htmlspecialchars($q['option_b']); ?><br>
                        <strong>Opsi C:</strong> <?php echo htmlspecialchars($q['option_c']); ?><br>
                        <strong>Opsi D:</strong> <?php echo htmlspecialchars($q['option_d']); ?><br>
                        <strong>Jawaban:</strong> <?php echo htmlspecialchars($q['answer']); ?><br>
                        <strong>Bantuan:</strong> <?php echo htmlspecialchars($q['bantuan']); ?><br>

                        <form method="POST" onsubmit="return confirmDeletion();" style="margin-top:10px; display:inline;">
                            <input type="hidden" name="delete_question_id" value="<?php echo $q['id']; ?>" />
                            <button type="submit" class="btn-danger-delete">Hapus</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada soal yang ditambahkan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
