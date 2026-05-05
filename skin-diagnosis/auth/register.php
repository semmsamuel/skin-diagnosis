<?php
include '../config/koneksi.php';

if(isset($_POST['daftar'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek_email) > 0){
        $error = "Email sudah terdaftar! Silakan gunakan email lain.";
    } else {
        mysqli_query($conn, "INSERT INTO users VALUES(NULL,'$nama','$email','$password')");
        header("Location: login.php?msg=registered");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Skin AI</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        /* Latar belakang medis yang hidup dengan overlay gradient hijau/teal */
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.85) 0%, rgba(16, 185, 129, 0.85) 100%), 
                    url('https://images.unsplash.com/photo-1551076805-e18690c5e531?q=80&w=2000&auto=format&fit=crop') no-repeat center center;
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

    .register-container {
        display: flex;
        flex-direction: row-reverse; /* Balik posisi gambar dan form untuk variasi */
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        min-height: 550px;
        animation: fadeIn 0.8s ease-out;
    }

    .register-image {
        flex: 1;
        background: url('https://images.unsplash.com/photo-1581056771107-24ca5f033842?q=80&w=1000&auto=format&fit=crop') no-repeat center center;
        background-size: cover;
        position: relative;
        display: none;
    }

    @media (min-width: 768px) {
        .register-image {
            display: block;
        }
    }

    .register-image-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to bottom, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.9));
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 40px;
        color: white;
    }

    .register-form-wrap {
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
        color: #10b981;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-brand i {
        background: #d1fae5;
        color: #10b981;
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
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
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

    .btn-register {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 16px;
        color: white;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        margin-top: 10px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }

    .login-link {
        color: #10b981;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }

    .login-link:hover {
        color: #059669;
        text-decoration: underline;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>

<div class="register-container">
    <div class="register-image">
        <div class="register-image-overlay">
            <h3 class="fw-bold mb-2">Layanan Kesehatan AI</h3>
            <p class="mb-0 opacity-75">Bergabunglah dan manfaatkan teknologi AI untuk analisis kondisi kulit yang cepat dan akurat.</p>
        </div>
    </div>
    
    <div class="register-form-wrap">
        <div class="mb-4">
            <div class="logo-brand"><i class="fa fa-notes-medical"></i> Skin AI</div>
            <div class="welcome-text">Buat akun baru Anda dan mulai diagnosis cerdas hari ini.</div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger" style="border-radius: 12px; font-size: 14px;">
                <i class="fa fa-circle-exclamation me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold" style="font-size: 14px;">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap Anda" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold" style="font-size: 14px;">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary fw-semibold" style="font-size: 14px;">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Buat kata sandi baru" required>
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100" name="daftar">
                <i class="fa fa-user-plus me-2"></i> Daftar Akun
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted" style="font-size: 14px;">Sudah memiliki akun? <a href="login.php" class="login-link">Masuk di sini</a></p>
        </div>
    </div>
</div>

</body>
</html>