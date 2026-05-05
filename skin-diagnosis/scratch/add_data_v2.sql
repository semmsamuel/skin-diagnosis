-- ============================================================
-- PENAMBAHAN GEJALA BARU (ID 24 - 60)
-- Penyakit global & Indonesia
-- ============================================================
INSERT INTO gejala (id, nama_gejala) VALUES 
(24, 'Kulit berminyak'),
(25, 'Komedo hitam'),
(26, 'Komedo putih'),
(27, 'Kista di bawah kulit'),
(28, 'Bintik gelap tidak simetris'),
(29, 'Tepi luka tidak beraturan'),
(30, 'Luka tidak sembuh-sembuh'),
(31, 'Warna kulit berubah-ubah'),
(32, 'Sisik tebal keperakan'),
(33, 'Kulit retak/pecah-pecah'),
(34, 'Gatal di sela jari kaki'),
(35, 'Kulit terbakar matahari'),
(36, 'Kemerahan berbentuk cincin'),
(37, 'Bercak putih di kulit'),
(38, 'Kehilangan pigmen kulit'),
(39, 'Gatal di kulit kepala'),
(40, 'Ketombe'),
(41, 'Rambut rontok di area gatal'),
(42, 'Kulit kepala merah'),
(43, 'Jerawat di punggung'),
(44, 'Komedo di dada'),
(45, 'Beruntusan halus'),
(46, 'Gatal saat berkeringat'),
(47, 'Biang keringat'),
(48, 'Kulit melepuh kecil-kecil'),
(49, 'Kutil kecil keras'),
(50, 'Kutil di telapak kaki'),
(51, 'Bercak merah saat terkena matahari'),
(52, 'Kulit sangat sensitif'),
(53, 'Kulit bernanah'),
(54, 'Bisul besar memerah'),
(55, 'Infeksi di bawah kuku'),
(56, 'Kuku rapuh/berubah warna'),
(57, 'Kulit menghitam tidak merata'),
(58, 'Flek hitam karena hormonal'),
(59, 'Kulit gatal di malam hari parah'),
(60, 'Terowongan di bawah kulit');

-- ============================================================
-- PENAMBAHAN PENYAKIT BARU (ID 10 - 25)
-- ============================================================
INSERT INTO penyakit (id, nama_penyakit, solusi, gambar) VALUES
-- Penyakit Global
(10, 'Melanoma', 'Segera konsultasi ke dokter kulit (dermatologis). Melanoma adalah kanker kulit yang serius. Hindari paparan sinar UV berlebih dan gunakan tabir surya SPF 50+.', 'melanoma.jpg'),
(11, 'Karsinoma Sel Basal', 'Periksakan ke dokter. Umumnya ditangani dengan operasi/krioterapi. Hindari paparan matahari langsung dan gunakan tabir surya setiap hari.', 'karsinoma_basal.jpg'),
(12, 'Dermatitis Kontak', 'Hindari bahan yang menyebabkan reaksi (sabun, nikel, karet). Kompres dingin dan gunakan krim kortikosteroid ringan.', 'dermatitis.jpg'),
(13, 'Rosacea', 'Hindari pemicu: makanan pedas, alkohol, paparan matahari. Gunakan pelembab hypoallergenic dan konsultasikan obat topikal ke dokter.', 'rosacea.jpg'),
(14, 'Vitiligo', 'Konsultasi ke dokter. Bisa diobati dengan terapi cahaya UV atau krim kortikosteroid. Gunakan tabir surya untuk melindungi area yang terkena.', 'vitiligo.jpg'),
(15, 'Tinea Pedis (Kutu Air)', 'Jaga kaki tetap kering dan bersih. Gunakan obat antijamur (clotrimazole). Gunakan sandal di tempat umum (kolam, kamar mandi umum).', 'tinea_pedis.jpg'),
(16, 'Dermatitis Seboroik', 'Gunakan sampo khusus yang mengandung zinc pyrithione atau ketokonazol. Kelola stres dan jaga kebersihan kulit kepala.', 'seboroik.jpg'),
(17, 'Jerawat Dewasa / Acne Vulgaris', 'Cuci muka 2x sehari dengan sabun ringan. Gunakan benzoyl peroxide atau retinoid topikal. Hindari memencet jerawat.', 'acne_vulgaris.jpg'),
(18, 'Kutil (Verruca Vulgaris)', 'Bisa sembuh sendiri, namun bisa diobati dengan asam salisilat, krioterapi, atau laser oleh dokter. Jangan didiamkan jika tumbuh makin banyak.', 'kutil.jpg'),
(19, 'Biang Keringat (Miliaria)', 'Gunakan pakaian longgar berbahan katun. Mandi dengan air dingin setelah berkeringat. Hindari cuaca panas berlebih. Bedak salisilat bisa membantu.', 'miliaria.jpg'),
-- Penyakit Khas Indonesia / Tropis
(20, 'Panu (Tinea Versicolor)', 'Gunakan sampo ketokonazol atau selenium sulfida sebagai sabun mandi. Konsisten selama 2-4 minggu. Jaga kelembaban kulit.', 'panu.jpg'),
(21, 'Kaki Gajah (Filariasis Kulit)', 'Segera ke Puskesmas/RSUD untuk mendapatkan obat DEC (Diethylcarbamazine). Jaga kebersihan lingkungan dan hindari gigitan nyamuk.', 'filariasis.jpg'),
(22, 'Frambusia (Patek)', 'Segera konsultasi ke dokter. Diobati dengan suntikan penisilin atau azitromisin oral dosis tunggal. Penyakit ini sangat menular.', 'frambusia.jpg'),
(23, 'Bisul (Furunkel)', 'Kompres hangat 3-4x sehari selama 15 menit. Jangan dipencet. Jika tidak membaik, konsultasi dokter untuk pemberian antibiotik atau insisi.', 'bisul.jpg'),
(24, 'Pioderma / Infeksi Bakteri Kulit', 'Jaga kebersihan luka. Gunakan antiseptik dan salep antibiotik (mupirocin). Konsultasi dokter jika menyebar.', 'pioderma.jpg'),
(25, 'Hiperpigmentasi / Melasma', 'Gunakan tabir surya SPF 50+ setiap hari. Konsultasi dokter untuk krim pemutih (hidrokuinon, kojic acid). Hindari sinar matahari langsung.', 'melasma.jpg');

