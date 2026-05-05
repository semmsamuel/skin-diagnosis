<?php
include '../config/koneksi.php';

if(isset($_POST['reset_password'])){
    $email = $_POST['email'];
    $new_password = md5($_POST['new_password']);

    // Cek apakah email ada
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek_email) > 0){
        // Update password
        mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE email='$email'");
        header("Location: login.php?msg=password_updated");
        exit;
    } else {
        $error = "Alamat email tidak ditemukan dalam sistem kami.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password - Skin AI</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.85) 0%, rgba(20, 184, 166, 0.85) 100%), 
                    url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2000&auto=format&fit=crop') no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        padding: 20px;
    }

    .login-container {
        display: flex;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        min-height: 500px;
        animation: fadeIn 0.8s ease-out;
    }

    .login-image {
        flex: 1;
        background: url('https://images.unsplash.com/photo-1530497610245-94d3c16cda28?q=80&w=1000&auto=format&fit=crop') no-repeat center center;
        background-size: cover;
        position: relative;
        display: none;
    }

    @media (min-width: 768px) {
        .login-image {
            display: block;
        }
    }

    .login-image-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to bottom, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0.9));
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 40px;
        color: white;
    }

    .login-form-wrap {
        flex: 1;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    .logo-brand {
        font-size: 28px;
        font-weight: 800;
        color: #0ea5e9;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-brand i {
        background: #e0f2fe;
        color: #0ea5e9;
        padding: 10px;
        border-radius: 12px;
    }

    .welcome-text {
        color: #64748b;
        font-size: 15px;
        margin-bottom: 35px;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        font-size: 15px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .input-group-text {
        border-radius: 12px 0 0 12px;
        border: 2px solid #e2e8f0;
        border-right: none;
        background: #f8fafc;
        color: #94a3b8;
    }

    .form-control {
        border-left: none;
    }

    .btn-login {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 16px;
        color: white;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        margin-top: 10px;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
    }

    .back-link {
        color: #64748b;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }

    .back-link:hover {
        color: #0ea5e9;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>

<div class="login-container">
    <div class="login-image">
        <div class="login-image-overlay">
            <h3 class="fw-bold mb-2">Pemulihan Akun</h3>
            <p class="mb-0 opacity-75">Sistem pakar deteksi penyakit kulit berbasis AI. Pulihkan akses Anda untuk melanjutkan kesehatan kulit.</p>
        </div>
    </div>
    
    <div class="login-form-wrap">
        <div class="mb-4">
            <div class="logo-brand"><i class="fa fa-shield-halved"></i> Keamanan</div>
            <div class="welcome-text">Masukkan email yang terdaftar untuk mengatur ulang kata sandi Anda.</div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger" style="border-radius: 12px; font-size: 14px;">
                <i class="fa fa-circle-exclamation me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold" style="font-size: 14px;">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary fw-semibold" style="font-size: 14px;">Kata Sandi Baru</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-key"></i></span>
                    <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100" name="reset_password">
                Simpan Password Baru <i class="fa fa-check-circle ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="back-link"><i class="fa fa-arrow-left me-1"></i> Kembali ke Login</a>
        </div>
    </div>
</div>

</body>
</html>
