<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil data terbaru dari database
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

// Update session untuk jaga-jaga
$_SESSION['user'] = $user;

if(isset($_POST['simpan'])){
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    $update = mysqli_query($conn, "UPDATE users SET jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', no_hp='$no_hp', alamat='$alamat' WHERE id='$user_id'");

    if($update){
        $_SESSION['user']['jenis_kelamin'] = $jenis_kelamin;
        $_SESSION['user']['tanggal_lahir'] = $tanggal_lahir;
        $_SESSION['user']['no_hp'] = $no_hp;
        $_SESSION['user']['alamat'] = $alamat;

        $success = "Data diri berhasil disimpan! Sekarang Anda dapat memulai analisis.";
        // Jika asalnya dari attempt diagnosa, kita bisa kasih tau
    } else {
        $error = "Gagal menyimpan data diri.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Data Diri - Skin AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { 
            background: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #0f172a;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .navbar-brand { font-weight: 800; color: #2563eb !important; font-size: 1.4rem; }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <i class="fa fa-stethoscope text-primary"></i> Skin AI
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <?php if(isset($_GET['req']) && $_GET['req'] == 'diagnosa'): ?>
                <div class="alert alert-warning mb-4" style="border-radius: 12px;">
                    <i class="fa fa-circle-exclamation me-2"></i> Anda wajib melengkapi data diri sebelum melakukan Asesmen Klinis / Diagnosa.
                </div>
            <?php endif; ?>

            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa fa-user-edit fa-2x"></i>
                    </div>
                    <h3 class="fw-bold">Data Diri Pasien</h3>
                    <p class="text-muted">Lengkapi profil Anda untuk akurasi rekam medis</p>
                </div>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success" style="border-radius: 12px;">
                        <i class="fa fa-check-circle me-2"></i> <?= $success ?>
                        <div class="mt-2">
                            <a href="diagnosa.php" class="btn btn-sm btn-success rounded-pill fw-bold px-3">Mulai Diagnosa <i class="fa fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger" style="border-radius: 12px;">
                        <i class="fa fa-circle-xmark me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" disabled readonly style="background-color: #f1f5f9;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled readonly style="background-color: #f1f5f9;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" <?= ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat Domisili</label>
                        <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn btn-primary w-100">
                        <i class="fa fa-save me-2"></i> Simpan Data Diri
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
