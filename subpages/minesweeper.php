<?php
session_start();
require_once "database.php";

// Ověření, zda je uživatel přihlášen
if (!isset($_SESSION["user_id"])) {
    http_response_code(403); // Zakázáno
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

// Zpracování AJAX požadavku pro uložení výsledku
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["action"]) && $data["action"] === "save_score") {
        $userId = $_SESSION["user_id"];
        $difficulty = $data["difficulty"];
        $time = $data["time"];
    
        // Validace dat
        if (!in_array($difficulty, ["easy", "medium", "hard"]) || !is_numeric($time)) {
            http_response_code(400); // Špatný požadavek
            echo json_encode(["error" => "Invalid data"]);
            exit();
        }
    
        // Logování pro ladění
        error_log("Saving score: user_id={$userId}, difficulty={$difficulty}, time={$time}");
        
        // Uložení do databáze
        $sql = "INSERT INTO leaderboard (user_id, difficulty, time) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $userId, $difficulty, $time);
    
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500); // Interní chyba serveru
            echo json_encode(["error" => "Failed to save score"]);
        }
        exit();
    }
}
?>



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
            <a href="leaderboard.php" class="action_btn">leaderboard</a>
            <a href="logout.php" class="action_btn">Logout</a>
            <!-- Toggle button for responsive menu -->
            <div class="toggle_btn">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

    </header>
<div id="game-container">
    <div id="flag-counter">Vlajky: 0/0</div> <!-- Počítadlo vlajek -->
    <table id="game-grid"></table>
</div>
<?php include "../php/footer.php" ?>
</body>
</html>