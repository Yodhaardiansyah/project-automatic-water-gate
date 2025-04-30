<?php
include "../config/koneksi.php"; // Koneksi ke database

$error = "";
$success = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Hash password

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if (mysqli_query($koneksi, $sql)) {
        $success = "Registrasi berhasil! <a href='login.php'>Login</a>";
    } else {
        if (mysqli_errno($koneksi) == 1062) { // Duplicate entry error code
            $error = "Username sudah digunakan, silakan pilih username lain.";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($koneksi);
        }
    }

    mysqli_close($koneksi);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="box">
        <span class="borderLine"></span>
        <form action="register.php" method="POST"> <!-- Ganti action agar memproses login -->
            <h2>Sign up</h2>

            <?php if (!empty($error)) { ?>
                <p style="color: red; text-align: center;"><?php echo $error; ?></p>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <p style="color: green; text-align: center;"><?php echo $success; ?></p>
            <?php } ?>

            <div class="inputBox">
                <input type="text" name="username" required="required">
                <span>Username</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="password" name="password" required="required">
                <span>Password</span>
                <i></i>
            </div>
            <input type="submit" value="Daftar">
            <a href="login.php">Sudah punya Akun</a>
        </form>
    </div>
</body>
</html>

