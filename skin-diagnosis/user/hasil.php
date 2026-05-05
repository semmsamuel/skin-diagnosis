<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_POST['gejala']) || empty($_POST['gejala'])) {
    header("Location: diagnosa.php");
    exit;
}

$gejala_input = $_POST['gejala'];
$gejala_user = [];
$gejala_severities = [];

foreach ($gejala_input as $id => $sev) {
    if ($sev > 0) {
        $gejala_user[] = $id;
        $gejala_severities[$id] = $sev;
    }
}

if (empty($gejala_user)) {
    echo "<script>alert('Silakan pilih minimal satu gejala yang dialami.'); window.location='diagnosa.php';</script>";
    exit;
}

$user_id = $_SESSION['user']['id'];

// =======================================================
// IMPLEMENTASI METODE CASE BASED REASONING (CBR)
// =======================================================
// Similarity(T, S) = ( Σ (W_i * sim(T_i, S_i)) ) / Σ W_i
// T = Kasus Baru (Input User)
// S = Kasus Lama (Basis Pengetahuan / Penyakit)
// W = Bobot Gejala
// sim = Nilai kemiripan gejala (Severity)
// =======================================================

$query_all_relasi = mysqli_query($conn, "
    SELECT r.penyakit_id, r.gejala_id, r.bobot, p.nama_penyakit, p.solusi 
    FROM relasi r
    JOIN penyakit p ON r.penyakit_id = p.id
");

$data_kasus_basis = [];
while ($row = mysqli_fetch_assoc($query_all_relasi)) {
    $pid = $row['penyakit_id'];
    if (!isset($data_kasus_basis[$pid])) {
        $data_kasus_basis[$pid] = [
            'nama' => $row['nama_penyakit'],
            'solusi' => $row['solusi'],
            'total_bobot_kasus' => 0,
            'bobot_kemiripan' => 0
        ];
    }
    
    // Σ W_i (Total bobot kasus pada basis pengetahuan)
    $data_kasus_basis[$pid]['total_bobot_kasus'] += $row['bobot'];
    
    // Cek apakah gejala (fitur) ini ada pada kasus baru (input user)
    if (in_array($row['gejala_id'], $gejala_user)) {
        $severity = $gejala_severities[$row['gejala_id']];
        // sim(T_i, S_i) = severity / 5
        $sim_value = $severity / 5;
        
        // W_i * sim(T_i, S_i)
        $data_kasus_basis[$pid]['bobot_kemiripan'] += $row['bobot'] * $sim_value;
    }
}

$hasil_diagnosa = [];
foreach ($data_kasus_basis as $pid => $data) {
    if ($data['total_bobot_kasus'] > 0) {
        // Menghitung persentase nilai similarity CBR
        $similarity_score = ($data['bobot_kemiripan'] / $data['total_bobot_kasus']) * 100;
        
        // Thresholding minimal kemiripan agar masuk hasil (opsional, misalnya similarity > 0)
        if ($similarity_score > 0) {
            $hasil_diagnosa[] = [
                'nama' => $data['nama'],
                'persen' => $similarity_score, // Nilai CBR similarity dalam persen
                'solusi' => $data['solusi']
            ];
        }
    }
}

// Urutkan hasil tertinggi ke terendah
usort($hasil_diagnosa, function($a, $b) {
    return $b['persen'] <=> $a['persen'];
});

// Simpan ke riwayat
$email_sent = false;
$user_email = $_SESSION['user']['email'] ?? '';

if (!empty($hasil_diagnosa)) {
    $nama_hasil = mysqli_real_escape_string($conn, $hasil_diagnosa[0]['nama']);
    $persen_hasil = $hasil_diagnosa[0]['persen'];
    $solusi_hasil = $hasil_diagnosa[0]['solusi'];
    mysqli_query($conn, "INSERT INTO diagnosa (user_id, hasil, persentase, tanggal) VALUES ('$user_id', '$nama_hasil', '$persen_hasil', NOW())");

    // --- FITUR KIRIM EMAIL KE USER ---
    if (!empty($user_email)) {
        $user_name = $_SESSION['user']['nama'] ?? 'Pengguna';
        $subject = "Hasil Analisis Skin AI - " . $nama_hasil;
        
        $message = "
        <html>
        <head>
          <title>Hasil Analisis Skin AI</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; background: #f8fafc; padding: 20px;'>
          <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
              <div style='text-align: center; margin-bottom: 30px;'>
                  <h2 style='color: #2563eb; margin-bottom: 5px;'>Laporan Analisis Kulit AI (Metode CBR)</h2>
                  <p style='color: #64748b; margin-top: 0;'>Dihasilkan secara otomatis oleh sistem</p>
              </div>
              
              <p>Halo <strong>{$user_name}</strong>,</p>
              <p>Berikut adalah hasil analisis kulit berdasarkan gejala klinis yang Anda masukkan pada aplikasi Skin AI:</p>
              
              <div style='background: #eff6ff; border: 1px solid #bfdbfe; padding: 20px; border-radius: 12px; margin: 25px 0;'>
                  <h3 style='margin-top: 0; color: #1e3a8a; font-size: 1.2rem;'>Diagnosis Utama: {$nama_hasil}</h3>
                  <p style='margin-bottom: 0; font-size: 1.1rem;'><strong>Probabilitas Kecocokan:</strong> <span style='color: #16a34a; font-weight: bold;'>" . round($persen_hasil, 1) . "%</span></p>
              </div>
              
              <h4 style='color: #0f172a; margin-bottom: 10px;'>Rekomendasi Medis & Solusi:</h4>
              <div style='background: #fefce8; border-left: 4px solid #fbbf24; padding: 15px; border-radius: 0 8px 8px 0; color: #475569;'>
                  " . nl2br($solusi_hasil) . "
              </div>
              
              <hr style='border: 0; border-top: 1px dashed #cbd5e1; margin: 30px 0;'>
              
              <p style='font-size: 0.85em; color: #94a3b8; text-align: justify;'>
                <strong>Disclaimer Medis:</strong> Hasil analisis ini di-generate oleh Kecerdasan Buatan (AI) berdasarkan input gejala dan hanya untuk tujuan informasi. Ini <strong>bukan</strong> merupakan diagnosis medis definitif. Selalu konsultasikan dengan Dokter Spesialis Kulit (Dermatolog) untuk diagnosis dan pengobatan yang akurat.
              </p>
          </div>
        </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Skin AI <noreply@skinai.local>" . "\r\n";

        // Menggunakan @ untuk mem-bypass error jika SMTP lokal (Laragon) belum berjalan sempurna
        if(@mail($user_email, $subject, $message, $headers)){
            $email_sent = true;
        }
    }
}

// Persiapkan data untuk Chart.js
$chart_labels = [];
$chart_data = [];
$chart_colors = ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'];

for ($i = 0; $i < min(5, count($hasil_diagnosa)); $i++) {
    $chart_labels[] = $hasil_diagnosa[$i]['nama'];
    $chart_data[] = round($hasil_diagnosa[$i]['persen'], 1);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis - Skin AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Chart.js & HTML2PDF -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .result-highlight {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 30px;
        }
        
        .chart-container {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        .action-btn {
            border-radius: 30px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-export {
            background: white;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
        .btn-export:hover {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #f87171;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
        }
        
        .solution-card {
            border-left: 5px solid #fbbf24;
            background: #fefce8;
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
            <i class="fa fa-stethoscope text-primary"></i> Skin AI
        </a>
        <div class="navbar-nav ms-auto">
            <button class="btn action-btn btn-export" onclick="exportPDF()">
                <i class="fa fa-file-pdf me-2"></i> Cetak / Export PDF
            </button>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Area yang akan di-export ke PDF -->
            <div id="pdf-content">
                <div class="report-header text-center">
                    <i class="fa fa-notes-medical fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-1">Laporan Analisis Kulit AI</h2>
                    <p class="mb-0 opacity-75">Metode Case Based Reasoning (CBR)</p>
                    <div class="mt-3 text-white-50" style="font-size: 0.9rem;">
                        Tanggal: <?= date('d M Y, H:i') ?> | ID Pasien: #USER-<?= $_SESSION['user']['id'] ?>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <?php if (isset($email_sent) && $email_sent): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;" data-html2canvas-ignore="true">
                            <i class="fa-solid fa-envelope-circle-check fs-4 me-3"></i>
                            <div>
                                <strong>Berhasil Terkirim!</strong> Salinan hasil diagnosis ini telah dikirimkan ke email Anda (<strong><?= htmlspecialchars($user_email) ?></strong>).
                            </div>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($hasil_diagnosa)): ?>
                        
                        <div class="row align-items-center mb-5">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="result-highlight text-center h-100 d-flex flex-column justify-content-center">
                                    <h6 class="text-uppercase fw-bold text-primary mb-2" style="letter-spacing: 1px;">Diagnosis Utama</h6>
                                    <h2 class="display-5 fw-bold text-dark mb-3"><?= $hasil_diagnosa[0]['nama'] ?></h2>
                                    
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <div class="progress flex-grow-1" style="height: 14px; border-radius: 10px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                                 style="width: <?= $hasil_diagnosa[0]['persen'] ?>%"></div>
                                        </div>
                                        <span class="h3 fw-bold text-success mb-0"><?= round($hasil_diagnosa[0]['persen'], 1) ?>%</span>
                                    </div>
                                    <p class="text-muted mt-3 mb-0" style="font-size:0.85rem;">*Nilai Similarity (kemiripan kasus) menggunakan metode CBR.</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="diagnosaChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="solution-card mb-5">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px;">
                                    <i class="fa fa-lightbulb"></i>
                                </div>
                                Rekomendasi Medis & Solusi
                            </h5>
                            <p class="mb-0 text-secondary lh-lg fs-6"><?= nl2br($hasil_diagnosa[0]['solusi']) ?></p>
                        </div>

                        <?php if (count($hasil_diagnosa) > 1): ?>
                            <h5 class="fw-bold text-dark mb-3"><i class="fa fa-list-ul text-primary me-2"></i> Kemungkinan Diagnosis Lainnya</h5>
                            <div class="table-responsive border rounded-3">
                                <table class="table table-hover table-borderless mb-0 align-middle">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="py-3 px-4">Penyakit / Kondisi</th>
                                            <th class="py-3 px-4 text-end">Probabilitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for($i=1; $i<min(4, count($hasil_diagnosa)); $i++): ?>
                                            <tr class="border-top">
                                                <td class="py-3 px-4 fw-semibold text-dark"><?= $hasil_diagnosa[$i]['nama'] ?></td>
                                                <td class="py-3 px-4 text-end">
                                                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill fs-6">
                                                        <?= round($hasil_diagnosa[$i]['persen'], 1) ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Medical Disclaimer -->
                        <div class="mt-5 p-3 bg-light border-start border-4 border-danger rounded text-muted" style="font-size: 0.85rem;">
                            <strong>Disclaimer Medis:</strong> Hasil analisis ini di-generate oleh Kecerdasan Buatan (AI) berdasarkan input gejala dan hanya untuk tujuan informasi. Ini bukan merupakan diagnosis medis definitif. Selalu konsultasikan dengan Dokter Spesialis Kulit (Dermatolog) untuk diagnosis dan pengobatan yang akurat.
                        </div>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4">
                                <i class="fa fa-triangle-exclamation fa-4x text-warning"></i>
                            </div>
                            <h3 class="fw-bold text-dark">Data Gejala Tidak Mencukupi</h3>
                            <p class="text-muted fs-5">AI tidak dapat menemukan pola penyakit yang cocok dengan tingkat keparahan gejala yang Anda masukkan. Silakan ulangi asesmen dengan lebih detail.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-flex justify-content-center gap-3" data-html2canvas-ignore="true">
                <a href="diagnosa.php" class="btn action-btn btn-outline-primary px-4">
                    <i class="fa fa-redo me-2"></i> Diagnosa Ulang
                </a>
                <a href="dashboard.php" class="btn action-btn btn-primary shadow px-4">
                    <i class="fa fa-home me-2"></i> Ke Dashboard
                </a>
            </div>

        </div>
    </div>
</div>

<script>
// Render Chart.js
<?php if (!empty($hasil_diagnosa)): ?>
const ctx = document.getElementById('diagnosaChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            data: <?= json_encode($chart_data) ?>,
            backgroundColor: <?= json_encode($chart_colors) ?>,
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' ' + context.label + ': ' + context.raw + '%';
                    }
                }
            }
        },
        cutout: '65%'
    }
});
<?php endif; ?>

// PDF Export Function
function exportPDF() {
    const element = document.getElementById('pdf-content');
    const opt = {
        margin:       0.5,
        filename:     'Hasil_Diagnosa_SkinAI.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    // Prevent button spam
    const btn = document.querySelector('.btn-export');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Memproses PDF...';
    btn.disabled = true;

    html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

</body>
</html>