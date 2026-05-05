<?php include '../config/koneksi.php'; ?>

<form action="hasil.php" method="POST">
<h3>Pilih Gejala:</h3>

<?php
$g=mysqli_query($conn,"SELECT * FROM gejala");
while($d=mysqli_fetch_assoc($g)){
    echo "<input type='checkbox' name='gejala[]' value='{$d['id']}'> {$d['nama_gejala']}<br>";
}
?>

<button>Diagnosa</button>
</form>