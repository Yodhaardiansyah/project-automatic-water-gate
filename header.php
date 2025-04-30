<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Monitoring Irigasi IoT</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="user-info-header">
        <div class="user-info">
            Hello, <?php echo htmlspecialchars($username); ?>
            <form action="auth/logout.php" method="POST" style="margin:0; padding:0;">
                <button type="submit">Logout</button>
            </form>
        </div>
    </nav>
<div style="padding-top: 60px;">
