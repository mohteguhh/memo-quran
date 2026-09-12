<?php
session_start();
$host = 'localhost';
$db = 'memf3243_penghafal_alquran';
$user = 'memf3243';
$pass = 'jWNxKV6GdcqE87';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id']; // Ambil ID pengguna dari sesi
$surah_number = $data['surahNumber'];
$ayat_number = $data['ayatNumber'];

$sql = "DELETE FROM bookmarks WHERE user_id = ? AND surah_number = ? AND ayat_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $surah_number, $ayat_number);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
?>
