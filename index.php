<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <!-- Link to external JavaScript file -->
    <script src="scripts/web.js" defer></script>
    <title>Minesweeper Game</title>
</head>
<body>
<header>
        <div class="navbar">
            <div class="logo"><a href="#" title="Přejít na hlavní stránku">Minesweeper Game</a></div>
            <ul class="links">
                <!-- Navigation links -->
                <li><a href="subpages/minesweeper.php">Game</a></li>
            </ul>
            <!-- Privacy action button -->
            <a href="subpages/registration.php" class="action_btn">Sign up</a>
            <a href="subpages/login.php" class="action_btn">Log in</a>
            <!-- Toggle button for responsive menu -->
            <div class="toggle_btn">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

    </header>
    <main>
        <section id="uvod">
            <h1>Welcome</h1>
            <!-- Button to start playing the game -->
            <a href="subpages/minesweeper.php" class="action_btn" id="playBTN">Play</a>
        </section>
    </main>

<?php include "php/footer.php" ?>
</body>
</html>