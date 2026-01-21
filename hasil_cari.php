<?php
session_start();
if (!isset($_SESSION['username'])) header("Location: auth/login.php");

require 'config/database.php';
include 'layouts/header.php';

$q = $_GET['q'] ?? '';
// Query untuk mencari lagu berdasarkan judul atau artis
$stmt = $pdo->prepare("SELECT DISTINCT Artist, Track, Album FROM mytable WHERE Track LIKE ? OR Artist LIKE ? LIMIT 20");
$stmt->execute(["%$q%", "%$q%"]);
$results = $stmt->fetchAll();
?>

<div class="recommendation-container">
    <div class="section-header" style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 5px;">
            <i class="fa-solid fa-magnifying-glass"></i> Hasil Pencarian: "<?= htmlspecialchars($q) ?>"
        </h2>
        <p style="color: #7f8c8d;">
            Menampilkan <?= count($results) ?> lagu yang sesuai dengan kata kunci Anda.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        
        <?php if(count($results) > 0): ?>
            <?php foreach($results as $r): ?>
                <div class="song-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #3498db; transition: 0.3s;">
                    <div style="font-size: 0.7rem; color: #3498db; font-weight: bold; margin-bottom: 5px;">HASIL PENCARIAN</div>
                    <div style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 5px;"><?= htmlspecialchars($r['Track']) ?></div>
                    <div style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 20px;"><?= htmlspecialchars($r['Artist']) ?></div>
                    
                    <form action="tambah_riwayat.php" method="POST">
                        <input type="hidden" name="track" value="<?= htmlspecialchars($r['Track']) ?>">
                        <input type="hidden" name="artist" value="<?= htmlspecialchars($r['Artist']) ?>">
                        <input type="hidden" name="album" value="<?= htmlspecialchars($r['Album']) ?>">
                        <button type="submit" style="width: 100%; background: #3498db; color: white; border: none; padding: 12px; border-radius: 25px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;">
                           <i class="fa-solid fa-play"></i> PLAY
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 15px;">
                <i class="fa-solid fa-face-frown" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                <p style="color: #7f8c8d;">Maaf, lagu atau artis yang Anda cari tidak ditemukan.</p>
                <a href="index.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Kembali ke Beranda</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    .song-card:hover { transform: translateY(-5px); }
    .song-card button { transition: 0.2s; }
    .song-card button:hover { background: #2c3e50 !important; transform: scale(1.05); }
</style>

<?php include 'layouts/footer.php'; ?>