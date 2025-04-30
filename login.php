<?php
session_start();
include "koneksi.php"; // Koneksi ke database

$error = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST["username"]);
    $password = $_POST["password"];

    // Debugging output
    error_log("Login attempt for user: " . $username);

    // Ambil data user dari database berdasarkan username
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        error_log("MySQL error: " . mysqli_error($koneksi));
    }
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password
    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        header("Location: index.php"); // Redirect ke halaman utama setelah login
        exit();
    } else {
        $error = "Username atau password salah!";
        error_log("Login failed for user: " . $username);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="box">
        <span class="borderLine"></span>
        <form action="login.php" method="POST"> <!-- Ganti action agar memproses login -->
            <h2>Sign in</h2>

            <?php if (!empty($error)) { ?>
                <p style="color: red; text-align: center;"><?php echo $error; ?></p>
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
            <div class="links">
                <a href="#">Forgot Password</a>
                <a href="register.php">Signup</a> <!-- Tambahkan link ke halaman registrasi -->
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
