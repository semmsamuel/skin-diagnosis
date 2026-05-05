<?php 
session_start();
include '../config/koneksi.php'; 

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$cek_profil = mysqli_query($conn, "SELECT jenis_kelamin, tanggal_lahir, no_hp, alamat FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($cek_profil);

// Jika ada field data diri yang masih kosong
if(empty($user_data['jenis_kelamin']) || empty($user_data['tanggal_lahir']) || empty($user_data['no_hp']) || empty($user_data['alamat'])) {
    header("Location: profil.php?req=diagnosa");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosa Gejala - Skin AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #0f172a;
            padding-bottom: 120px;
            position: relative;
            z-index: 1;
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
        .nav-link { color: #475569 !important; font-weight: 600; }
        .nav-link:hover { color: #0ea5e9 !important; }
        
        .header-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 40px;
            border: 1px solid #bfdbfe;
        }

        .symptom-card { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            padding: 24px;
            height: 100%;
        }
        .symptom-card:hover { 
            box-shadow: 0 12px 20px -8px rgba(37, 99, 235, 0.15); 
            border-color: #bfdbfe;
        }
        .symptom-card.active {
            border-color: #3b82f6;
            background: #f8fafc;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .form-range { height: 6px; }
        .form-range::-webkit-slider-thumb {
            background: #2563eb;
            width: 20px; height: 20px;
            margin-top: -7px;
            box-shadow: 0 2px 6px rgba(37,99,235,0.4);
        }
        .form-range::-webkit-slider-runnable-track {
            background: #e2e8f0;
            border-radius: 10px;
            height: 6px;
        }

        .severity-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #64748b;
            transition: all 0.3s ease;
        }
        .severity-badge.active-1 { background: #dcfce7; color: #166534; }
        .severity-badge.active-2 { background: #fef9c3; color: #854d0e; }
        .severity-badge.active-3 { background: #ffedd5; color: #9a3412; }
        .severity-badge.active-4 { background: #fee2e2; color: #991b1b; }
        .severity-badge.active-5 { background: #fecaca; color: #7f1d1d; }

        .bottom-action-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            padding: 20px 0;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -10px 25px -5px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .btn-diagnose {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 30px;
            padding: 14px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.5);
            transition: all 0.3s ease;
        }
        .btn-diagnose:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px rgba(37, 99, 235, 0.6);
            color: white;
        }

        #loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(8px);
        }
        .loader-pulse {
            width: 80px; height: 80px;
            background: #2563eb;
            border-radius: 50%;
            animation: pulse 1.5s infinite ease-in-out;
        }
        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }
    </style>
</head>
<body>

<div class="bg-image"></div>
<div class="bg-overlay"></div>

<div id="loading-overlay">
    <div class="loader-pulse mb-4"></div>
    <h3 class="fw-bold text-primary">Menganalisis Kondisi...</h3>
    <p class="text-muted fw-medium">AI Sedang memproses data klinis Anda</p>
</div>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <i class="fa fa-stethoscope text-primary"></i> Skin AI
        </a>
        <div class="navbar-nav ms-auto flex-row gap-3 align-items-center">
            <a class="nav-link d-none d-md-block" href="dashboard.php">Dashboard</a>
            <a class="nav-link d-none d-md-block" href="riwayat.php">Riwayat</a>
            <a class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-4" style="background:#fee2e2;" href="../auth/logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="header-section text-center shadow-sm" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.9);">
        <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3 shadow-sm border"><i class="fa fa-info-circle me-1"></i> Asesmen Klinis</span>
        <h2 class="fw-bold mb-3">Tentukan Tingkat Keparahan Gejala</h2>
        <p class="text-muted mx-auto" style="max-width:600px;">Geser slider pada gejala yang Anda alami. <b>0</b> berarti tidak mengalami, dan <b>5</b> berarti sangat parah/mengganggu.</p>
    </div>

    <form action="hasil.php" method="POST" id="diagForm">
        <div class="row g-4">
            <?php
            $g = mysqli_query($conn, "SELECT * FROM gejala ORDER BY nama_gejala ASC");
            while($d = mysqli_fetch_assoc($g)){
                echo "
                <div class='col-md-6 col-lg-4'>
                    <div class='symptom-card' id='card_{$d['id']}'>
                        <div class='d-flex justify-content-between align-items-start mb-3'>
                            <h6 class='fw-bold mb-0 text-dark'>{$d['nama_gejala']}</h6>
                            <span class='severity-badge' id='badge_{$d['id']}'>Tidak Ada (0)</span>
                        </div>
                        <div class='mt-4'>
                            <input type='range' class='form-range symptom-slider' 
                                   name='gejala[{$d['id']}]' 
                                   id='slider_{$d['id']}' 
                                   min='0' max='5' value='0'
                                   oninput='updateSeverity({$d['id']}, this.value)'>
                            <div class='d-flex justify-content-between text-muted mt-2' style='font-size:0.75rem; font-weight:600;'>
                                <span>0</span>
                                <span>1</span>
                                <span>2</span>
                                <span>3</span>
                                <span>4</span>
                                <span>5</span>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
        </div>

        <div class="bottom-action-bar">
            <div class="container text-center">
                <button type="submit" class="btn btn-diagnose">
                    <i class="fa fa-microchip me-2"></i> Proses Diagnosa AI
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const severityLabels = [
    "Tidak Ada (0)",
    "Sangat Ringan (1)",
    "Ringan (2)",
    "Sedang (3)",
    "Parah (4)",
    "Sangat Parah (5)"
];

function updateSeverity(id, value) {
    const card = document.getElementById('card_' + id);
    const badge = document.getElementById('badge_' + id);
    
    badge.innerText = severityLabels[value];
    
    // Remove previous active classes
    badge.className = 'severity-badge';
    
    if (value > 0) {
        card.classList.add('active');
        badge.classList.add('active-' + value);
    } else {
        card.classList.remove('active');
    }
}

document.getElementById("diagForm").addEventListener("submit", function(e){
    const sliders = document.querySelectorAll('.symptom-slider');
    let hasSymptom = false;
    
    sliders.forEach(slider => {
        if(slider.value > 0) hasSymptom = true;
    });

    if(!hasSymptom){
        e.preventDefault();
        alert("Silakan tentukan tingkat keparahan minimal pada satu gejala (geser slider > 0)!");
        return;
    }

    document.getElementById('loading-overlay').style.display = 'flex';
});
</script>

</body>
</html>