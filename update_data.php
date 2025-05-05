<?php
include "config/koneksi.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ketinggian = isset($_POST["ketinggian"]) ? floatval($_POST["ketinggian"]) : null;
    $status_manual = isset($_POST["status"]) ? trim($_POST["status"]) : null;
    $sumber_perubahan = isset($_POST["sumber_perubahan"]) ? trim($_POST["sumber_perubahan"]) : "Sensor"; // Default dari sensor

    // Validasi input
    if ($ketinggian === null) {
        echo json_encode(["success" => false, "message" => "Ketinggian air tidak boleh kosong!"]);
        exit;
    }

    // Cek status pintu berdasarkan jadwal
    $waktu_sekarang = date("H:i:s");
    $sql_jadwal = "SELECT * FROM jadwal WHERE waktu_buka <= '$waktu_sekarang' AND waktu_tutup >= '$waktu_sekarang' LIMIT 1";
    $result_jadwal = mysqli_query($koneksi, $sql_jadwal);
    
    if (mysqli_num_rows($result_jadwal) > 0) {
        // Jika waktu sekarang sesuai jadwal, buka pintu air
        $status_otomatis = "Tertutup";
        $sumber_perubahan_otomatis = "Jadwal";
    } else {
        // Jika tidak ada jadwal, cek sensor air
        $batas_ketinggian = 50; // Batas tinggi air sebelum buka pintu
        if ($ketinggian > $batas_ketinggian) {
            $status_otomatis = "Terbuka";
            $sumber_perubahan_otomatis = "Sensor";
        } else {
            $status_otomatis = "Terbuka";
            $sumber_perubahan_otomatis = "Sensor";
        }
    }

    // Jika ada input manual, gunakan input manual
    if (!empty($status_manual)) {
        $status_final = $status_manual;
        $sumber_final = "Manual";
    } else {
        $status_final = $status_otomatis;
        $sumber_final = $sumber_perubahan_otomatis;
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Simpan data sensor ke tabel sensor_air
        $sql_sensor = "INSERT INTO sensor_air (ketinggian) VALUES (?)";
        $stmt_sensor = mysqli_prepare($koneksi, $sql_sensor);
        mysqli_stmt_bind_param($stmt_sensor, "d", $ketinggian);
        mysqli_stmt_execute($stmt_sensor);
        mysqli_stmt_close($stmt_sensor);

        // Simpan status pintu ke tabel pintu_air
        $sql_pintu = "INSERT INTO pintu_air (sumber_perubahan, status) VALUES (?, ?)";
        $stmt_pintu = mysqli_prepare($koneksi, $sql_pintu);
        mysqli_stmt_bind_param($stmt_pintu, "ss", $sumber_final, $status_final);
        mysqli_stmt_execute($stmt_pintu);
        mysqli_stmt_close($stmt_pintu);

        // Commit transaksi
        mysqli_commit($koneksi);

        echo json_encode([
            "success" => true,
            "status_pintu" => $status_final,
            "sumber_perubahan" => $sumber_final,
            "message" => "Data berhasil disimpan"
        ]);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }

    mysqli_close($koneksi);
} else {
    echo json_encode(["success" => false, "message" => "Akses tidak sah!"]);
}
?>
