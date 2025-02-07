<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: subpages/login.php");
    exit();
}

require_once "subpages/database.php"; // Připojení k db

$isAdmin = false;
$nickName = strtolower($_SESSION["user"]); // Převádí na malá písmena

$sql = "SELECT * FROM admins WHERE LOWER(nick_name) = LOWER(?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nickName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_fetch_assoc($result)) {
        $isAdmin = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Minesweeper Game</title>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><a href="#" id="title">Minesweeper Game</a></div>
        <!-- Admin button -->
        <?php if ($isAdmin): ?>
            <a href="subpages/admin.php" class="action_btn" id="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <a href="subpages/leaderboard.php" class="action_btn">leaderboard</a>
        <!-- Logout button -->
        <a href="subpages/logout.php" class="action_btn" id="logout-btn">Logout</a>
    </div>
</header>
<main>
    <div class="container">
        <section id="welcome">
            <h1><span class="auto-type"></span></h1>
            <a href="subpages/minesweeper.php" class="action_btn" id="playBTN">PLAY</a>
        </section>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
<script>
    var typed = new Typed(".auto-type", {
        strings: ["Minesweeper", "Démineur", "Buscaminas", "Miny", "Campo Minado", "扫雷"],
        typeSpeed: 150,
        backSpeed: 150,
        loop: true        
    });
</script>
<?php include "footer/footer.php" ?>
</body>
</html>