-- ============================================================
-- RELASI PENYAKIT BARU DENGAN GEJALA
-- ============================================================

-- Melanoma (10): Bintik gelap tidak simetris, Tepi tidak beraturan, Luka tidak sembuh, Warna berubah
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(10, 28, 1.0), (10, 29, 1.0), (10, 30, 0.9), (10, 31, 0.9), (10, 2, 0.5);

-- Karsinoma Sel Basal (11): Luka tidak sembuh, Bintik cokelat, Perih
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(11, 30, 1.0), (11, 28, 0.8), (11, 6, 0.7), (11, 29, 0.8);

-- Dermatitis Kontak (12): Kemerahan, Gatal, Ruam, Kulit kering, Melepuh
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(12, 2, 0.8), (12, 1, 0.9), (12, 5, 0.8), (12, 3, 0.7), (12, 7, 0.7);

-- Rosacea (13): Kemerahan wajah, Bintik merah, Kulit sensitif, Rasa panas
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(13, 2, 0.9), (13, 52, 0.9), (13, 10, 0.7), (13, 8, 0.6), (13, 5, 0.5);

-- Vitiligo (14): Bercak putih, Kehilangan pigmen
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(14, 37, 1.0), (14, 38, 1.0), (14, 57, 0.6);

-- Tinea Pedis / Kutu Air (15): Gatal sela jari, Berair, Kulit mengelupas, Bersisik
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(15, 34, 1.0), (15, 14, 0.8), (15, 9, 0.8), (15, 4, 0.7), (15, 1, 0.8);

-- Dermatitis Seboroik (16): Ketombe, Kulit kepala merah, Gatal kepala, Rambut rontok
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(16, 40, 1.0), (16, 42, 0.9), (16, 39, 0.9), (16, 41, 0.7), (16, 4, 0.6);

-- Jerawat Dewasa / Acne Vulgaris (17): Berminyak, Komedo hitam, Komedo putih, Kista, Jerawat punggung
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(17, 24, 0.9), (17, 25, 0.9), (17, 26, 0.8), (17, 27, 0.7), (17, 43, 0.6), (17, 44, 0.5);

-- Kutil / Verruca (18): Kutil keras, Kutil telapak kaki, Tidak gatal
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(18, 49, 1.0), (18, 50, 0.9);

-- Biang Keringat / Miliaria (19): Biang keringat, Gatal berkeringat, Beruntusan halus, Ruam
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(19, 47, 1.0), (19, 46, 0.9), (19, 45, 0.8), (19, 5, 0.7), (19, 48, 0.6);

-- Panu / Tinea Versicolor (20): Bercak putih, Kehilangan pigmen sebagian, Gatal ringan
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(20, 37, 0.9), (20, 38, 0.8), (20, 1, 0.5), (20, 12, 0.7);

-- Kaki Gajah (21): Pembengkakan, Kulit menebal, Kemerahan
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(21, 13, 1.0), (21, 2, 0.7), (21, 11, 0.8), (21, 16, 0.6);

-- Frambusia (22): Ruam, Luka tidak sembuh, Bintil kuning
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(22, 5, 0.8), (22, 30, 0.9), (22, 21, 0.9), (22, 11, 0.7);

-- Bisul (23): Bisul besar, Nyeri, Bernanah, Kemerahan
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(23, 54, 1.0), (23, 11, 0.9), (23, 53, 0.9), (23, 2, 0.7), (23, 10, 0.6);

-- Pioderma (24): Bernanah, Kemerahan, Melepuh, Infeksi kuku
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(24, 53, 1.0), (24, 2, 0.8), (24, 7, 0.7), (24, 55, 0.8), (24, 56, 0.7);

-- Melasma / Hiperpigmentasi (25): Flek hitam hormonal, Kulit menghitam, Bercak cokelat
INSERT INTO relasi (penyakit_id, gejala_id, bobot) VALUES
(25, 58, 1.0), (25, 57, 0.9), (25, 28, 0.6), (25, 51, 0.5);
