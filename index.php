<?php
session_start();
if(!isset($_SESSION["user"])){
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
    <script src="scripts/web.js" defer></script>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Minesweeper Game</title>
</head>
<body>
<header>
        <div class="navbar">
            <div class="logo"><a href="#" title="Přejít na hlavní stránku">Minesweeper Game</a></div>
            <!-- Privacy action button -->
            <a href="subpages/registration.php" class="action_btn">Sign up</a>
            <a href="subpages/login.php" class="action_btn">Log in</a>
            <a href="subpages/logout.php" class="action_btn">Logout</a>
            <!-- Toggle button for responsive menu -->
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
            strings: ["Minesweeper", "Démineur", "Buscaminas", "Miny", "Campo Minado", "扫雷" ],
            typeSpeed: 150,
            backSpeed: 150,
            loop: true        
        })
    </script>
<?php include "php/footer.php" ?>
</body>
</html>