<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    $username = "Guest";
}
?>
<link rel="stylesheet" href="user_info.css">
<header class="user-info-header">
    <div class="user-info">
        Hello, <?php echo htmlspecialchars($username); ?>
        <form action="logout.php" method="POST" style="margin:0;">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>
