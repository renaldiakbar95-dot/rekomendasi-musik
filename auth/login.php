<?php
session_start();
require '../config/database.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | MusicRec</title>
    <link rel="stylesheet" href="/music-recommender/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;
        }
        .auth-card { 
            background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%; max-width: 400px; text-align: center;
        }
        .auth-card h2 { color: #333; margin-bottom: 10px; font-weight: 600; }
        .auth-card p { color: #777; margin-bottom: 30px; font-size: 0.9rem; }
        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #555; }
        input { 
            width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 10px; 
            font-family: inherit; transition: 0.3s;
        }
        input:focus { border-color: #3498db; outline: none; box-shadow: 0 0 8px rgba(52,152,219,0.2); }
        .btn-primary { 
            width: 100%; background: #3498db; color: white; border: none; padding: 13px; 
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .btn-primary:hover { background: #2980b9; transform: translateY(-2px); }
        .error { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div style="font-size: 2rem; color: #3498db; font-weight: bold; margin-bottom: 10px;">MusicRec</div>
        <h2>Selamat Datang</h2>
        <p>Masuk untuk melihat rekomendasi musikmu</p>

        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username..." required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-primary">Masuk Sekarang</button>
        </form>
        <div style="margin-top: 25px; font-size: 0.85rem; color: #888;">
            Belum punya akun? <a href="register.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Daftar</a>
        </div>
    </div>
</body>
</html>