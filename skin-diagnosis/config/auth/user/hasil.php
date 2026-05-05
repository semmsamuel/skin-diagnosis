<?php
session_start();
include '../config/koneksi.php';

$gejala_user=$_POST['gejala'];
$hasil=[];

$p=mysqli_query($conn,"SELECT * FROM penyakit");

while($pen=mysqli_fetch_assoc($p)){
    $r=mysqli_query($conn,"SELECT * FROM relasi WHERE penyakit_id='{$pen['id']}'");

    $total=0;
    $cocok=0;

    while($rel=mysqli_fetch_assoc($r)){
        $total += $rel['bobot'];

        if(in_array($rel['gejala_id'],$gejala_user)){
            $cocok += $rel['bobot'];
        }
    }

    if($total>0){
        $persen=($cocok/$total)*100;

        $hasil[]=[
            'nama'=>$pen['nama_penyakit'],
            'persen'=>$persen,
            'solusi'=>$pen['solusi'],
            'gambar'=>$pen['gambar']
        ];
    }
}

usort($hasil,function($a,$b){
    return $b['persen'] <=> $a['persen'];
});

$top3=array_slice($hasil,0,3);
?>

<h2>Top 3 Diagnosa</h2>

<?php foreach($top3 as $h): ?>
<div style="border:1px solid #000; margin:10px; padding:10px;">
<h3><?= $h['nama'] ?> (<?= round($h['persen'],2) ?>%)</h3>
<img src="../assets/img/<?= $h['gambar'] ?>" width="120"><br>
<b>Solusi:</b><br>
<?= $h['solusi'] ?>
</div>
<?php endforeach; ?>