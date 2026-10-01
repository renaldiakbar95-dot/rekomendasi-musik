<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicRec - Sistem Rekomendasi Musik</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --dark-bg: #0f172a;
            --input-bg: #1e293b;
            --text-light: #f8fafc;
            --hover-color: #2563eb;
            --bg-color: #0b0f19;
            --text-color: #cbd5e1;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background: var(--bg-color); 
            margin: 0; 
            color: var(--text-color);
        }

        .navbar { 
            background: var(--dark-bg); 
            padding: 12px 0; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.2); 
            position: sticky; 
            top: 0; 
            z-index: 1000;
        }

        .nav-container { 
            width: 90%; 
            margin: auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            gap: 15px;
        }

        .logo { 
            font-size: 1.6rem; 
            font-weight: 700; 
            color: var(--primary-color); 
            text-decoration: none; 
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 150px;
        }

        .search-box { 
            flex-grow: 1; 
            max-width: 600px; 
            position: relative; 
            display: flex;
            align-items: center;
        }

        .search-icon-left {
            position: absolute;
            left: 15px;
            color: #bdc3c7;
            pointer-events: none;
            z-index: 2;
        }

        .search-box input { 
            width: 100%; 
            padding: 10px 85px 10px 45px; 
            border-radius: 30px; 
            border: 1px solid #445b73; 
            background: var(--input-bg); 
            color: white; 
            outline: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-search-submit {
            position: absolute;
            right: 5px;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            z-index: 3;
        }

        .menu { 
            display: flex; 
            align-items: center; 
            min-width: 400px; /* Diperlebar agar muat menu baru */
            justify-content: flex-end;
        }

        .menu a { 
            color: var(--text-light); 
            text-decoration: none; 
            margin-left: 15px; 
            font-size: 0.85rem; 
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
        }

        .menu a:hover { color: var(--primary-color); }

        .btn-logout { 
            background: #e74c3c; 
            padding: 6px 15px !important; 
            border-radius: 15px; 
            font-weight: 600 !important;
        }

        .container { 
            width: 90%; 
            margin: 30px auto; 
            min-height: 80vh;
        }

        @media (max-width: 768px) {
            .search-box { display: none; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/music-recommender/index.php" class="logo">
                <i class="fa-solid fa-compact-disc"></i> 
                <span>MusicRec</span>
            </a>

            <?php if(isset($_SESSION['username'])): ?>
            <form action="/music-recommender/hasil_cari.php" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon-left"></i>
                <input type="text" name="q" placeholder="Cari lagu atau artis..." required autocomplete="off">
                <button type="submit" class="btn-search-submit">Cari</button>
            </form>
            <?php endif; ?>

            <div class="menu">
                <?php if(isset($_SESSION['username'])): ?>
                    <a href="/music-recommender/index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
                    <a href="/music-recommender/debug_data.php"><i class="fa-solid fa-table-cells"></i> Matriks Data</a>
                    <a href="/music-recommender/rekomendasi.php"><i class="fa-solid fa-wand-magic-sparkles"></i> Rekomendasi</a>
                    <a href="/music-recommender/auth/logout.php" class="btn-logout">Keluar</a>
                <?php else: ?>
                    <a href="/music-recommender/auth/login.php">Masuk</a>
                    <a href="/music-recommender/auth/register.php">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">