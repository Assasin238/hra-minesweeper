<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <title>Minesweeper Game - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
<div class="form-container">
    <h2 class="form-title">Login</h2>

    <?php
        if (isset($_POST["login"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];
            require_once "database.php";

            // Použití připraveného dotazu
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email); // 's' značí, že je to string
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                if (password_verify($password, $user["password"])) {
                    // Uložení uživatelských údajů do session
                    $_SESSION["user"] = $user["nick_name"]; // Uložení přezdívky do session
                    $_SESSION["user_id"] = $user["id"];     // Uložení ID uživatele do session
                    header("Location: ../index.php");
                    exit();
                } else {
                    echo "<div class='error-msg'>Password does not match</div>";
                }
            } else {
                echo "<div class='error-msg'>Email does not match</div>";
            }

            // Zavření připraveného dotazu
            $stmt->close();
            $conn->close();
        }
    ?>

    <form action="login.php" method="post" class="form">
        <input type="email" placeholder="Email" name="email" class="input-field" required><br>
        <input type="password" placeholder="Password" name="password" class="input-field" required><br>
        <input type="submit" value="Login" name="login" class="submit-btn">
    </form>

    <div class="signup-btn-container">
        <a href="registration.php" class="signup-btn">Sign up</a>
    </div>
</div>

<?php include "../footer/footer.php" ?>
</body>
</html>
