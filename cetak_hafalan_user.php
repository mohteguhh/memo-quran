<?php
session_start();
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

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil username dari parameter URL
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Query untuk mengambil data hafalan berdasarkan username
$sql = "SELECT bookmarks.surah_number, bookmarks.ayat_number, bookmarks.created_at, users.kelas, users.semester, users.jurusan
        FROM bookmarks
        INNER JOIN users ON bookmarks.user_id = users.id
        WHERE users.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Array nama surat
$surah_names = [
    1 => "Surat Al-Fatihah",
    2 => "Surat Al-Baqarah",
    3 => "Surat Ali 'Imran",
    4 => "Surat An-Nisa",
    5 => "Surat Al-Ma'idah",
    6 => "Surat Al-An'am",
    7 => "Surat Al-A'raf",
    8 => "Surat Al-Anfal",
    9 => "Surat At-Tawbah",
    10 => "Surat Yunus",
    11 => "Surat Hud",
    12 => "Surat Yusuf",
    13 => "Surat Ar-Ra'd",
    14 => "Surat Ibrahim",
    15 => "Surat Al-Hijr",
    16 => "Surat An-Nahl",
    17 => "Surat Al-Isra",
    18 => "Surat Al-Kahf",
    19 => "Surat Maryam",
    20 => "Surat Taha",
    21 => "Surat Al-Anbiya",
    22 => "Surat Al-Hajj",
    23 => "Surat Al-Mu'minun",
    24 => "Surat An-Nur",
    25 => "Surat Al-Furqan",
    26 => "Surat Ash-Shu'ara",
    27 => "Surat An-Naml",
    28 => "Surat Al-Qasas",
    29 => "Surat Al-Ankabut",
    30 => "Surat Ar-Rum",
    31 => "Surat Luqman",
    32 => "Surat As-Sajda",
    33 => "Surat Al-Ahzab",
    34 => "Surat Saba",
    35 => "Surat Fatir",
    36 => "Surat Ya-Sin",
    37 => "Surat As-Saffat",
    38 => "Surat Sad",
    39 => "Surat Az-Zumar",
    40 => "Surat Ghafir",
    41 => "Surat Fussilat",
    42 => "Surat Ash-Shura",
    43 => "Surat Az-Zukhruf",
    44 => "Surat Ad-Dukhan",
    45 => "Surat Al-Jathiya",
    46 => "Surat Al-Ahqaf",
    47 => "Surat Muhammad",
    48 => "Surat Al-Fath",
    49 => "Surat Al-Hujurat",
    50 => "Surat Qaf",
    51 => "Surat Adh-Dhariyat",
    52 => "Surat At-Tur",
    53 => "Surat An-Najm",
    54 => "Surat Al-Qamar",
    55 => "Surat Ar-Rahman",
    56 => "Surat Al-Waqi'a",
    57 => "Surat Al-Hadid",
    58 => "Surat Al-Mujadila",
    59 => "Surat Al-Hashr",
    60 => "Surat Al-Mumtahana",
    61 => "Surat As-Saff",
    62 => "Surat Al-Jumu'a",
    63 => "Surat Al-Munafiqun",
    64 => "Surat At-Taghabun",
    65 => "Surat At-Talaq",
    66 => "Surat At-Tahrim",
    67 => "Surat Al-Mulk",
    68 => "Surat Al-Qalam",
    69 => "Surat Al-Haaqqa",
    70 => "Surat Al-Ma'arij",
    71 => "Surat Nuh",
    72 => "Surat Al-Jinn",
    73 => "Surat Al-Muzzammil",
    74 => "Surat Al-Muddathir",
    75 => "Surat Al-Qiyama",
    76 => "Surat Al-Insan",
    77 => "Surat Al-Mursalat",
    78 => "Surat An-Naba",
    79 => "Surat An-Nazi'at",
    80 => "Surat Abasa",
    81 => "Surat At-Takwir",
    82 => "Surat Al-Infitar",
    83 => "Surat Al-Mutaffifin",
    84 => "Surat Al-Inshiqaq",
    85 => "Surat Al-Burooj",
    86 => "Surat At-Takwir",
    87 => "Surat Al-A'la",
    88 => "Surat Al-Ghashiya",
    89 => "Surat Al-Fajr",
    90 => "Surat Al-Balad",
    91 => "Surat Ash-Shams",
    92 => "Surat Al-Lail",
    93 => "Surat Ad-Dhuha",
    94 => "Surat Al-Inshirah",
    95 => "Surat At-Tin",
    96 => "Surat Al-Alaq",
    97 => "Surat Al-Qadr",
    98 => "Surat Al-Bayyina",
    99 => "Surat Az-Zalzalah",
    100 => "Surat Al-Adiyat",
    101 => "Surat Al-Qari'a",
    102 => "Surat At-Takathur",
    103 => "Surat Al-Asr",
    104 => "Surat Al-Humazah",
    105 => "Surat Al-Fil",
    106 => "Surat Quraish",
    107 => "Surat Al-Ma'un",
    108 => "Surat Al-Kawthar",
    109 => "Surat Al-Kafirun",
    110 => "Surat An-Nasr",
    111 => "Surat Al-Masad",
    112 => "Surat Al-Ikhlas",
    113 => "Surat Al-Falaq",
    114 => "Surat An-Nas",
];

