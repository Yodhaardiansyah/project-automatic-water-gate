<?php
$host       = "localhost";
$user       = "root";
$password   = "";
$database   = "irigasi_db"; // Sesuaikan dengan nama database

// Mengaktifkan mode error untuk debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Membuat koneksi ke database
    $koneksi = new mysqli($host, $user, $password, $database);

    // Set karakter set ke UTF-8
    $koneksi->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Jika gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
