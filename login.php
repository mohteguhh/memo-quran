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

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login_type = isset($_POST['login_type']) && $_POST['login_type'] === 'admin' ? 'admin' : 'user'; // Tentukan jenis login

    // Query untuk memeriksa pengguna
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Simpan username
            $_SESSION['is_admin'] = ($login_type === 'admin' && $user['is_admin'] == 1); // Cek apakah admin

            // Redirect berdasarkan status admin
            if ($_SESSION['is_admin']) {
                header("Location: admin_dashboard.php"); // Arahkan ke admin_dashboard.php
            } else {
                header("Location: index.php"); // Arahkan ke index.php
            }
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Penghafal Alquran</title>
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
            text-align: center; /* Tambahkan ini untuk meratakan label */
        }

        input[type="text"],
        input[type="password"] {
            width: 80%; /* Ubah lebar menjadi 80% agar lebih proporsional */
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
            margin: 0 auto; /* Tambahkan ini untuk meratakan input */
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50; /* Hijau tua */
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #4CAF50; /* Hijau tua */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 0.5rem; /* Spasi antar tombol */
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

        .admin-checkbox {
            margin-top: 1rem;
            text-align: left;
            display: flex;
            align-items: center; /* Rata tengah secara vertikal */
        }

        .admin-checkbox input {
            margin-right: 0.5rem; /* Spasi antara checkbox dan label */
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
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" id="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
            </div>

            <div class="admin-checkbox">
                <input type="checkbox" id="admin_login" name="login_type" value="admin">
                <label for="admin_login">Login sebagai Admin</label>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <a href="lupa_password.php" class="link">Lupa Password?</a> <!-- Modifikasi di sini -->
        <a href="register.php" class="link">Daftar</a>
        <div class="footer">
            &copy; 2025 Memo Qur'an
        </div>
    </div>
</body>
</html>
