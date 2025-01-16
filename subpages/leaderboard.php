<?php
session_start();
require_once "database.php";

// Ověření, zda je uživatel přihlášen
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Načtení dat z databáze, aby se zobrazilo pouze nejlepší skóre každého uživatele pro každou obtížnost
$sql = "SELECT users.nick_name, leaderboard.difficulty, MIN(leaderboard.time) AS best_time
        FROM leaderboard
        JOIN users ON leaderboard.user_id = users.id
        GROUP BY users.nick_name, leaderboard.difficulty
        ORDER BY leaderboard.difficulty, best_time ASC
        LIMIT 10";
$leaderboardResult = mysqli_query($conn, $sql);

// Kontrola, zda je uživatel admin
$isAdmin = false;
$nickName = strtolower($_SESSION["user"]); // Převod na malá písmena pro porovnání bez ohledu na velikost písmen

$sql = "SELECT * FROM admins WHERE LOWER(nick_name) = LOWER(?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nickName);
    mysqli_stmt_execute($stmt);
    $adminResult = mysqli_stmt_get_result($stmt);

    if ($adminResult && mysqli_fetch_assoc($adminResult)) {
        $isAdmin = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minesweeper Leaderboard</title>
    <link rel="stylesheet" href="../css/leaderboard.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><a href="../index.php" id="title">Minesweeper Game</a></div>
        <?php if ($isAdmin): ?>
            <a href="admin.php" class="action_btn" id="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" class="action_btn" id="logout-btn">Logout</a>
    </div>
</header>

<main>
    <h1>Leaderboard</h1>
    <table>
        <thead>
            <tr>
                <th>Player</th>
                <th>Difficulty</th>
                <th>Time (s)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($leaderboardResult) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($leaderboardResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["nick_name"]) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row["difficulty"])) ?></td>
                        <td><?= htmlspecialchars($row["best_time"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No results yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="minesweeper.php" class="action_btn">Back to Game</a>
</main>
<?php include "../php/footer.php" ?>
</body>
</html>
