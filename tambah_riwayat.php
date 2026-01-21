<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $track = $_POST['track'];
    $artist = $_POST['artist'];
    $album = $_POST['album'];
    $date = date('d-M-y');
    $time = date('H:i');

    try {
        // --- TAHAP 1: SIMPAN KE RIWAYAT TRANSAKSI (mytable) ---
        // Mencari ID terakhir untuk FIELD1
        $stmtId = $pdo->query("SELECT MAX(FIELD1) as max_id FROM mytable");
        $row = $stmtId->fetch();
        $new_id = ($row['max_id']) ? $row['max_id'] + 1 : 1;

        // Insert data ke tabel riwayat utama
        $stmt = $pdo->prepare("INSERT INTO mytable (FIELD1, Username, Artist, Track, Album, Date, Time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$new_id, $username, $artist, $track, $album, $date, $time]);


        // --- TAHAP 2: UPDATE DATA MATRIKS (mytable_matrix) ---
        // 1. Hitung total pemutaran (PlayCount) lagu ini oleh user tersebut secara real-time
        $stmtCount = $pdo->prepare("SELECT COUNT(*) as total FROM mytable WHERE Username = ? AND Track = ?");
        $stmtCount->execute([$username, $track]);
        $count = $stmtCount->fetch()['total'];

        // 2. Tentukan RatingScore berdasarkan Logika Kelipatan 5:
        // 1-4 kali (1), 5-9 kali (2), 10-14 kali (3), 15-19 kali (4), >=20 kali (5)
        if ($count >= 20) {
            $rating = 5;
        } else {
            $rating = (int)(floor($count / 5) + 1);
        }

        // 3. Cek apakah record (User + Lagu) sudah ada di tabel matriks
        $check = $pdo->prepare("SELECT id FROM mytable_matrix WHERE Username = ? AND Track = ?");
        $check->execute([$username, $track]);
        
        if ($check->rowCount() > 0) {
            // Update data jika record sudah ada
            $upd = $pdo->prepare("UPDATE mytable_matrix SET PlayCount = ?, RatingScore = ? WHERE Username = ? AND Track = ?");
            $upd->execute([$count, $rating, $username, $track]);
        } else {
            // Masukkan data baru jika record belum ada
            $ins = $pdo->prepare("INSERT INTO mytable_matrix (Username, Track, PlayCount, RatingScore) VALUES (?, ?, ?, ?)");
            $ins->execute([$username, $track, $count, $rating]);
        }

        // Redirect kembali ke halaman utama dengan notifikasi sukses
        header("Location: index.php?msg=success");
        exit();

    } catch (PDOException $e) {
        // Menampilkan pesan error jika terjadi masalah pada database
        die("Error Database: " . $e->getMessage());
    }
} else {
    // Jika diakses tanpa metode POST atau belum login
    header("Location: index.php");
    exit();
}