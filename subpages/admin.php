<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/database.php"; // Připojení k databázi

// Kontrola, zda je uživatel admin
$nickName = $_SESSION["user"];
$sql = "SELECT * FROM admins WHERE nick_name = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $nickName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!mysqli_fetch_assoc($result)) {
    header("Location: ../index.php");
    exit();
}

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
    <title>Admin Panel</title>
</head>
<body>
<header>
    <h1>Admin Panel</h1>
</header>
<main>
    <section>
        <h2>Seznam uživatelů</h2>
        <table>
            <thead>
                <tr>
                    <th>Přezdívka</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user["nick_name"]) ?></td>
                        <td><?= htmlspecialchars($user["email"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <section>
        <h2>Přidat nového admina</h2>
        <form method="POST">
            <input type="text" name="new_admin" placeholder="Zadejte přezdívku uživatele">
            <button type="submit">Přidat</button>
        </form>
        <?php if (isset($message)): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
