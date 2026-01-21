<?php
session_start();
if (!isset($_SESSION['username'])) header("Location: auth/login.php");

require 'config/database.php';
require 'functions/recommender.php';
include 'layouts/header.php';

list($recs, $sims) = getMusicRecommendations($pdo, $_SESSION['username']);

$is_new_user = empty($recs);
if ($is_new_user) {
    $stmtPop = $pdo->query("SELECT Artist, Track, Album, COUNT(*) as total_putaran 
                            FROM mytable 
                            GROUP BY Track 
                            ORDER BY total_putaran DESC 
                            LIMIT 12");
    $popular_songs = $stmtPop->fetchAll();
}
?>

<div class="recommendation-container">
    <div class="section-header" style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 5px;">
            <?= $is_new_user ? "<i class='fa-solid fa-fire'></i> Lagu Trending Untuk Anda" : "<i class='fa-solid fa-wand-magic-sparkles'></i> Rekomendasi Spesial Untukmu" ?>
        </h2>
        <p style="color: #7f8c8d;">
            <?= $is_new_user ? "Lagu-lagu paling populer saat ini." : "Berdasarkan kemiripan seleramu dengan pendengar lain." ?>
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php if(!$is_new_user): ?>
            <?php foreach($recs as $track => $score): 
                $stmtDetail = $pdo->prepare("SELECT Artist, Album FROM mytable WHERE Track = ? LIMIT 1");
                $stmtDetail->execute([$track]);
                $detail = $stmtDetail->fetch();
            ?>
                <div class="song-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #3498db; transition: 0.3s;">
                    <div style="font-size: 0.7rem; color: #3498db; font-weight: bold; margin-bottom: 5px;">MATCH SCORE: <?= round($score, 2) ?></div>
                    <div style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 5px;"><?= htmlspecialchars($track) ?></div>
                    <div style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 20px;"><?= htmlspecialchars($detail['Artist'] ?? 'Unknown Artist') ?></div>
                    
                    <form action="tambah_riwayat.php" method="POST">
                        <input type="hidden" name="track" value="<?= htmlspecialchars($track) ?>">
                        <input type="hidden" name="artist" value="<?= htmlspecialchars($detail['Artist'] ?? 'Unknown Artist') ?>">
                        <input type="hidden" name="album" value="<?= htmlspecialchars($detail['Album'] ?? 'Unknown Album') ?>">
                        <button type="submit" style="width: 100%; background: #3498db; color: white; border: none; padding: 12px; border-radius: 25px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;">
                           <i class="fa-solid fa-play"></i> PLAY
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <?php foreach($popular_songs as $p): ?>
                <div class="song-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #000000;">
                    <div style="font-size: 0.7rem; color: #3300ff; font-weight: bold; margin-bottom: 5px;">TRENDING 🔥</div>
                    <div style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 5px;"><?= htmlspecialchars($p['Track']) ?></div>
                    <div style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 20px;"><?= htmlspecialchars($p['Artist']) ?></div>

                    <form action="tambah_riwayat.php" method="POST">
                        <input type="hidden" name="track" value="<?= htmlspecialchars($p['Track']) ?>">
                        <input type="hidden" name="artist" value="<?= htmlspecialchars($p['Artist']) ?>">
                        <input type="hidden" name="album" value="<?= htmlspecialchars($p['Album']) ?>">
                        <button type="submit" style="width: 100%; background: #2600ff; color: white; border: none; padding: 12px; border-radius: 25px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;">
                           <i class="fa-solid fa-play"></i> PLAY
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .song-card:hover { transform: translateY(-5px); }
    .song-card button { transition: 0.2s; }
    .song-card button:hover { background: #2c3e50 !important; transform: scale(1.05); }
</style>

<?php include 'layouts/footer.php'; ?>