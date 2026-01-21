<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

require 'config/database.php';
include 'layouts/header.php';

$stmt = $pdo->prepare("SELECT Artist, Track, Album, Date, Time FROM mytable WHERE Username = ? ORDER BY FIELD1 DESC LIMIT 10");
$stmt->execute([$_SESSION['username']]);
$history = $stmt->fetchAll();
?>

<div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <h1>Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <h3>Riwayat Musik Terakhir Anda:</h3>
    
    <div style="display: grid; gap: 15px;">
        <?php if(empty($history)): ?>
            <p style="color: #999;">Belum ada riwayat. Ayo cari lagu!</p>
        <?php else: ?>
            <?php foreach($history as $h): ?>
            <div style="padding:15px; border-left:4px solid #3498db; background:#f9f9f9; border-radius:5px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong><?= htmlspecialchars($h['Track']) ?></strong> - <?= htmlspecialchars($h['Artist']) ?>
                    <br><small style="color: #888;"><?= htmlspecialchars($h['Album']) ?></small>
                </div>
                <div style="text-align: right; font-size: 0.8rem; color: #aaa;">
                    <?= $h['Date'] ?> <br> <?= $h['Time'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>