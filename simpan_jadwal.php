<?php
include "config/koneksi.php";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $waktu_buka = trim($_POST["waktu_buka"]);
    $waktu_tutup = trim($_POST["waktu_tutup"]);

    // Validasi input tidak boleh kosong
    if (empty($waktu_buka) || empty($waktu_tutup)) {
        echo "<p style='color: red;'>Waktu buka dan tutup harus diisi!</p>";
        exit();
    }

    // Validasi format waktu (opsional, jika perlu)
    if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $waktu_buka) || 
        !preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $waktu_tutup)) {
        echo "<p style='color: red;'>Format waktu tidak valid!</p>";
        exit();
    }

    // Mencegah SQL Injection
    $waktu_buka = mysqli_real_escape_string($koneksi, $waktu_buka);
    $waktu_tutup = mysqli_real_escape_string($koneksi, $waktu_tutup);

    // Query untuk menyimpan data jadwal
    $sql = "INSERT INTO jadwal (waktu_buka, waktu_tutup) VALUES ('$waktu_buka', '$waktu_tutup')";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='atur_jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal!'); window.location='atur_jadwal.php';</script>";
    }

    mysqli_close($koneksi);
} else {
    echo "<p style='color: red;'>Akses tidak sah!</p>";
}
?>
