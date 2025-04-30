<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Jadwal Irigasi</title>
    <link rel="stylesheet" href="jadwal.css">
</head>
<body>
    <h1>Atur Jadwal Irigasi</h1>

    <!-- Daftar Jadwal yang Sudah Ada -->
    <h2>Jadwal yang Tersedia</h2>
    <table>
        <thead>
            <tr>
                <th>Waktu Buka</th>
                <th>Waktu Tutup</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "koneksi.php";

            // Ambil data dari tabel jadwal
            $sql = "SELECT id, waktu_buka, waktu_tutup FROM jadwal ORDER BY waktu_buka ASC";
            $result = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["waktu_buka"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["waktu_tutup"]) . "</td>";
                    echo "<td><button onclick='hapusJadwal(" . $row["id"] . ")'>Hapus</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Tidak ada jadwal yang tersedia.</td></tr>";
            }

            mysqli_close($koneksi);
            ?>
        </tbody>
    </table>

    <!-- Form Tambah Jadwal -->
    <h2>Tambah Jadwal Baru</h2>
    <form action="simpan_jadwal.php" method="POST">
        <label for="waktu_buka">Waktu Buka:</label>
        <input type="time" id="waktu_buka" name="waktu_buka" required><br><br>

        <label for="waktu_tutup">Waktu Tutup:</label>
        <input type="time" id="waktu_tutup" name="waktu_tutup" required><br><br>

        <button type="submit" name="tambah">Simpan Jadwal</button>
    </form>

    <br>
    <a href="index.php">
        <button>Kembali</button>
    </a>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function hapusJadwal(id) {
            if (confirm("Apakah Anda yakin ingin menghapus jadwal ini?")) {
                $.ajax({
                    url: 'hapus_jadwal.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            }
        }
    </script>

</body>
</html>
