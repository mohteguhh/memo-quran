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

// Cek jika ada permintaan POST untuk menghapus bookmark
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bookmark_id'])) {
    $deleteBookmarkId = $_POST['delete_bookmark_id'];

    // Query untuk menghapus bookmark
    $deleteSql = "DELETE FROM bookmarks WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $deleteBookmarkId);

    if ($stmt->execute()) {
        // Jika berhasil, tampilkan pesan sukses
        $successMessage = "Bookmark berhasil dihapus.";
    } else {
        // Jika gagal, tampilkan pesan error
        $errorMessage = "Gagal menghapus bookmark: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil keyword pencarian dari form
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$kelasFilter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : '';
$jurusanFilter = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

// Fungsi untuk mendapatkan data surah dari API Quran.com
function getSurahDataFromAPI() {
    $api_url = "https://api.quran.com/api/v4/chapters?language=id"; // Ganti 'id' dengan kode bahasa lain jika perlu
    $json_data = file_get_contents($api_url);

    if ($json_data === false) {
        return null; // Gagal mengambil data dari API
    }

    $response = json_decode($json_data, true);

    if ($response === null || !isset($response['chapters'])) {
        return null; // Gagal memproses data JSON
    }

    return $response['chapters'];
}

// Ambil data surah dari API
$surahData = getSurahDataFromAPI();

// Jika gagal mengambil data dari API, tampilkan pesan error
if ($surahData === null) {
    $errorMessage = "Gagal mengambil data surah dari API. Pastikan koneksi internet Anda stabil.";
}

// Query untuk mengambil data bookmark pengguna
$sql = "SELECT
            users.id AS user_id,
            users.username,
            bookmarks.id AS bookmark_id,  -- Include the bookmark ID
            bookmarks.surah_number,
            bookmarks.ayat_number,
            bookmarks.created_at,
            users.kelas,
            users.semester,
            users.jurusan
        FROM bookmarks
        INNER JOIN users ON bookmarks.user_id = users.id";

// Tambahkan kondisi WHERE jika ada keyword pencarian
if (!empty($searchKeyword)) {
    $sql .= " WHERE users.username LIKE '%" . $conn->real_escape_string($searchKeyword) . "%'";
}

// Tambahkan filter untuk kelas, semester, dan jurusan
if (!empty($kelasFilter)) {
    $sql .= " AND users.kelas = '" . $conn->real_escape_string($kelasFilter) . "'";
}
if (!empty($semesterFilter)) {
    $sql .= " AND users.semester = '" . $conn->real_escape_string($semesterFilter) . "'";
}
if (!empty($jurusanFilter)) {
    $sql .= " AND users.jurusan = '" . $conn->real_escape_string($jurusanFilter) . "'";
}

$sql .= " ORDER BY users.username, bookmarks.created_at DESC";

$result = $conn->query($sql);

// Struktur data untuk pengelompokan
$groupedBookmarks = [];
$userHighestSurah = []; // Array untuk menyimpan nomor surah tertinggi per user
$userHighestSurahName = []; // Array untuk menyimpan nama surah tertinggi per user

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $username = $row["username"];
        $surahNumber = $row["surah_number"];

        if (!isset($groupedBookmarks[$username])) {
            $groupedBookmarks[$username] = [];
            $userHighestSurah[$username] = 0; // Inisialisasi nomor surah tertinggi
            $userHighestSurahName[$username] = ""; // Inisialisasi nama surah tertinggi
        }
        $groupedBookmarks[$username][] = $row;

        // Perbarui nomor surah tertinggi jika perlu
        if ($surahNumber > $userHighestSurah[$username]) {
            $userHighestSurah[$username] = $surahNumber;

            // Cari nama surah berdasarkan nomor surah
            $surahName = "Surah " . $surahNumber; // Default name
            if ($surahData !== null) {
                foreach ($surahData as $surah) {
                    if ($surah['id'] == $surahNumber) {
                        $surahName = $surah['name_simple'];
                        break;
                    }
                }
            }
            $userHighestSurahName[$username] = $surahName;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Hafalan (Bookmarks)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
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

        /* Search Input Styles */
        .search-container {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center; /* Center the search container */
        }

        .search-input {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px; /* Kecilkan lebar kolom pencarian */
            font-size: 1rem;
            transition: border-color 0.3s;
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
        }

        .search-button:hover {
            background-color: #388E3C;
        }

        /* Button Styles */
        .btn {
            padding: 0.5rem 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-danger {
            background-color: #f44336; /* Warna merah untuk tombol hapus */
        }

        .btn-danger:hover {
            background-color: #d32f2f; /* Warna merah lebih gelap saat hover */
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

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                max-width: none;
                text-align: center;
            }

            .content {
                margin: 10px;
            }

            .search-input {
                width: 100%;
            }
        }

        /* Style untuk canvas chart */
        .chart-container {
            width: auto;
            margin: auto;
                    }
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
        <h1>Data Bookmark Pengguna</h1>

        <?php if (isset($successMessage)): ?>
            <div style="color: green;"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div style="color: red;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <!-- Canvas untuk Chart -->
        <div class="chart-container">
            <canvas id="bookmarkChart"></canvas>
        </div>

        <!-- Form Pencarian dan Filter -->
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" class="search-input" name="search" placeholder="Cari nama pengguna..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
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
                    <!-- Tambahkan jurusan lain jika ada -->
                </select>
                <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                <button type="button" class="search-button" onclick="resetFilters()">Reset</button>
            </form>
        </div>

        <?php
        if (!empty($groupedBookmarks)) {
            foreach ($groupedBookmarks as $username => $bookmarks) {
                echo "<h2>" . htmlspecialchars($username) . "</h2>";
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Surah</th>";
                echo "<th>Ayat</th>";
                echo "<th>Tanggal</th>";
                echo "<th>Kelas</th>"; // Tambahkan kolom kelas
                echo "<th>Semester</th>"; // Tambahkan kolom semester
                echo "<th>Jurusan</th>"; // Tambahkan kolom jurusan
                echo "<th>Aksi</th>"; // Tambahkan kolom aksi
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                foreach ($bookmarks as $bookmark) {
                    $surahNumber = $bookmark["surah_number"];
                    $surahName = "Nama Surah Tidak Ditemukan"; // Default value

                    // Cari nama surah berdasarkan nomor surah
                    if ($surahData !== null) {
                        foreach ($surahData as $surah) {
                            if ($surah['id'] == $surahNumber) {
                                $surahName = $surah['name_simple'];
                                break;
                            }
                        }
                    }

                    // Format tanggal
                    $formattedDate = $bookmark["created_at"];
                    $kelas = htmlspecialchars($bookmark["kelas"]);
                    $semester = htmlspecialchars($bookmark["semester"]);
                    $jurusan = htmlspecialchars($bookmark["jurusan"]);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($surahName) . "</td>";
                    echo "<td>" . htmlspecialchars($bookmark["ayat_number"]) . "</td>";
                    echo "<td>" . htmlspecialchars($formattedDate) . "</td>";
                    echo "<td>" . $kelas . "</td>"; // Tampilkan kelas
                    echo "<td>" . $semester . "</td>"; // Tampilkan semester
                    echo "<td>" . $jurusan . "</td>"; // Tampilkan jurusan
                    echo "<td>
                            <form method='POST' style='display:inline;'>";
    
                    // Check if 'bookmark_id' key exists before accessing it
                    if (isset($bookmark["bookmark_id"])) {
                        echo "<input type='hidden' name='delete_bookmark_id' value='" . $bookmark["bookmark_id"] . "'>";
                        echo "<button type='submit' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus bookmark ini?\");'>Hapus</button>";
                    } else {
                        echo "<span>Bookmark ID tidak ditemukan</span>"; // Optional: handle the case where ID is missing
                    }

                    echo "</form>
                          </td>";
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

    <script>
        // Data dari PHP
        var userHighestSurah = <?php echo json_encode($userHighestSurah); ?>;
        var userHighestSurahName = <?php echo json_encode($userHighestSurahName); ?>;

        // Ekstrak labels (usernames) dan data (nomor surah tertinggi)
        var labels = Object.keys(userHighestSurah);
        var data = Object.values(userHighestSurah);

        // Konfigurasi chart
        var ctx = document.getElementById('bookmarkChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nomor Surah Tertinggi',
                    data: data,
                    backgroundColor: 'rgba(76, 175, 80, 0.7)', // Warna hijau
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 114, // Set maksimum ke jumlah surah dalam Al-Quran
                        title: {
                            display: true,
                            text: 'Nomor Surah Tertinggi'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Pengguna'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Nomor Surah Tertinggi yang Dibookmark per Pengguna',
                        padding: 10,
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: false // Sembunyikan legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';

                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                label += ' (' + userHighestSurahName[context.dataIndex] + ')';
                                return label;
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, context) => {
                            return userHighestSurahName[context.dataIndex];
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
</body>
</html>
