<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: subpages/login.php");
    exit();
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
