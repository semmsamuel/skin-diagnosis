<?php session_start(); ?>
<h2>Halo <?= $_SESSION['user']['nama']; ?></h2>
<a href="diagnosa.php">Diagnosa</a>