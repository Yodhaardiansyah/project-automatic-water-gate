<?php
include "config/koneksi.php";   
date_default_timezone_set("Asia/Jakarta");

$now = date("H:i:s"); // Waktu saat ini
$status = "Tertutup"; // Default

$sql = "SELECT * FROM jadwal";
$result = $koneksi->query($sql);

while ($row = $result->fetch_assoc()) {
    $buka = $row['waktu_buka'];
    $tutup = $row['waktu_tutup'];

    if ($buka < $tutup) {
        // Jadwal normal: buka dan tutup di hari yang sama
        if ($now >= $buka && $now <= $tutup) {
            $status = "Terbuka";
            break;
        }
    } 
}

echo $status;
?>
