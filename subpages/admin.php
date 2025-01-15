<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php"; // Připojení k databázi

// Kontrola, zda je uživatel admin
$nickName = $_SESSION["user"];
$sql = "SELECT * FROM admins WHERE nick_name = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $nickName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$isAdmin = mysqli_fetch_assoc($result) ? true : false;

// Zpracování přidání nového admina
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_admin"])) {
    $newAdmin = trim($_POST["new_admin"]);

    // Ověření, zda uživatel existuje
    $checkUserSql = "SELECT nick_name FROM users WHERE nick_name = ?";
    $stmt = mysqli_prepare($conn, $checkUserSql);
    mysqli_stmt_bind_param($stmt, "s", $newAdmin);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Přidání uživatele do adminů
        $addAdminSql = "INSERT INTO admins (nick_name) VALUES (?)";
        $addStmt = mysqli_prepare($conn, $addAdminSql);
        mysqli_stmt_bind_param($addStmt, "s", $newAdmin);
        mysqli_stmt_execute($addStmt);
        $message = "Uživatel byl přidán jako admin.";
    } else {
        $message = "Uživatel neexistuje.";
    }
}

// Načtení všech uživatelů
$usersSql = "SELECT nick_name, email FROM users";
$usersResult = mysqli_query($conn, $usersSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="../scripts/language.js" defer></script>
    <title>Admin Panel</title>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><a href="../index.php" id="title">Minesweeper Game</a></div>
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
        <a href="minesweeper.php" class="action_btn" id="logout-btn">Game</a>
        <a href="logout.php" class="action_btn" id="logout-btn">Logout</a>
    </div>
</header>
<main>
    <section id="main">
        <h1>Seznam uživatelů</h1>
        <div class="user-list">
            <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                <div class="user-card">
                    <p><strong>Přezdívka:</strong> <?= htmlspecialchars($user["nick_name"]) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?></p>
                    <form method="POST" class="delete-user-form">
                        <input type="hidden" name="delete_user" value="<?= htmlspecialchars($user["nick_name"]) ?>">
                        <button type="submit" class="delete-btn">Odstranit</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <section>
        <h2>Přidat nového admina</h2>
        <form method="POST" class="add-admin-form">
            <input type="text" name="new_admin" placeholder="Zadejte přezdívku uživatele" required>
            <button class="action_btn" type="submit">Přidat</button>
        </form>
        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </section>
</main>

<?php include "../php/footer.php" ?>
</body>
</html>
