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
    <div class="parent-container"> <!-- Přidání rodičovského kontejneru -->
        <div class="container mt-5">
            <?php
            $errors = []; // Inicializace chybového pole

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // Získání dat z formuláře
                $nickName = trim($_POST["nickname"]);
                $email = trim($_POST["email"]);
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_password"];
                
                // Validace dat
                if (empty($nickName) || empty($email) || empty($password) || empty($passwordRepeat)) {
                    $errors[] = "Všechny pole jsou povinné.";
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Zadejte platný email.";
                }
                if (strlen($password) < 12) {
                    $errors[] = "Heslo musí mít alespoň 12 znaků.";
                }
                if ($password !== $passwordRepeat) {
                    $errors[] = "Hesla se neshodují.";
                }

            if (empty($errors)) {
                require_once "database.php";

                // prvně verify zda email je už v db
                $checkEmailSql = "SELECT email FROM users WHERE email = ?";
                $checkStmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($checkStmt, $checkEmailSql)) {
                    mysqli_stmt_bind_param($checkStmt, "s", $email);
                    mysqli_stmt_execute($checkStmt);
                    mysqli_stmt_store_result($checkStmt);

                    if (mysqli_stmt_num_rows($checkStmt) > 0) {
                        $errors[] = "Tento email je již registrován.";
                    } else {
                        // po emailu přejde na druhou část
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);

                        $sql = "INSERT INTO users (nick_name, email, password) VALUES (?, ?, ?)";
                        $stmt = mysqli_stmt_init($conn);

                        if (mysqli_stmt_prepare($stmt, $sql)) {
                            mysqli_stmt_bind_param($stmt, "sss", $nickName, $email, $password_hash);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<div class='alert alert-success'>Registrace byla úspěšná, Přesměrováváme Vás na Login!</div>";
                                echo "<script>
                                setTimeout(function() {
                                    window.location.href = 'login.php';
                                },2000); // 2 sekundy delay
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

            }
            ?>

            <!-- Formulář -->
            <h2 class="form-title">Registration</h2>
            <form action="registration.php" method="post" class="mt-4">
                <div class="mb-3">
                    <label for="nickname" class="form-label">NickName:</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Zadejte svůj přezdívku">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Zadejte svůj email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Heslo:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Zadejte heslo">
                </div>
                <div class="mb-3">
                    <label for="repeat_password" class="form-label">Zopakujte heslo:</label>
                    <input type="password" class="form-control" id="repeat_password" name="repeat_password" placeholder="Zadejte heslo znovu">
                </div>
                <button type="submit" class="btn btn-primary">Registrovat</button>
            </form>
        </div>
        <!-- Chybové hlášky (mimo formulář) -->
        <?php if (!empty($errors)) : ?>
            <div class="error-container">
                <?php foreach ($errors as $error) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php include "../php/footer.php" ?>
    </div>
</body>
</html>
