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
    <title>Minesweeper Game - Registration</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <div class="parent-container">
        <div class="container mt-5">
            <?php
            $errors = [];
            $nickName = $email = ""; // Předvyplněné hodnoty polí

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $nickName = trim($_POST["nickname"]);
                $email = trim($_POST["email"]);
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_password"];

                // Validace dat
                if (empty($nickName)) {
                    $errors[] = "Nickname is required.";
                }
                if (empty($email)) {
                    $errors[] = "Email is required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Enter a valid email.";
                }
                if (strlen($password) < 8) { // Změněno na 8 znaků
                    $errors[] = "Password must be at least 8 symbols.";
                }
                if ($password !== $passwordRepeat) {
                    $errors[] = "Passwords don't match.";
                }

                if (empty($errors)) {
                    require_once "database.php";

                    // Kontrola, zda přezdívka již existuje
                    $checkNickSql = "SELECT nick_name FROM users WHERE nick_name = ?";
                    $checkStmt = mysqli_stmt_init($conn);

                    if (mysqli_stmt_prepare($checkStmt, $checkNickSql)) {
                        mysqli_stmt_bind_param($checkStmt, "s", $nickName);
                        mysqli_stmt_execute($checkStmt);
                        mysqli_stmt_store_result($checkStmt);

                        if (mysqli_stmt_num_rows($checkStmt) > 0) {
                            $errors[] = "This nickname is already registered.";
                        } else {
                            // Kontrola, zda email již existuje
                            $checkEmailSql = "SELECT email FROM users WHERE email = ?";
                            if (mysqli_stmt_prepare($checkStmt, $checkEmailSql)) {
                                mysqli_stmt_bind_param($checkStmt, "s", $email);
                                mysqli_stmt_execute($checkStmt);
                                mysqli_stmt_store_result($checkStmt);

                                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                                    $errors[] = "This email is already registered.";
                                } else {
                                    // Vložení nového uživatele
                                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                    $sql = "INSERT INTO users (nick_name, email, password) VALUES (?, ?, ?)";
                                    $stmt = mysqli_stmt_init($conn);

                                    if (mysqli_stmt_prepare($stmt, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "sss", $nickName, $email, $password_hash);

                                        if (mysqli_stmt_execute($stmt)) {
                                            echo "<div class='alert alert-success'>Registration was successful, you're being redirected to Login!</div>";
                                            echo "<script>
                                                setTimeout(function() {
                                                    window.location.href = 'login.php';
                                                }, 2000);
                                            </script>";
                                        } else {
                                            $errors[] = "Something went wrong during registration.";
                                        }
                                    } else {
                                        $errors[] = "SQL query error: " . htmlspecialchars(mysqli_error($conn));
                                    }
                                }
                            } else {
                                $errors[] = "Error checking email: " . htmlspecialchars(mysqli_error($conn));
                            }
                        }
                    } else {
                        $errors[] = "Error checking nickname: " . htmlspecialchars(mysqli_error($conn));
                    }
                }
            }
            ?>

            <h2 class="form-title">Registration</h2>
            <form action="registration.php" method="post" class="mt-4">
                <div class="mb-3">
                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Enter your nickname" value="<?= htmlspecialchars($nickName) ?>">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="repeat_password" name="repeat_password" placeholder="Repeat your password">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <?php if (!empty($errors)) : ?>
                <div class="error-container">
                    <?php foreach ($errors as $error) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php include "../footer/footer.php" ?>
    </div>
</body>
</html>
