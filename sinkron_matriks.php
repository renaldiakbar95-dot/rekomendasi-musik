<?php
session_start();
require 'config/database.php';
include 'layouts/header.php';

// Pastikan hanya admin atau user login yang bisa akses (opsional)
if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}
?>

<div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 800px; margin: 20px auto;">
    <h2 style="color: #2c3e50;"><i class="fa-solid fa-sync"></i> Sinkronisasi Data Matriks</h2>
    <p style="color: #7f8c8d;">Klik tombol di bawah untuk memproses data riwayat lama dari <code>mytable</code> ke <code>mytable_matrix</code>.</p>

    <?php
    if (isset($_POST['proses_sinkron'])) {
        try {
            // 1. Bersihkan tabel matriks lama agar tidak duplikat (Opsional, tergantung kebutuhan)
            $pdo->exec("TRUNCATE TABLE mytable_matrix");

            // 2. Ambil data agregat dari riwayat mentah
            $query = "SELECT Username, Track, COUNT(*) as total FROM mytable GROUP BY Username, Track";
            $stmt = $pdo->query($query);
            $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count_inserted = 0;

            foreach ($all_data as $row) {
                $user = $row['Username'];
                $track = $row['Track'];
                $play_count = (int)$row['total'];

                // 3. Logika Kelipatan 5
                if ($play_count >= 20) {
                    $rating = 5;
                } else {
                    $rating = (int)(floor($play_count / 5) + 1);
                }

                // 4. Masukkan ke tabel matriks
                $ins = $pdo->prepare("INSERT INTO mytable_matrix (Username, Track, PlayCount, RatingScore) VALUES (?, ?, ?, ?)");
                $ins->execute([$user, $track, $play_count, $rating]);
                $count_inserted++;
            }

            echo "<div style='padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-top: 20px;'>
                    <strong>Berhasil!</strong> $count_inserted data riwayat telah berhasil dikonversi ke tabel matriks.
                  </div>";
        } catch (PDOException $e) {
            echo "<div style='padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px; margin-top: 20px;'>
                    <strong>Error:</strong> " . $e->getMessage() . "
                  </div>";
        }
    }
    ?>

    <form method="POST" style="margin-top: 30px;">
        <button type="submit" name="proses_sinkron" style="padding: 15px 30px; background: #27ae60; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 1rem; width: 100%;">
            Mulai Proses Migrasi Data Sekarang
        </button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="debug_data.php" style="color: #3498db; text-decoration: none;">&larr; Lihat Hasil di Tabel Matriks</a>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>