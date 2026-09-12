<?php
$host = 'localhost';
$dbname = 'penghafal_alquran';
$username = 'root';
$password = ''; // Default XAMPP tidak pakai password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
