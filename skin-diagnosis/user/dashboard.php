<?php 
session_start(); 

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Skin AI</title>
    
    <!-- Font Premium: Plus Jakarta Sans (Selaras dengan Login/Register) -->
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
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh; z-index: -2;
            background-image: url('https://images.unsplash.com/photo-1576091160399-11cbbe12ce75?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            /* Gradient Sky to Teal cerah untuk memastikan teks mudah dibaca */
            background: linear-gradient(135deg, rgba(224, 242, 254, 0.95) 0%, rgba(204, 251, 241, 0.95) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Navbar Custom */
        .navbar {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05) !important;
            padding: 15px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .navbar-brand { color: #0ea5e9 !important; font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; }
        .nav-link.text-dark { color: #475569 !important; font-weight: 600; }
        .nav-link.text-primary { color: #0ea5e9 !important; transition: 0.3s; }
        .nav-link.text-primary:hover { color: #0284c7 !important; transform: translateY(-1px); }
        
        .btn-danger-outline {
            background: #fff0f2; color: #e11d48; border: 1px solid #ffe4e6;
            font-weight: 600; border-radius: 12px; padding: 8px 20px; transition: 0.3s;
        }
        .btn-danger-outline:hover { background: #e11d48; color: #ffffff; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2); }

        .header-title { font-weight: 800; color: #0f172a; letter-spacing: -1px; margin-bottom: 15px; }
        .header-subtitle { color: #475569; font-size: 1.1rem; font-weight: 500; }

        /* Card Menu Utama */
        .card-menu { 
            background: #ffffff;
            border-radius: 24px; 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            padding: 40px 30px;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
            position: relative; overflow: hidden;   
        }

        .card-menu::before {
            content: ""; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%;
            background: radial-gradient(circle, rgba(14,165,233,0.05) 0%, rgba(255,255,255,0) 70%);
            transition: all 0.5s ease; z-index: 0;
        }
        .card-menu:hover::before { transform: scale(1.5); }
        .card-menu > div, .card-menu > a { position: relative; z-index: 1; }
        .card-menu:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 25px 50px -12px rgba(14, 165, 233, 0.15); 
            border-color: #bae6fd;
        }

        .icon-box {
            width: 85px; height: 85px; border-radius: 24px; 
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 25px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        /* Penyesuaian warna ikon agar lebih segar */
        .icon-box-primary { background-color: #e0f2fe; color: #0ea5e9; box-shadow: 0 8px 20px rgba(14,165,233,0.15); }
        .icon-box-success { background-color: #ccfbf1; color: #14b8a6; box-shadow: 0 8px 20px rgba(20,184,166,0.15); }
        .icon-box-danger { background-color: #ffe4e6; color: #f43f5e; box-shadow: 0 8px 20px rgba(244,63,94,0.15); }
        .card-menu:hover .icon-box { transform: scale(1.1) rotate(8deg); }

        .card-title-custom { font-weight: 800; font-size: 1.3rem; color: #0f172a; margin-bottom: 12px; }
        .card-text-custom { color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 30px; font-weight: 500;}

        /* Button Action dengan Nuansa Medis (Gradient) */
        .btn-action {
            border-radius: 14px; font-weight: 700; padding: 14px 20px; letter-spacing: 0.5px;
            border: none; transition: all 0.3s ease; text-decoration: none; display: flex;
            align-items: center; justify-content: center; gap: 8px; color: white;
        }
        .btn-primary-action { background: linear-gradient(135deg, #0ea5e9, #0284c7); box-shadow: 0 6px 20px rgba(14, 165, 233, 0.3); }
        .btn-primary-action:hover { background: linear-gradient(135deg, #0284c7, #0369a1); color: white; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4); }
        
        .btn-success-action { background: linear-gradient(135deg, #14b8a6, #0d9488); box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3); }
        .btn-success-action:hover { background: linear-gradient(135deg, #0d9488, #0f766e); color: white; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(20, 184, 166, 0.4); }
        
        .btn-danger-action { background: linear-gradient(135deg, #f43f5e, #e11d48); box-shadow: 0 6px 20px rgba(244, 63, 94, 0.3); }
        .btn-danger-action:hover { background: linear-gradient(135deg, #e11d48, #be123c); color: white; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(244, 63, 94, 0.4); }

        /* News Section */
        .section-title { font-weight: 800; color: #0f172a; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .card-news {
            background: #ffffff;
            border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); transition: 0.3s ease; height: 100%;
            display: flex; flex-direction: column; cursor: pointer;
        }
        .card-news:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(14, 165, 233, 0.1); border-color: #bae6fd; }
        .card-news img { width: 100%; height: 180px; object-fit: cover; transition: 0.5s ease; }
        .card-news:hover img { transform: scale(1.05); } 
        .card-news-img-wrapper { overflow: hidden; }
        
        .card-news-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .news-date { font-size: 0.8rem; color: #0ea5e9; font-weight: 700; margin-bottom: 8px; }
        .news-title { font-weight: 800; font-size: 1.1rem; color: #0f172a; margin-bottom: 10px; line-height: 1.4; }
        .news-excerpt { font-size: 0.9rem; color: #475569; margin-bottom: 0; line-height: 1.6; flex-grow: 1; font-weight: 500; }
        .badge-category {
            position: absolute; top: 15px; left: 15px; background: rgba(255, 255, 255, 0.95);
            color: #14b8a6; padding: 6px 14px; border-radius: 12px; font-size: 0.75rem;
            font-weight: 800; backdrop-filter: blur(4px); z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Modal Styling */
        .modal-content { border-radius: 24px; border: none; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif;}
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 25px; }
        .modal-title-badge { background: #e0f2fe; color: #0ea5e9; padding: 6px 14px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; }
        .modal-body { padding: 30px; }
        .modal-body img { width: 100%; border-radius: 16px; margin-bottom: 25px; max-height: 350px; object-fit: cover; }
        .modal-news-title { font-weight: 800; color: #0f172a; margin-bottom: 15px; font-size: 1.5rem; line-height: 1.4; }
        .modal-news-text { color: #475569; line-height: 1.8; font-size: 1rem; font-weight: 500;}
    </style>
</head>
<body>

<!-- DOUBLE LAYER UNTUK BACKGROUND FOTO PREMIUM -->
<div class="bg-image"></div>
<div class="bg-overlay"></div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <i class="fa fa-stethoscope"></i> Skin AI
        </a>
        <div class="navbar-nav ms-auto align-items-center">
            <span class="nav-link text-dark me-3">Halo, <?= isset($_SESSION['user']['nama']) ? htmlspecialchars($_SESSION['user']['nama']) : 'Guest'; ?></span>
            <a class="nav-link text-primary fw-bold me-3" href="profil.php"><i class="fa fa-user-edit me-1"></i> Profil</a>
            <a class="btn-danger-outline text-decoration-none" href="../auth/logout.php">Keluar <i class="fa fa-arrow-right-from-bracket ms-1"></i></a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-3 pb-5">
    <div class="text-center mb-5 pb-2">
        <h2 class="header-title">Sistem Diagnosa Kulit AI Terpadu</h2>
        <p class="header-subtitle">Analisis gejala, pantau rekam medis, dan dapatkan edukasi kesehatan kulit terbaik.</p>
    </div>

    <!-- MAIN CARDS -->
    <div class="row g-4 justify-content-center align-items-stretch mb-5 pb-4">
        
        <div class="col-md-4">
            <div class="card-menu text-center">
                <div>
                    <div class="icon-box icon-box-primary">
                        <i class="fa fa-microchip fa-2x"></i>
                    </div>
                    <h5 class="card-title-custom">Mulai Diagnosa</h5>
                    <p class="card-text-custom">Analisis gejala kulit Anda dengan bantuan algoritma AI (CBR) secara *real-time*.</p>
                </div>
                <a href="diagnosa.php" class="btn-action btn-primary-action w-100">
                    Mulai Analisis <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-menu text-center">
                <div>
                    <div class="icon-box icon-box-success">
                        <i class="fa fa-file-medical fa-2x"></i>
                    </div>
                    <h5 class="card-title-custom">Riwayat Medis</h5>
                    <p class="card-text-custom">Lihat kembali hasil rekam medis dan pantau histori perkembangan kulit Anda.</p>
                </div>
                <a href="riwayat.php" class="btn-action btn-success-action w-100">
                    Lihat Riwayat <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-menu text-center">
                <div>
                    <div class="icon-box icon-box-danger">
                        <i class="fa fa-power-off fa-2x"></i>
                    </div>
                    <h5 class="card-title-custom">Keluar Sistem</h5>
                    <p class="card-text-custom">Akhiri sesi Anda dan keluar dari sistem aplikasi secara aman untuk menjaga privasi.</p>
                </div>
                <a href="../auth/logout.php" class="btn-action btn-danger-action w-100">
                    Keluar Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- SECTION BERITA EDUKASI -->
    <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
        <h4 class="section-title">
            <i class="fa-solid fa-book-medical text-primary"></i> Pusat Edukasi Kulit
        </h4>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-news position-relative" data-bs-toggle="modal" data-bs-target="#modalBerita1">
                    <span class="badge-category">Kesehatan</span>
                    <div class="card-news-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1616401784845-180882ba9ba8?q=80&w=1000&auto=format&fit=crop" alt="Berita 1">
                    </div>
                    <div class="card-news-body">
                        <span class="news-date"><i class="fa-regular fa-calendar me-1"></i> 05 Mei 2026</span>
                        <h5 class="news-title">Pentingnya Menjaga Skin Barrier di Tengah Cuaca Ekstrem</h5>
                        <p class="news-excerpt">Ketahui cara merawat pelindung alami kulit Anda agar terhindar dari iritasi dan masalah serius lainnya...</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-news position-relative" data-bs-toggle="modal" data-bs-target="#modalBerita2">
                    <span class="badge-category">Teknologi Medis</span>
                    <div class="card-news-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1551076805-e18690c5e531?q=80&w=1000&auto=format&fit=crop" alt="Berita 2">
                    </div>
                    <div class="card-news-body">
                        <span class="news-date"><i class="fa-regular fa-calendar me-1"></i> 02 Mei 2026</span>
                        <h5 class="news-title">Implementasi AI untuk Deteksi Dini Masalah Kulit</h5>
                        <p class="news-excerpt">Bagaimana model Artificial Intelligence bekerja mendiagnosa kondisi kulit dengan akurasi tinggi dan cepat...</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-news position-relative" data-bs-toggle="modal" data-bs-target="#modalBerita3">
                    <span class="badge-category">Tips Harian</span>
                    <div class="card-news-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1556228578-0d85b1a4d571?q=80&w=1000&auto=format&fit=crop" alt="Berita 3">
                    </div>
                    <div class="card-news-body">
                        <span class="news-date"><i class="fa-regular fa-calendar me-1"></i> 28 April 2026</span>
                        <h5 class="news-title">5 Kandungan Skincare yang Wajib Dihindari Kulit Sensitif</h5>
                        <p class="news-excerpt">Tidak semua produk aman. Kenali bahan aktif yang memicu alergi dan kemerahan secara mendetail...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  

<!-- Modal Berita -->
<div class="modal fade" id="modalBerita1" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg">
      <div class="modal-header">
        <span class="modal-title-badge"><i class="fa-solid fa-tag me-1"></i> Kesehatan</span>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h3 class="modal-news-title">Pentingnya Menjaga Skin Barrier di Tengah Cuaca Ekstrem</h3>
        <p class="text-muted mb-4"><i class="fa-regular fa-calendar me-2"></i> Dipublikasikan pada 05 Mei 2026</p>
        <img src="https://images.unsplash.com/photo-1616401784845-180882ba9ba8?q=80&w=1000&auto=format&fit=crop" alt="Detail Berita 1">
        <div class="modal-news-text">
            <p>Di tengah cuaca panas yang ekstrem seperti belakangan ini, menjaga <strong>Skin Barrier</strong> (lapisan pelindung kulit terluar) menjadi sangat krusial. Lapisan ini bertugas mengunci kelembapan sekaligus mencegah polusi, bakteri, dan sinar UV merusak lapisan kulit yang lebih dalam.</p>
            <p>Gejala skin barrier yang rusak biasanya ditandai dengan kulit yang terasa sangat kering, mudah memerah (iritasi), timbulnya jerawat secara tiba-tiba, hingga terasa perih saat menggunakan produk skincare harian.</p>
            <h5 class="fw-bold mt-4 mb-3">Cara Memperbaiki Skin Barrier:</h5>
            <ul>
                <li><strong>Gunakan Pembersih Wajah Lembut:</strong> Hindari sabun cuci muka yang memberikan efek kesat atau ketarik setelah dibilas.</li>
                <li><strong>Cari Kandungan Ceramide:</strong> Gunakan moisturizer yang mengandung Ceramide, Hyaluronic Acid, atau Niacinamide.</li>
                <li><strong>Wajib Sunscreen:</strong> Aplikasikan tabir surya setiap pagi dan re-apply setiap 3 jam.</li>
            </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalBerita2" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg">
      <div class="modal-header">
        <span class="modal-title-badge"><i class="fa-solid fa-tag me-1"></i> Teknologi Medis</span>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h3 class="modal-news-title">Implementasi AI untuk Deteksi Dini Masalah Kulit</h3>
        <p class="text-muted mb-4"><i class="fa-regular fa-calendar me-2"></i> Dipublikasikan pada 02 Mei 2026</p>
        <img src="https://images.unsplash.com/photo-1551076805-e18690c5e531?q=80&w=1000&auto=format&fit=crop" alt="Detail Berita 2">
        <div class="modal-news-text">
            <p>Teknologi Artificial Intelligence (AI), khususnya <em>Case Based Reasoning</em> dan <em>Deep Learning</em>, kini mulai digunakan secara luas di bidang dermatologi. Sistem Pakar ini mampu mengenali pola gejala dan visual untuk mengidentifikasi penyakit secara dini.</p>
            <p>Dalam aplikasi seperti Skin AI, sistem akan memproses keluhan atau fitur gambar yang diunggah pengguna, mencocokkannya dengan bobot data latih, dan memberikan tingkat probabilitas jenis penyakit serta solusi medisnya secara cepat.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalBerita3" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg">
      <div class="modal-header">
        <span class="modal-title-badge"><i class="fa-solid fa-tag me-1"></i> Tips Harian</span>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h3 class="modal-news-title">5 Kandungan Skincare yang Wajib Dihindari Kulit Sensitif</h3>
        <p class="text-muted mb-4"><i class="fa-regular fa-calendar me-2"></i> Dipublikasikan pada 28 April 2026</p>
        <img src="https://images.unsplash.com/photo-1556228578-0d85b1a4d571?q=80&w=1000&auto=format&fit=crop" alt="Detail Berita 3">
        <div class="modal-news-text">
            <p>Bagi pemilik kulit sensitif, memilih produk skincare harus dilakukan secara hati-hati. Salah pilih sedikit saja bisa berujung pada reaksi <em>breakout</em>. Berikut adalah bahan aktif yang sebaiknya Anda waspadai:</p>
            <ol>
                <li><strong>Fragrance (Pewangi Buatan):</strong> Merupakan pemicu utama dermatitis kontak pada kulit sensitif.</li>
                <li><strong>Alkohol Kering (Denatured Alcohol):</strong> Membuat produk cepat kering di wajah tapi berisiko menguras minyak alami kulit.</li>
            </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>