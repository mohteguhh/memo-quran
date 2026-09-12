<?php
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

// Inisialisasi variabel filter
$filterName = isset($_POST['filter_name']) ? $_POST['filter_name'] : '';
$filterClass = isset($_POST['filter_class']) ? $_POST['filter_class'] : '';
$filterSemester = isset($_POST['filter_semester']) ? $_POST['filter_semester'] : '';
$filterMajor = isset($_POST['filter_major']) ? $_POST['filter_major'] : '';

// Query untuk mengambil data bookmark pengguna dengan filter
$sql = "SELECT
            users.id AS user_id,
            users.username,
            bookmarks.surah_number,
            bookmarks.ayat_number,
            bookmarks.created_at,
            users.kelas,
            users.semester,
            users.jurusan
        FROM bookmarks
        INNER JOIN users ON bookmarks.user_id = users.id
        WHERE (users.username LIKE ? OR ? = '')
        AND (users.kelas LIKE ? OR ? = '')
        AND (users.semester LIKE ? OR ? = '')
        AND (users.jurusan LIKE ? OR ? = '')
        ORDER BY users.username, bookmarks.created_at DESC";

$stmt = $conn->prepare($sql);
$filterNameParam = "%$filterName%";
$filterClassParam = "%$filterClass%";
$filterSemesterParam = "%$filterSemester%";
$filterMajorParam = "%$filterMajor%";
$stmt->bind_param("ssssssss", $filterNameParam, $filterName, $filterClassParam, $filterClass, $filterSemesterParam, $filterSemester, $filterMajorParam, $filterMajor);
$stmt->execute();
$result = $stmt->get_result();

// Struktur data untuk pengelompokan
$groupedBookmarks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $username = $row["username"];
        if (!isset($groupedBookmarks[$username])) {
            $groupedBookmarks[$username] = [];
        }
        $groupedBookmarks[$username][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Hafalan (Bookmarks)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
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
        /* Tombol Logout */
        .logout-btn {
            background-color: red; /* Warna merah untuk tombol logout */
        }
        .logout-btn:hover {
            background-color: darkred; /* Warna lebih gelap saat hover */
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
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form input, .filter-form select {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-form button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #388E3C;
        }
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
        .print-button {
            padding: 10px 15px;
            background-color: #4CAF50; /* Warna hijau */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
        }
        .print-button:hover {
            background-color: #388E3C; /* Warna hijau lebih gelap saat hover */
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
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>
    </div>

    <div class="content">
        <h1>Capaian Hafalan</h1>

        <form method="POST" class="filter-form">
            <input type="text" name="filter_name" placeholder="Nama" value="<?php echo htmlspecialchars($filterName); ?>" />
            <input type="text" name="filter_class" placeholder="Kelas" value="<?php echo htmlspecialchars($filterClass); ?>" />
            <input type="text" name="filter_semester" placeholder="Semester" value="<?php echo htmlspecialchars($filterSemester); ?>" />
            <input type="text" name="filter_major" placeholder="Jurusan" value="<?php echo htmlspecialchars($filterMajor); ?>" />
            <button type="submit">Filter</button>
        </form>

        <?php
        if (!empty($groupedBookmarks)) {
            foreach ($groupedBookmarks as $username => $bookmarks) {
                echo "<h2>" . htmlspecialchars($username) . "</h2>";
                echo "<a href='cetak_hafalan_user.php?username=" . urlencode($username) . "' class='print-button'>Cetak</a>";
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Surah</th>";
                echo "<th>Ayat</th>";
                echo "<th>Tanggal</th>";
                echo "<th>Kelas</th>";
                echo "<th>Semester</th>";
                echo "<th>Jurusan</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                foreach ($bookmarks as $bookmark) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($bookmark["surah_number"]) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["ayat_number"]) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["created_at"]) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["kelas"]) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["semester"]) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["jurusan"]) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        } else {
            echo "<p>Tidak ada data bookmark.</p>";
        }
        ?>
    </div>
</body>
</html>
