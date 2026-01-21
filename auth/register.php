<?php
session_start();
require '../config/database.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi sederhana
    if (empty($username) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        try {
            // Cek apakah username sudah terdaftar
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetchColumn() > 0) {
                $error = "Username sudah digunakan!";
            } else {
                // Enkripsi password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Simpan user baru
                $ins = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if ($ins->execute([$username, $hashed_password])) {
                    $success = "Akun berhasil dibuat! Silakan login.";
                }
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar | MusicRec</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .auth-card { 
            background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%; max-width: 400px; text-align: center;
        }
        .auth-card h2 { color: #333; margin-bottom: 10px; font-weight: 600; }
        .auth-card p { color: #777; margin-bottom: 30px; font-size: 0.9rem; }
        .input-group { text-align: left; margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #555; }
        input { 
            width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 10px; 
            font-family: inherit; transition: 0.3s; box-sizing: border-box;
        }
        input:focus { border-color: #3498db; outline: none; box-shadow: 0 0 8px rgba(52,152,219,0.2); }
        .btn-primary { 
            width: 100%; background: #27ae60; color: white; border: none; padding: 13px; 
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-primary:hover { background: #219150; transform: translateY(-2px); }
        .error { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 20px; }
        .success { background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div style="font-size: 2rem; color: #3498db; font-weight: bold; margin-bottom: 10px;">MusicRec</div>
        <h2>Buat Akun</h2>
        <p>Daftar sekarang untuk mulai eksplorasi musik</p>

        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Pilih username unik..." required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Buat password aman..." required>
            </div>
            <div class="input-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" placeholder="Ulangi password..." required>
            </div>
            <button type="submit" class="btn-primary">Daftar Akun</button>
        </form>
        
        <div style="margin-top: 25px; font-size: 0.85rem; color: #888;">
            Sudah punya akun? <a href="login.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Login</a>
        </div>
    </div>
</body>
</html>