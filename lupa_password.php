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

// Proses penggantian password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Cek apakah email ada di database
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password di database
        $update_sql = "UPDATE users SET password = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $email);

        if ($update_stmt->execute()) {
            $success = "Password berhasil diubah.";
        } else {
            $error = "Gagal mengubah password: " . $update_stmt->error;
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lupa Password - Penghafal Alquran</title>
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

        input[type="email"],
        input[type="password"] {
            width: 80%; /* Ubah lebar menjadi 80% agar lebih proporsional */
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
            margin: 0 auto; /* Tambahkan ini untuk meratakan input */
        }

        input[type="email"]:focus,
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

        .error {
            color: red; /* Warna untuk pesan kesalahan */
            margin-bottom: 1rem;
        }

        .success {
            color: green; /* Warna untuk pesan sukses */
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
        <h1>Lupa Password</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" id="reset-password-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" required />
            </div>
            <button type="submit" class="btn">Ubah Password</button>
        </form>
        <a href="login.php" class="link">Kembali ke Login</a>
        <div class="footer">
            &copy; 2025 Penghafal Alquran
        </div>
    </div>
</body>
</html>
