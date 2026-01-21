<?php
session_start();
require 'config/database.php';
include 'layouts/header.php';

if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

$limit_kolom = 10; 
$page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
$start_index = $page * $limit_kolom;

$query = "SELECT Username, Track, PlayCount, RatingScore FROM mytable_matrix";
$stmt = $pdo->query($query);

$matrix = [];
$all_tracks = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Menyimpan data ke dalam array matriks untuk ditampilkan di tabel
    $matrix[$row['Username']][$row['Track']] = [
        'asli' => $row['PlayCount'],
        'bobot' => $row['RatingScore']
    ];
    // Menyimpan daftar judul lagu unik untuk header tabel
    $all_tracks[$row['Track']] = true;
}

$unique_tracks = array_keys($all_tracks);
$total_lagu = count($unique_tracks);
$display_tracks = array_slice($unique_tracks, $start_index, $limit_kolom); 
?>

<div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <h2 style="color: #2c3e50;"><i class="fa-solid fa-database"></i> Database Matrix Viewer</h2>
    <p style="color: #7f8c8d;">Menampilkan data dari tabel <strong>mytable_matrix</strong> (Hasil Preprocessing Tersimpan).</p>
    
    <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
        <?php if ($page > 0): ?>
            <a href="?p=<?= $page - 1 ?>" style="padding: 8px 15px; background: #3498db; color: white; border-radius: 5px; text-decoration: none; font-size: 0.85rem;">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <span style="font-size: 0.9rem; color: #7f8c8d;">Halaman Lagu: <?= $page + 1 ?></span>

        <?php if ($start_index + $limit_kolom < $total_lagu): ?>
            <a href="?p=<?= $page + 1 ?>" style="padding: 8px 15px; background: #3498db; color: white; border-radius: 5px; text-decoration: none; font-size: 0.85rem;">Berikutnya &raquo;</a>
        <?php endif; ?>
    </div>

    <div style="overflow-x: auto; border: 1px solid #ddd; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 0.8rem;">
            <thead>
                <tr style="background: #2c3e50; color: white;">
                    <th style="padding: 12px; border: 1px solid #444; min-width: 150px; position: sticky; left: 0; background: #2c3e50; z-index: 2;">Username</th>
                    <?php foreach($display_tracks as $t): ?>
                        <th style="padding: 12px; border: 1px solid #444; min-width: 120px;">
                            <?= htmlspecialchars(substr($t, 0, 15)) ?>...
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($matrix)): ?>
                    <tr>
                        <td colspan="<?= count($display_tracks) + 1 ?>" style="padding: 20px; color: #999;">Data matriks masih kosong. Putar lagu terlebih dahulu.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($matrix as $user => $tracks): ?>
                        <tr>
                            <td style="padding: 10px; font-weight: bold; background: #f9f9f9; border: 1px solid #ddd; position: sticky; left: 0; z-index: 1;">
                                <?= htmlspecialchars($user) ?>
                            </td>
                            <?php foreach($display_tracks as $t): ?>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php if(isset($tracks[$t])): ?>
                                        <span style="color: #888; font-size: 0.7rem;"><?= $tracks[$t]['asli'] ?> Plays</span><br>
                                        <div style="background: #fff3e0; color: #e67e22; border: 1px solid #ffe0b2; border-radius: 5px; padding: 2px; font-weight: bold; margin-top: 5px;">
                                            Score: <?= $tracks[$t]['bobot'] ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #ccc;">0</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 25px; padding: 20px; background: #fdfae8; border-left: 5px solid #f1c40f; border-radius: 5px;">
        <strong style="color: #7f4f01;"><i class="fa-solid fa-circle-info"></i> Info Arsitektur Sistem:</strong><br>
        <p style="margin: 10px 0; color: #7f4f01; font-size: 0.9rem;">
            Tabel di atas adalah visualisasi dari tabel database <strong>mytable_matrix</strong>. 
            Data ini di-update secara otomatis oleh sistem setiap kali user menekan tombol <strong>PLAY</strong> di halaman Rekomendasi atau hasil pencarian.
        </p>
        <ul style="font-size: 0.85rem; color: #7f4f01;">
            <li>PlayCount: Jumlah asli lagu diputar (Real Data).</li>
            <li>Rating Score: Nilai transformasi (1-5) untuk algoritma Cosine Similarity.</li>
        </ul>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>