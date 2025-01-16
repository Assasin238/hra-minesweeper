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

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $nickName = trim($_POST["nickname"]);
                $email = trim($_POST["email"]);
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_password"];

                // Validace dat
                if (empty($nickName) || empty($email) || empty($password) || empty($passwordRepeat)) {
                    $errors[] = "Všechny pole jsou povinné.";
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Enter valid email.";
                }
                if (strlen($password) < 12) {
                    $errors[] = "Password must be atleast 12 symbols.";
                }
                if ($password !== $passwordRepeat) {
                    $errors[] = "Passwords doesn't match.";
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
                            $errors[] = "This nickname is already registrated.";
                        } else {
                            // Kontrola, zda email již existuje
                            $checkEmailSql = "SELECT email FROM users WHERE email = ?";
                            if (mysqli_stmt_prepare($checkStmt, $checkEmailSql)) {
                                mysqli_stmt_bind_param($checkStmt, "s", $email);
                                mysqli_stmt_execute($checkStmt);
                                mysqli_stmt_store_result($checkStmt);

                                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                                    $errors[] = "This email is already registrated.";
                                } else {
                                    // Vložení nového uživatele
                                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                    $sql = "INSERT INTO users (nick_name, email, password) VALUES (?, ?, ?)";
                                    $stmt = mysqli_stmt_init($conn);

                                    if (mysqli_stmt_prepare($stmt, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "sss", $nickName, $email, $password_hash);

                                        if (mysqli_stmt_execute($stmt)) {
                                            echo "<div class='alert alert-success'>Registration was successful, your being dericted to Login!</div>";
                                            echo "<script>
                                                setTimeout(function() {
                                                    window.location.href = 'login.php';
                                                }, 2000);
                                            </script>";
                                        } else {
                                            $errors[] = "Něco se pokazilo při vkládání dat.";
                                        }
                                    } else {
                                        $errors[] = "Chyba v SQL dotazu: " . htmlspecialchars(mysqli_error($conn));
                                    }
                                }
                            } else {
                                $errors[] = "Chyba při kontrole emailu: " . htmlspecialchars(mysqli_error($conn));
                            }
                        }
                    } else {
                        $errors[] = "Chyba při kontrole přezdívky: " . htmlspecialchars(mysqli_error($conn));
                    }
                }
            }
            ?>

            <h2 class="form-title">Registration</h2>
            <form action="registration.php" method="post" class="mt-4">
                <div class="mb-3">
                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Enter your nickname">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
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
            <?php include "../php/footer.php" ?>
        </div>
    </div>
</body>
</html>
