<?php
session_start();
if (!isset($_SESSION['username'])) header("Location: auth/login.php");
require 'config/database.php';
include 'layouts/header.php';

$search_results = [];
if (isset($_GET['q'])) {
    $q = "%" . $_GET['q'] . "%";
    $stmt = $pdo->prepare("SELECT DISTINCT Artist, Track, Album FROM mytable WHERE Track LIKE ? OR Artist LIKE ? LIMIT 20");
    $stmt->execute([$q, $q]);
    $search_results = $stmt->fetchAll();
}
?>

<div class="search-container">
    <h2>Cari Musik</h2>
    <form action="" method="GET" style="margin-bottom: 30px; display: flex; gap: 10px;">
        <input type="text" name="q" placeholder="Cari judul lagu atau artis..." 
               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
               style="flex-grow: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
        <button type="submit" style="padding: 12px 25px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer;">Cari</button>
    </form>

    <div class="results">
        <?php if ($search_results): ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($search_results as $s): ?>
                    <div style="background: white; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <div>
                            <strong><?= htmlspecialchars($s['Track']) ?></strong><br>
                            <small style="color: #7f8c8d;"><?= htmlspecialchars($s['Artist']) ?> • <?= htmlspecialchars($s['Album']) ?></small>
                        </div>
                        <form action="tambah_riwayat.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="track" value="<?= htmlspecialchars($s['Track']) ?>">
                            <input type="hidden" name="artist" value="<?= htmlspecialchars($s['Artist']) ?>">
                            <input type="hidden" name="album" value="<?= htmlspecialchars($s['Album']) ?>">
                            <button type="submit" style="background: #27ae60; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 0.8rem;">+ Dengarkan</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($_GET['q'])): ?>
            <p style="color: #95a5a6;">Lagu tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>