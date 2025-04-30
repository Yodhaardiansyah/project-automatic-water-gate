<?php
include "config/koneksi.php";

// Query untuk mengambil data jadwal
$sql = "SELECT id, waktu_buka, waktu_tutup FROM jadwal ORDER BY waktu_buka DESC";
$result = mysqli_query($koneksi, $sql);

$jadwal = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $jadwal[] = $row;
    }
}

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode(["jadwal" => $jadwal]);

mysqli_close($koneksi);
?>
