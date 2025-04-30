<?php
include "config/koneksi.php";

// Ambil data status pintu air beserta sumber perubahan
$sql_pintu = "SELECT status, sumber_perubahan FROM pintu_air ORDER BY updated_at DESC LIMIT 1";
$result_pintu = mysqli_query($koneksi, $sql_pintu);
if (mysqli_num_rows($result_pintu) > 0) {
    $row_pintu = mysqli_fetch_assoc($result_pintu);
    $status_pintu = $row_pintu["status"];
    $sumber_perubahan = $row_pintu["sumber_perubahan"];
} else {
    $status_pintu = "Tidak diketahui";
    $sumber_perubahan = "Tidak ada data";
}

// Ambil data ketinggian air terbaru
$sql_sensor = "SELECT ketinggian, recorded_at FROM sensor_air ORDER BY recorded_at DESC LIMIT 1";
$result_sensor = mysqli_query($koneksi, $sql_sensor);
if (mysqli_num_rows($result_sensor) > 0) {
    $row_sensor = mysqli_fetch_assoc($result_sensor);
    $ketinggian_air = $row_sensor["ketinggian"];
    $recorded_at = $row_sensor["recorded_at"];
} else {
    $ketinggian_air = "Tidak ada data";
    $recorded_at = "Tidak tersedia";
}

// Ambil data jadwal irigasi
$sql_jadwal = "SELECT id, waktu_buka, waktu_tutup FROM jadwal ORDER BY waktu_buka ASC";
$result_jadwal = mysqli_query($koneksi, $sql_jadwal);
$jadwal = array();
if (mysqli_num_rows($result_jadwal) > 0) {
    while ($row_jadwal = mysqli_fetch_assoc($result_jadwal)) {
        $jadwal[] = $row_jadwal;
    }
}

// Kirim data dalam format JSON
$data = array(
    "status_pintu" => $status_pintu,
    "sumber_perubahan" => $sumber_perubahan,
    "ketinggian_air" => $ketinggian_air,
    "recorded_at" => $recorded_at,
    "jadwal" => $jadwal
);

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);

mysqli_close($koneksi);
?>
