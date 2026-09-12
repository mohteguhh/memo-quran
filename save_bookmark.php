<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

// Koneksi ke database
$host = 'localhost';
$db = 'memf3243_penghafal_alquran';
$user = 'memf3243';
$pass = 'jWNxKV6GdcqE87';

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);
$surah_number = $data['surah_number'];
$ayat_number = $data['ayat_number'];

// Query untuk menyimpan bookmark
$sql = "INSERT INTO bookmarks (user_id, surah_number, ayat_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $surah_number, $ayat_number);

// Eksekusi query
if ($stmt->execute()) {
    echo json_encode(["message" => "Bookmark berhasil disimpan!"]);
} else {
    echo json_encode(["message" => "Kesalahan saat menyimpan bookmark: " . $stmt->error]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
