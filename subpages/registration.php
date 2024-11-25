<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <title>Minesweeper Game - Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])) {
            $nickName = $_POST["nickname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();

            if (empty($nickName) OR empty($email) OR empty($password) OR empty($passwordRepeat)){
                array_push($errors, "Všechny pole jsou povinné");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Email is not valid");
            }
            if (strlen($password)<12) {
                array_push($errors, "Password must be at least 12 characters long");
            }
            if ($password!==$passwordRepeat){
                array_push($errors, "Password does not match");
            }

            if (count($errors)>0) {
                foreach ($errors as $error) {
                    echo "<div>$error</div>";
                }
            }else{
                require_once "database.php"
                $sql = "INSERT INTO users (nick_name, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_perpare($stmt,$sql);
                if ($prepareStmt){
                    mysqli_stmt_bind_param($stmt, "sss",$nickName, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class="alert alert-success"></div>";
                }
            }

        } 
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="nickname" placeholder="NickName:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
    </div>

</body>
</html>