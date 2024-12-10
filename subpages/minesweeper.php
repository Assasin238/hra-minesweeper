<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minesweeper Game - Game</title>
    <script src="../scripts/minesweeper.js" defer></script>
    <link rel="stylesheet" href="../css/game.css">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
</head>
<body>
<header>
        <div class="navbar">
            <div class="logo"><a href="../index.php" title="Přejít na hlavní stránku">Minesweeper Game</a></div>
            <!-- Privacy action button -->
            <a href="registration.php" class="action_btn">Sign up</a>
            <a href="login.php" class="action_btn">Log in</a>
            <a href="logout.php">Logout</a>
            <!-- Toggle button for responsive menu -->
            <div class="toggle_btn">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

    </header>
<div id="game-container">
        <table id="game-grid"></table>
</div>
<?php include "../php/footer.php" ?>
</body>
</html>