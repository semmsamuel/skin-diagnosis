<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$query = mysqli_query($conn, "SELECT * FROM diagnosa WHERE user_id='$user_id' ORDER BY tanggal DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Diagnosa - Skin AI</title>
    <!-- Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        /* Latar Belakang Medis Cerah */
        .bg-image {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -2;
            background-image: url('https://images.unsplash.com/photo-1576091160399-11cbbe12ce75?q=80&w=2000&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }

        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            background: linear-gradient(135deg, rgba(224, 242, 254, 0.95) 0%, rgba(204, 251, 241, 0.95) 100%);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .navbar-brand { color: #0ea5e9 !important; font-weight: 800; }
        .table-card { border-radius: 20px; border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); padding: 25px; }
        .badge-persen { font-size: 0.9rem; padding: 8px 12px; background: linear-gradient(135deg, #14b8a6, #0d9488) !important; border-radius: 10px;}
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; vertical-align: middle; background-color: transparent !important;}
        thead.table-light th { background-color: rgba(248, 250, 252, 0.6) !important; border-bottom: 2px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="bg-image"></div>
    <div class="bg-overlay"></div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
                <i class="fa fa-stethoscope"></i> Skin AI
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-secondary fw-semibold" href="dashboard.php">Dashboard</a>
                <a class="nav-link text-primary fw-bold" href="riwayat.php">Riwayat</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fa fa-history text-primary me-2"></i> Riwayat Medis</h3>
            <a href="diagnosa.php" class="btn btn-primary" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border: none; border-radius: 12px; font-weight: 600;"><i class="fa fa-plus me-1"></i> Diagnosa Baru</a>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hasil Diagnosa</th>
                            <th>Tingkat Keyakinan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($d = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d M Y, H:i', strtotime($d['tanggal'])) ?></td>
                            <td><strong class="text-primary"><?= $d['hasil'] ?></strong></td>
                            <td>
                                <span class="badge bg-success badge-persen"><?= round($d['persentase'], 2) ?>%</span>
                            </td>
                            <td>
                                <a href="detail_riwayat.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill fw-semibold px-3">
                                    <i class="fa fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat diagnosa.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
