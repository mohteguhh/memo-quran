<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Konfigurasi database
$host = 'localhost';
$db = 'memf3243_penghafal_alquran';
$user = 'memf3243';
$pass = 'jWNxKV6GdcqE87';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses reset poin dan hapus username
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_points_user'])) {
        $username = $_POST['reset_points_user'];
        $reset_points_sql = "UPDATE user_progress SET points = 0 WHERE username = ?";
        $stmt = $conn->prepare($reset_points_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_user'])) {
        $username = $_POST['delete_user'];
        $delete_user_sql = "DELETE FROM user_progress WHERE username = ?";
        $stmt = $conn->prepare($delete_user_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
    }
}

$searchKeyword = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT username, points FROM user_progress WHERE username LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("s", $searchKeyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Ambil data progres pengguna
    $sql = "SELECT username, points FROM user_progress";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Kuis - Progres Pengguna</title>
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

        /* Search Form */
        .search-form {
            display: flex;
            justify-content: flex-start; /* Ubah dari flex-end ke flex-start agar di kiri */
            margin-bottom: 1rem;
        }

        .search-input {
            padding: 0.5rem 8.5rem;
            font-size: 1rem;
            border: 2px solid #4CAF50;
            border-radius: 25px 0 0 25px;
            outline: none;
            width: 250px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: #388E3C;
        }

        .search-button {
            background-color: #4CAF50;
            border: 2px solid #4CAF50;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            color: white;
        }

        .search-button:hover {
            background-color: #388E3C;
            border-color: #388E3C;
        }

        .search-button:focus {
            outline: 3px solid #a5d6a7;
        }

        .search-button i {
            font-size: 1.2rem;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
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

        /* General Styles */
        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
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
            width: auto;
            text-align: center;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        .btn-warning {
            background-color: #ff9800;
            color: white;
        }

        .btn-warning:hover {
            background-color: #fb8c00;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .content {
                margin: 10px;
                padding: 1rem;
            }

            .content h1 {
                font-size: 1.5rem;
            }

            table {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.9rem;
            }

            .search-input {
                width: 150px;
            }
        }

        @media (max-width: 480px) {
            .sidebar h2 {
                font-size: 1.5rem;
            }

            .content h1 {
                font-size: 1.25rem;
            }

            th, td {
                padding: 0.5rem;
            }
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
        <h1>Progres Pengguna Kuis</h1>

        <form class="search-form" method="GET" action="admin_kuis.php" role="search" aria-label="Form pencarian pengguna">
            <input 
                type="search" 
                name="search" 
                class="search-input" 
                placeholder="Cari username..." 
                value="<?php echo htmlspecialchars($searchKeyword); ?>" 
                aria-label="Masukkan kata kunci pencarian username" 
                />
            <button type="submit" class="search-button" aria-label="Cari">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    // Output data dari setiap baris
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['points']) . "</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='reset_points_user' value='" . htmlspecialchars($row['username']) . "'>
                                        <button type='submit' class='btn btn-warning'>Reset Poin</button>
                                    </form>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='delete_user' value='" . htmlspecialchars($row['username']) . "'>
                                        <button type='submit' class='btn btn-danger'>Hapus Username</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Tidak ada data pengguna.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conn->close(); // Tutup koneksi database
?>
