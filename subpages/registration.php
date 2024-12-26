
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
    <div class="container mt-5">
        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Získání dat z formuláře
            $nickName = trim($_POST["nickname"]);
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            
            $errors = [];

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

            // Pokud jsou chyby, zobrazíme je
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                // Zpracování hesla a vložení dat do databáze
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                require_once "database.php";

                $sql = "INSERT INTO users (nick_name, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $nickName, $email, $password_hash);

                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='alert alert-success'>Registrace byla úspěšná!</div>";
                        echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        },1500); // 1,5 sekundy delay
                        </script>";
                    } else {
                        echo "<div class='alert alert-danger'>Něco se pokazilo při vkládání dat.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Chyba v SQL dotazu: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
                }
            }
        }
        ?>
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
</body>
</html>
