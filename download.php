<?php
session_start();

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

// Tutup tag PHP sebelum beralih ke HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi Memo Quran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
            text-align: center;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        h1 {
            color: #2c7da0;
        }
        .download-btn {
            display: inline-block;
            background-color: #2c7da0;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .download-btn:hover {
            background-color: #1a5d7a;
        }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left: 4px solid #2c7da0;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
       
        <div id="status">
            <p>Mempersiapkan download...</p>
            <div class="spinner"></div>
        </div>
        
        <p>Jika download tidak dimulai secara otomatis, klik tombol di bawah ini:</p>
        <a href="com_memo_quran.apk" class="download-btn" download>Download Manual</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Membuat elemen <a> sementara untuk memicu download
            const downloadLink = document.createElement('a');
            downloadLink.href = 'com_memo_quran.apk';
            downloadLink.download = 'com_memo_quran.apk';
            
            // Simulasi klik pada link untuk memulai download
            downloadLink.click();
            
            // Mengubah status
            document.getElementById('status').innerHTML = '<p>Download telah dimulai.</p>';
        });
    </script>
</body>
</html>