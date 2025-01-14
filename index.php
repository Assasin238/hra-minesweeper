<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: subpages/login.php");
    exit();
}

require_once "subpages/database.php"; // Připojení k databázi

$isAdmin = false;
$nickName = $_SESSION["user"];
echo "Checking admin for user: " . htmlspecialchars($nickName) . "<br>";
echo "Session User: " . htmlspecialchars($_SESSION["user"]) . "<br>";

// Převod na malá písmena pro porovnání bez ohledu na velikost písmen
$nickName = strtolower($nickName);

$sql = "SELECT * FROM admins WHERE LOWER(nick_name) = LOWER(?)";
$stmt = mysqli_prepare($conn, $sql);

// Kontrola, zda příprava dotazu probíhla správně
if ($stmt === false) {
    echo "SQL query preparation failed.<br>";
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $nickName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Kontrola, zda dotaz vrátil nějaký výsledek
if ($result) {
    echo "SQL query executed successfully.<br>";
    if (mysqli_fetch_assoc($result)) {
        $isAdmin = true;
        echo "User is admin.<br>";
    } else {
        echo "User is not admin.<br>";
    }
} else {
    echo "SQL query failed.<br>";
}

// Pokud je uživatel administrátor, zobrazí se příslušná zpráva
if ($isAdmin) {
    echo "User is an admin.";
} else {
    echo "User is not an admin.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <script src="scripts/language.js" defer></script>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Minesweeper Game</title>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><a href="#" id="title">Minesweeper Game</a></div>
        <!-- Language dropdown -->
        <div class="language-selector">
            <button id="current-lang"></button>
            <div class="language-menu">
                <a href="#" data-lang="en">English</a>
                <a href="#" data-lang="cs">Čeština</a>
                <a href="#" data-lang="de">Deutsch</a>
                <a href="#" data-lang="fr">Français</a>
            </div>
        </div>
        <!-- Admin button -->
        <?php if ($isAdmin): ?>
            <a href="subpages/admin.php" class="action_btn" id="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <!-- Logout button -->
        <a href="subpages/logout.php" class="action_btn" id="logout-btn">Logout</a>
    </div>
</header>
<main>
    <div class="container">
        <section id="welcome">
            <h1><span class="auto-type"></span></h1>
            <!-- Button to start playing the game -->
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
<?php include "php/footer.php" ?>
</body>
</html>
