<?php
include '../config/koneksi.php';

// tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $solusi = $_POST['solusi'];
    $gambar = $_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/img/".$gambar);

    mysqli_query($conn, "INSERT INTO penyakit VALUES(NULL,'$nama','$solusi','$gambar')");
}

// tampil data
$data = mysqli_query($conn, "SELECT * FROM penyakit");
?>

<h2>Data Penyakit</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama" placeholder="Nama Penyakit"><br>
    <textarea name="solusi" placeholder="Solusi"></textarea><br>
    <input type="file" name="gambar"><br>
    <button name="tambah">Tambah</button>
</form>

<hr>

<?php while($d = mysqli_fetch_assoc($data)): ?>
    <p><?= $d['nama_penyakit']; ?></p>
<?php endwhile; ?>
<?php include '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $nama=$_POST['nama'];
    $solusi=$_POST['solusi'];
    $gambar=$_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'],"../assets/img/".$gambar);

    mysqli_query($conn,"INSERT INTO penyakit VALUES(NULL,'$nama','$solusi','$gambar')");
}
?>

<form method="POST" enctype="multipart/form-data">
<input name="nama"><br>
<textarea name="solusi"></textarea><br>
<input type="file" name="gambar"><br>
<button name="tambah">Tambah</button>
</form>