<?php

/**
 * Menghitung kedekatan antara dua pengguna dengan Cosine Similarity.
 * Sekarang menggunakan data RatingScore yang sudah tersedia di tabel matriks.
 */
function calculateSimilarity($userA_data, $userB_data) {
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;
    
    // Mengambil daftar lagu unik yang ada pada kedua user
    $allTracks = array_unique(array_merge(array_keys($userA_data), array_keys($userB_data)));

    foreach ($allTracks as $track) {
        $valA = $userA_data[$track] ?? 0;
        $valB = $userB_data[$track] ?? 0;
        
        $dotProduct += $valA * $valB;
        $normA += pow($valA, 2);
        $normB += pow($valB, 2);
    }
    
    if ($normA == 0 || $normB == 0) return 0;
    
    return $dotProduct / (sqrt($normA) * sqrt($normB));
}

/**
 * Fungsi utama untuk mendapatkan rekomendasi musik.
 * Diupdate: Mengambil data langsung dari tabel 'mytable_matrix'.
 */
function getMusicRecommendations($pdo, $currentUsername) {
    // 1. MENGAMBIL DATA DARI TABEL MATRIKS (Database-based Preprocessing)
    // Kita tidak perlu lagi COUNT(*) atau GROUP BY karena data sudah matang di tabel ini.
    $stmt = $pdo->query("SELECT Username, Track, RatingScore FROM mytable_matrix");
    $matrix = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Memasukkan hasil RatingScore (1-5) ke dalam matriks memori
        $matrix[$row['Username']][$row['Track']] = (int)$row['RatingScore'];
    }

    // Cek apakah user yang sedang login memiliki data di matriks
    if (!isset($matrix[$currentUsername])) {
        return [[], []];
    }

    $myRatings = $matrix[$currentUsername];
    $similarities = [];

    // 2. MENGHITUNG SIMILARITY BERDASARKAN RATING DI DATABASE
    foreach ($matrix as $otherUser => $userTracks) {
        if ($otherUser !== $currentUsername) {
            $sim = calculateSimilarity($myRatings, $userTracks);
            if ($sim > 0) {
                $similarities[$otherUser] = $sim;
            }
        }
    }

    // Urutkan berdasarkan kemiripan tertinggi
    arsort($similarities);

    // 3. MENGUMPULKAN REKOMENDASI (PREDICTION SCORE)
    $recs = [];
    foreach ($similarities as $neighbor => $simScore) {
        foreach ($matrix[$neighbor] as $track => $rating) {
            // Syarat: User belum pernah mendengarkan lagu ini (tidak ada di riwayat)
            if (!isset($myRatings[$track])) {
                if (!isset($recs[$track])) {
                    $recs[$track] = 0;
                }
                // Rumus Prediksi: Rating dari User Mirip * Skor Kemiripan
                $recs[$track] += ($rating * $simScore);
            }
        }
    }

    // Urutkan lagu dari skor prediksi tertinggi
    arsort($recs);
    
    // Mengembalikan 5 lagu rekomendasi teratas dan 3 user paling mirip
    return [
        array_slice($recs, 0, 5, true), 
        array_slice($similarities, 0, 10, true)
    ];
}