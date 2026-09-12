<?php
session_start();

// Konfigurasi database
$host = 'localhost';
$db = 'memf3243_penghafal_alquran';
$user = 'memf3243';
$pass = 'jWNxKV6GdcqE87';

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email']; // Ambil email dari form
    $jurusan = $_POST['jurusan']; // Ambil jurusan dari form
    $semester = $_POST['semester']; // Ambil semester dari form
    $kelas = $_POST['kelas']; // Ambil kelas dari form

    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username sudah terdaftar. Silakan pilih username lain.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk menambahkan pengguna baru
        $sql = "INSERT INTO users (username, password, email, jurusan, semester, kelas) VALUES (?, ?, ?, ?, ?, ?)"; // Tambahkan jurusan, semester, dan kelas ke query
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $hashed_password, $email, $jurusan, $semester, $kelas); // Bind semua parameter

        if ($stmt->execute()) {
            // Jika registrasi berhasil, arahkan ke halaman login
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal mendaftar: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Penghafal Alquran</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eafaf1; /* Background nuansa Islami */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 1rem;
            font-size: 2rem;
            font-weight: 600;
            color: #4CAF50; /* Hijau tua */
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: center; /* Ubah menjadi center */
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select { /* Tambahkan input untuk select */
            width: 80%; /* Ubah lebar menjadi 80% untuk rata tengah */
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
            margin: 0 auto; /* Tambahkan margin auto untuk rata tengah */
            display: block; /* Pastikan input ditampilkan sebagai block */
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus { /* Tambahkan fokus untuk input select */
            border-color: #4CAF50; /* Hijau tua */
            outline: none;
        }

        .btn {
            width: 80%; /* Ubah lebar menjadi 80% untuk rata tengah */
            padding: 0.75rem;
            background-color: #4CAF50; /* Hijau tua */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 0 auto; /* Tambahkan margin auto untuk rata tengah */
            display: block; /* Pastikan tombol ditampilkan sebagai block */
        }

        .btn:hover {
            background-color: #388E3C; /* Hijau lebih gelap */
        }

        .link {
            display: block;
            margin-top: 1rem;
            color: #4CAF50; /* Hijau tua */
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #6b7280; /* Warna teks sekunder */
        }

        .error {
            color: red; /* Warna untuk pesan kesalahan */
            margin-bottom: 1rem;
        }

        @media (max-width: 480px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" id="register-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
            </div>
            <div class="form-group">
                <label for="jurusan">Jurusan</label>
                <select id="jurusan" name="jurusan" required>
                    <option value="PAI">PAI</option>
                </select>
            </div>
            <div class="form-group">
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Semester 3">Semester 3</option>
                </select>
            </div>
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <select id="kelas" name="kelas" required>
                    <option value="A">Kelas A</option>
                    <option value="B">Kelas B</option>
                    <option value="C">Kelas C</option>
                    <option value="D">Kelas D</option>
                    <option value="E">Kelas E</option>
                    <option value="F">Kelas F</option>
                    <option value="G">Kelas G</option>
                </select>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>
        <a href="login.php" class="link">Sudah punya akun? Login</a>
        <div class="footer">
            &copy; 2024 Penghafal Alquran
        </div>
    </div>
</body>
</html>
