<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_riwayat = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user']['id'];

// Ambil data riwayat
$query = mysqli_query($conn, "SELECT * FROM diagnosa WHERE id='$id_riwayat' AND user_id='$user_id'");
$riwayat = mysqli_fetch_assoc($query);

if (!$riwayat) {
    echo "<script>alert('Data riwayat tidak ditemukan atau Anda tidak memiliki akses.'); window.location='riwayat.php';</script>";
    exit;
}

// Ambil detail penyakit berdasarkan nama_penyakit
$nama_penyakit = mysqli_real_escape_string($conn, $riwayat['hasil']);
$query_penyakit = mysqli_query($conn, "SELECT * FROM penyakit WHERE nama_penyakit='$nama_penyakit'");
$penyakit = mysqli_fetch_assoc($query_penyakit);

$solusi = $penyakit['solusi'] ?? 'Silakan berkonsultasi langsung dengan dokter spesialis untuk rekomendasi medis lebih lanjut.';
$persentase = $riwayat['persentase'];
$tanggal = date('d M Y, H:i', strtotime($riwayat['tanggal']));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Riwayat Analisis - Skin AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- HTML2PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .navbar-brand { font-weight: 800; color: #0ea5e9 !important; font-size: 1.4rem; }
        
        #pdf-content {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 30px;
        }
        
        .report-header {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .report-header::after {
            content: ''; position: absolute; top: -50%; right: -10%;
            width: 300px; height: 300px; background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .result-highlight {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 30px;
        }

        .action-btn {
            border-radius: 30px; padding: 12px 24px; font-weight: 600; transition: all 0.3s ease;
        }
        .btn-export {
            background: white; color: #dc2626; border: 1px solid #fca5a5;
        }
        .btn-export:hover {
            background: #fef2f2; color: #b91c1c; border-color: #f87171;
            transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
        }
        
        .solution-card {
            border-left: 5px solid #14b8a6;
            background: #f0fdfa;
            border-radius: 0 16px 16px 0;
            padding: 24px;
        }
    </style>
</head>
<body>

<div class="bg-image" data-html2canvas-ignore="true"></div>
<div class="bg-overlay" data-html2canvas-ignore="true"></div>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm" data-html2canvas-ignore="true">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <i class="fa fa-stethoscope"></i> Skin AI
        </a>
        <div class="navbar-nav ms-auto gap-2">
            <a href="riwayat.php" class="btn action-btn btn-outline-secondary px-3" style="padding: 8px 16px;">
                <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn action-btn btn-export" onclick="exportPDF()" style="padding: 8px 16px;">
                <i class="fa fa-file-pdf me-1"></i> Cetak PDF
            </button>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Area yang akan di-export ke PDF -->
            <div id="pdf-content">
                <div class="report-header text-center">
                    <i class="fa fa-notes-medical fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-1">Arsip Riwayat Analisis Kulit</h2>
                    <p class="mb-0 opacity-75">Metode Case Based Reasoning (CBR)</p>
                    <div class="mt-3 text-white-50" style="font-size: 0.9rem;">
                        Tanggal Analisis: <?= $tanggal ?> | ID Pasien: #USER-<?= $user_id ?>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <div class="result-highlight text-center mb-5">
                        <h6 class="text-uppercase fw-bold text-success mb-2" style="letter-spacing: 1px;">Diagnosis Utama</h6>
                        <h2 class="display-5 fw-bold text-dark mb-3"><?= htmlspecialchars($nama_penyakit) ?></h2>
                        
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <div class="progress flex-grow-1" style="height: 14px; border-radius: 10px; max-width: 300px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                     style="width: <?= $persentase ?>%"></div>
                            </div>
                            <span class="h3 fw-bold text-success mb-0"><?= round($persentase, 1) ?>%</span>
                        </div>
                        <p class="text-muted mt-3 mb-0" style="font-size:0.85rem;">*Nilai Similarity (kemiripan kasus) saat analisis dilakukan.</p>
                    </div>

                    <div class="solution-card mb-5">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                            <div class="bg-teal text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px; background-color: #14b8a6;">
                                <i class="fa fa-lightbulb"></i>
                            </div>
                            Rekomendasi Medis & Solusi
                        </h5>
                        <p class="mb-0 text-secondary lh-lg fs-6"><?= nl2br(htmlspecialchars($solusi)) ?></p>
                    </div>

                    <!-- Medical Disclaimer -->
                    <div class="mt-5 p-3 bg-light border-start border-4 border-danger rounded text-muted" style="font-size: 0.85rem;">
                        <strong>Disclaimer Medis:</strong> Hasil analisis ini di-generate oleh Kecerdasan Buatan (AI) berdasarkan input gejala dan hanya untuk tujuan informasi. Ini bukan merupakan diagnosis medis definitif. Selalu konsultasikan dengan Dokter Spesialis Kulit (Dermatolog) untuk diagnosis dan pengobatan yang akurat.
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
// PDF Export Function
function exportPDF() {
    const element = document.getElementById('pdf-content');
    const opt = {
        margin:       0.5,
        filename:     'Riwayat_Analisis_SkinAI.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    // Prevent button spam
    const btn = document.querySelector('.btn-export');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Proses PDF...';
    btn.disabled = true;

    html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

</body>
</html>