// Memuat FPDF
require('fpdf/fpdf.php');

// Membuat objek PDF
$pdf = new FPDF();
$pdf->AddPage();

// Menambahkan gambar latar belakang dari URL
$pdf->Image('https://memoquran.my.id/background_capaian_hafalan.jpg', 0, 0, 210, 297); // Sesuaikan ukuran dan posisi sesuai kebutuhan

// Menambahkan jarak sebelum judul
$pdf->Ln(60); // Geser ke bawah lebih jauh

$pdf->SetFont('Arial', 'B', 24);
$pdf->Cell(0, 10, strtoupper('Capaian Hafalan'), 0, 1, 'C'); // Judul dalam huruf kapital
$pdf->Ln(10);

// Menampilkan nama pengguna
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, strtoupper($username), 0, 1, 'C'); // Rata tengah
$pdf->Ln(5);

// Menampilkan kelas, semester, dan jurusan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $kelas = htmlspecialchars($row['kelas']);
    $semester = htmlspecialchars($row['semester']);
    $jurusan = htmlspecialchars($row['jurusan']);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Kelas: $kelas, $semester, Jurusan: $jurusan", 0, 1, 'C'); // Rata tengah
    $pdf->Ln(10);
}

// Menambahkan header tabel
$pdf->SetFont('Arial', 'B', 12, 'C');
$pdf->Cell(60, 10, 'Tanggal', 1, 0, 'C'); // Lebar kolom Tanggal diperlebar
$pdf->Cell(70, 10, 'Surah', 1, 0, 'C'); // Lebar kolom Surah
$pdf->Cell(60, 10, 'Ayat', 1, 0, 'C'); // Lebar kolom Ayat
$pdf->Ln();

// Menampilkan data
$pdf->SetFont('Arial', '', 12, 'C');
if ($result->num_rows > 0) {
    // Reset pointer ke awal hasil
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, htmlspecialchars($row['created_at']), 1, 0, 'C'); // Rata tengah
        $surah_name = isset($surah_names[$row['surah_number']]) ? $surah_names[$row['surah_number']] : "Tidak Diketahui";
        $pdf->Cell(70, 10, htmlspecialchars($surah_name), 1, 0, 'C'); // Rata tengah
        $pdf->Cell(60, 10, htmlspecialchars($row['ayat_number']), 1, 1, 'C'); // Rata tengah
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data', 1, 1, 'C');
}

// Output PDF
$pdf->Output('D', 'capaian_hafalan_' . $username . '.pdf');
?>
