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

<style>
    /* Animasi */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .welcome-banner {
        background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
        color: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .welcome-banner p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-top: 10px;
    }

    .history-container {
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(12px);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .history-title {
        color: #f8fafc;
        margin-bottom: 25px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .history-list {
        display: grid;
        gap: 15px;
    }

    .history-item {
        background: #0f172a;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        border: 1px solid #334155;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
        border-left: 5px solid transparent;
    }

    .history-item:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 12px 20px rgba(0,0,0,0.4);
        border-left: 5px solid #3b82f6;
        background: #1e293b;
    }

    .track-info {
        display: flex;
        flex-direction: column;
    }

    .track-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 5px;
    }

    .artist-name {
        color: #fca5a5;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .album-name {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 3px;
    }

    .time-info {
        text-align: right;
        font-size: 0.85rem;
        color: #cbd5e1;
        background: #1e293b;
        padding: 8px 12px;
        border-radius: 8px;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #475569;
    }
</style>

<div class="welcome-banner">
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>! <i class="fa-solid fa-hand-sparkles"></i></h1>
    <p>Temukan musik yang sesuai dengan selera Anda hari ini.</p>
</div>

<div class="history-container">
    <h3 class="history-title"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Musik Terakhir Anda</h3>
    
    <div class="history-list">
        <?php if(empty($history)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-music"></i>
                <p>Belum ada riwayat musik. Ayo mulai cari dan dengarkan lagu favoritmu!</p>
            </div>
        <?php else: ?>
            <?php foreach($history as $index => $h): ?>
            <div class="history-item" style="animation-delay: <?= $index * 0.1 ?>s;">
                <div class="track-info">
                    <span class="track-name"><?= htmlspecialchars($h['Track']) ?></span>
                    <span class="artist-name"><i class="fa-solid fa-microphone-lines"></i> <?= htmlspecialchars($h['Artist']) ?></span>
                    <span class="album-name"><i class="fa-solid fa-record-vinyl"></i> <?= htmlspecialchars($h['Album']) ?></span>
                </div>
                <div class="time-info">
                    <div><i class="fa-regular fa-calendar"></i> <?= $h['Date'] ?></div>
                    <div style="margin-top: 4px;"><i class="fa-regular fa-clock"></i> <?= $h['Time'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>