<?php
include '../config/koneksi.php';

if(isset($_POST['daftar'])){
    $nama=$_POST['nama'];
    $email=$_POST['email'];
    $pass=md5($_POST['password']);

    mysqli_query($conn,"INSERT INTO user VALUES(NULL,'$nama','$email','$pass')");
    header("Location: login.php");
}
?>

<form method="POST">
<input name="nama" placeholder="Nama"><br>
<input name="email" placeholder="Email"><br>
<input type="password" name="password"><br>
<button name="daftar">Daftar</button>
</form>