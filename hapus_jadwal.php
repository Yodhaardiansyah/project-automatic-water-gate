<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    // Validasi ID (harus angka dan aman dari SQL Injection)
    $id = intval($id);

    if ($id > 0) {
        $sql = "DELETE FROM jadwal WHERE id = $id";

        if (mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Jadwal berhasil dihapus."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus jadwal: " . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID tidak valid."]);
    }

    mysqli_close($koneksi);
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid!"]);
}
?>
