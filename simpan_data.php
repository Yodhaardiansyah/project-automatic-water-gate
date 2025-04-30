<?php
include "koneksi.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ketinggian = isset($_POST["ketinggian"]) ? floatval($_POST["ketinggian"]) : null;

    // Validasi input
    if ($ketinggian === null) {
        echo json_encode(["success" => false, "message" => "Ketinggian air tidak valid!"]);
        exit;
    }

    // Mulai transaksi database
    mysqli_begin_transaction($koneksi);

    try {
        // Simpan data ke sensor_air
        $sql_sensor = "INSERT INTO sensor_air (ketinggian) VALUES (?)";
        $stmt_sensor = mysqli_prepare($koneksi, $sql_sensor);
        mysqli_stmt_bind_param($stmt_sensor, "d", $ketinggian);
        
        if (!mysqli_stmt_execute($stmt_sensor)) {
            throw new Exception("Error menyimpan sensor: " . mysqli_error($koneksi));
        }
        mysqli_stmt_close($stmt_sensor);

        // **LOGIKA OTOMATIS:** Tentukan status pintu berdasarkan ketinggian air dan jadwal
        $batas_ketinggian = 50; // Sesuaikan dengan kebutuhan
        $status_pintu = ($ketinggian > $batas_ketinggian) ? "Terbuka" : "Tertutup";
        $sumber_perubahan = "Sensor";

        // Cek apakah ada jadwal aktif saat ini
        $current_time = date("H:i:s");
        $sql_jadwal = "SELECT * FROM jadwal WHERE waktu_buka <= ? AND waktu_tutup >= ?";
        $stmt_jadwal = mysqli_prepare($koneksi, $sql_jadwal);
        mysqli_stmt_bind_param($stmt_jadwal, "ss", $current_time, $current_time);
        mysqli_stmt_execute($stmt_jadwal);
        $result_jadwal = mysqli_stmt_get_result($stmt_jadwal);

        if (mysqli_num_rows($result_jadwal) > 0) {
            $status_pintu = "Terbuka";
            $sumber_perubahan = "Jadwal";
        }
        mysqli_stmt_close($stmt_jadwal);

        // Simpan status pintu ke pintu_air
        $sql_pintu = "INSERT INTO pintu_air (status, sumber_perubahan) VALUES (?, ?)";
        $stmt_pintu = mysqli_prepare($koneksi, $sql_pintu);
        mysqli_stmt_bind_param($stmt_pintu, "ss", $status_pintu, $sumber_perubahan);
        
        if (!mysqli_stmt_execute($stmt_pintu)) {
            throw new Exception("Error menyimpan pintu: " . mysqli_error($koneksi));
        }
        mysqli_stmt_close($stmt_pintu);

        // Commit transaksi
        mysqli_commit($koneksi);

        echo json_encode(["success" => true, "status_pintu" => $status_pintu, "message" => "Data berhasil disimpan"]);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    // Tutup koneksi
    mysqli_close($koneksi);
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan!"]);
}
?>
