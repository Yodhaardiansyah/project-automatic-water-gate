<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Irigasi IoT</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h1>Monitoring Irigasi IoT</h1>

    <!-- Data Pintu Air & Ketinggian Air -->
    <div class="data-container">
        <div class="data-box">
            <h2>Status Pintu Air</h2>
            <p id="status">Loading...</p>
            <small id="sumber_perubahan"></small>
        </div>
        <div class="data-box">
            <h2>Ketinggian Air (cm)</h2>
            <p id="ketinggian">Loading...</p>
        </div>
    </div>

    <!-- Riwayat Jadwal -->
    <h2>Riwayat Jadwal</h2>
    <table>
        <thead>
            <tr>
                <th>Waktu Buka</th>
                <th>Waktu Tutup</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="jadwal-table">
            <tr>
                <td colspan="4">Loading...</td>
            </tr>
        </tbody>
    </table>

    <!-- Tombol untuk membuka halaman penjadwalan -->
    <a href="atur_jadwal.php">
        <button id="atur-penjadwalan">Atur Penjadwalan</button>
    </a>

    <script>
    function bacaData() {
        $.ajax({
            url: 'get_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Perbarui status pintu air dan ketinggian air
                $("#status").text(data.status_pintu || "Tidak diketahui");
                $("#sumber_perubahan").text("Sumber: " + (data.sumber_perubahan || "Tidak ada data"));
                $("#ketinggian").text((data.ketinggian_air || "0") + " cm");

                // Perbarui tabel riwayat jadwal
                var tableBody = "";
                if (data.jadwal.length > 0) {
                    data.jadwal.forEach(function(item) {
                        tableBody += `<tr>
                            <td>${item.waktu_buka}</td>
                            <td>${item.waktu_tutup}</td>
                            <td><button onclick="hapusJadwal(${item.id})">Hapus</button></td>
                        </tr>`;
                    });
                } else {
                    tableBody = `<tr><td colspan="4">Tidak ada jadwal</td></tr>`;
                }

                $("#jadwal-table").html(tableBody);
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                $("#status").text("Gagal memuat data");
                $("#ketinggian").text("Gagal memuat data");
            }
        });
    }

    function hapusJadwal(id) {
        if (confirm("Apakah Anda yakin ingin menghapus jadwal ini?")) {
            $.ajax({
                url: 'hapus_jadwal.php',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    alert("Jadwal berhasil dihapus!");
                    bacaData();
                },
                error: function(xhr, status, error) {
                    alert("Gagal menghapus jadwal: " + error);
                }
            });
        }
    }

    $(document).ready(function() {
        bacaData(); // Panggil pertama kali saat halaman dimuat
        setInterval(bacaData, 5000); // Panggil setiap 5 detik (5000 ms)
    });
    </script>

</body>
</html>
