
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
    <title>Minesweeper Game - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <header class="header">
        <a href="../index.php" class="logo">Minesweeper</a>
        <div class="header-buttons">
            <a href="login.php" class="btn">Sign In</a>
            <a href="registration.php" class="btn">Sign Up</a>
        </div>
    </header>
 
    <div class="form-container">
        <h2 class="form-title">Login</h2>
 
        <?php
            if (isset($_POST["login"])){
                $email = $_POST["email"];
                $password = $_POST["password"];
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($user){
                    if (password_verify($password, $user["password"])){
                        session_start();
                        $_SESSION["user"] = "yes";
                        header("Location: ../index.php");
                        die();
                    } else {
                        echo "<div class='error-msg'>Password does not match</div>";
                    }
                } else {
                    echo "<div class='error-msg'>Email does not match</div>";
                }
            }
        ?>
 
        <form action="login.php" method="post" class="form">
            <input type="text" placeholder="Email" name="email" class="input-field" required><br>
            <input type="password" placeholder="Password" name="password" class="input-field" required><br>
            <input type="submit" value="Login" name="login" class="submit-btn">
        </form>
    </div>
 <?php include "../php/footer.php" ?>
</body>
</html>