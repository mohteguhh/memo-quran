<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

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

// Cek apakah ada permintaan untuk menghapus pengguna
if (isset($_POST['delete_user_id'])) {
    $deleteUserId = $_POST['delete_user_id'];
    $deleteSql = "DELETE FROM users WHERE id = " . $conn->real_escape_string($deleteUserId);
    
    if ($conn->query($deleteSql) === TRUE) {
        $message = "Pengguna berhasil dihapus.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Cek apakah ada permintaan untuk mengubah status admin
if (isset($_POST['user_id']) && isset($_POST['is_admin'])) {
    $userId = $_POST['user_id'];
    $isAdmin = $_POST['is_admin'];

    // Update status admin di database
    $updateSql = "UPDATE users SET is_admin = " . (int)$isAdmin . " WHERE id = " . $conn->real_escape_string($userId);
    
    if ($conn->query($updateSql) === TRUE) {
        $message = "Status admin berhasil diubah.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Ambil keyword pencarian dari form
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$kelasFilter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : '';
$jurusanFilter = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

// Query untuk mengambil semua pengguna
$sql = "SELECT id, username, is_admin, kelas, semester, jurusan FROM users WHERE 1=1";

// Tambahkan kondisi WHERE jika ada keyword pencarian
if (!empty($searchKeyword)) {
    $sql .= " AND username LIKE '%" . $conn->real_escape_string($searchKeyword) . "%'";
}

// Tambahkan filter untuk kelas, semester, dan jurusan
if (!empty($kelasFilter)) {
    $sql .= " AND kelas = '" . $conn->real_escape_string($kelasFilter) . "'";
}
if (!empty($semesterFilter)) {
    $sql .= " AND semester = '" . $conn->real_escape_string($semesterFilter) . "'";
}
if (!empty($jurusanFilter)) {
    $sql .= " AND jurusan = '" . $conn->real_escape_string($jurusanFilter) . "'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Penghafal Alquran</title>
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

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 600;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        /* Button Styles */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 0.5rem;
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #388E3C;
        }

        .btn-secondary {
            background-color: #008CBA;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #0077A3;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        /* Message Styles */
        .message {
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* General Styles */
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

        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
        }

        /* Edit Password Form Styles */
        .edit-password-form {
            display: none;
            margin-top: 10px;
        }

        .edit-password-form label {
            display: block;
            margin-bottom: 5px;
        }

        .edit-password-form input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
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

            table {
                width: 100%;
                overflow-x: auto;
                display: block;
            }

            th,
            td {
                white-space: nowrap;
            }

            .btn {
                display: block;
                width: 100%;
                text-align: center;
                width: auto;
            }
        }

        /* Search Input Styles */
        .search-container {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px; /* Kecilkan lebar kolom filter */
            font-size: 1rem;
            transition: border-color 0.3s;
            margin-right: 0.5rem; /* Spasi antara input dan tombol */
        }

        .search-input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        /* Search Button Styles */
        .search-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }

        .search-button:hover {
            background-color: #388E3C;
        }

        .search-button i {
            margin-right: 0.3rem;
        }

        /* Reset Button Styles */
        .reset-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: background-color 0.3s;
        }

        .reset-button:hover {
            background-color: #d32f2f;
        }
    </style>
    <script>
        function toggleEditPasswordForm(userId) {
            var form = document.getElementById('edit-password-form-' + userId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        function resetFilters() {
            document.querySelector('input[name="search"]').value = '';
            document.querySelector('select[name="kelas"]').selectedIndex = 0;
            document.querySelector('select[name="semester"]').selectedIndex = 0;
            document.querySelector('select[name="jurusan"]').selectedIndex = 0;
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
        <h1>Daftar Pengguna</h1>

        <?php if (isset($message)): ?>
        <div class="message success">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="message error">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Form Pencarian -->
        <div class="search-container">
            <form action="" method="GET" style="display: flex; align-items: center;">
                <input type="text" class="search-input" name="search" placeholder="Cari nama pengguna..."
                    value="<?php echo htmlspecialchars($searchKeyword); ?>">
                <select name="kelas" class="search-input">
                    <option value="">-- Kelas --</option>
                    <option value="A" <?php echo ($kelasFilter == 'A') ? 'selected' : ''; ?>>Kelas A</option>
                    <option value="B" <?php echo ($kelasFilter == 'B') ? 'selected' : ''; ?>>Kelas B</option>
                    <option value="C" <?php echo ($kelasFilter == 'C') ? 'selected' : ''; ?>>Kelas C</option>
                    <option value="D" <?php echo ($kelasFilter == 'D') ? 'selected' : ''; ?>>Kelas D</option>
                    <option value="E" <?php echo ($kelasFilter == 'E') ? 'selected' : ''; ?>>Kelas E</option>
                    <option value="F" <?php echo ($kelasFilter == 'F') ? 'selected' : ''; ?>>Kelas F</option>
                    <option value="G" <?php echo ($kelasFilter == 'G') ? 'selected' : ''; ?>>Kelas G</option>
                </select>
                <select name="semester" class="search-input">
                    <option value="">-- Semester --</option>
                    <option value="Semester 1" <?php echo ($semesterFilter == 'Semester 1') ? 'selected' : ''; ?>>Semester 1</option>
                    <option value="Semester 2" <?php echo ($semesterFilter == 'Semester 2') ? 'selected' : ''; ?>>Semester 2</option>
                    <option value="Semester 3" <?php echo ($semesterFilter == 'Semester 3') ? 'selected' : ''; ?>>Semester 3</option>
                </select>
                <select name="jurusan" class="search-input">
                    <option value="">-- Jurusan --</option>
                    <option value="PAI" <?php echo ($jurusanFilter == 'PAI') ? 'selected' : ''; ?>>PAI</option>
                </select>
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Cari
                </button>
                <button type="button" class="reset-button" onclick="resetFilters()">Reset</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Admin</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                        echo "<td>" . ($row["is_admin"] == 1 ? "Ya" : "Tidak") . "</td>";
                        echo "<td>" . htmlspecialchars($row["kelas"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["semester"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["jurusan"]) . "</td>";
                        echo "<td>
                                <form method='POST' style='margin-bottom: 0.5rem;'>
                                    <input type='hidden' name='user_id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='is_admin' value='" . ($row["is_admin"] == 1 ? 0 : 1) . "'>
                                    <button type='submit' class='btn btn-primary'>" . ($row["is_admin"] == 1 ? "Cabut Admin" : "Jadikan Admin") . "</button>
                                </form>
                                <button class='btn btn-secondary' onclick='toggleEditPasswordForm(" . $row["id"] . ")'>Edit Password</button>
                                <form method='POST' class='edit-password-form' id='edit-password-form-" . $row["id"] . "'>
                                    <label for='new_password'>Password Baru:</label>
                                    <input type='password' name='new_password' id='new_password' required>
                                    <input type='hidden' name='edit_password_user_id' value='" . $row["id"] . "'>
                                    <button type='submit' class='btn btn-primary'>Simpan Password Baru</button>
                                </form>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='delete_user_id' value='" . $row["id"] . "'>
                                    <button type='submit' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus pengguna ini?\");'>Hapus</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada pengguna.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
