<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="createLogin.php" method="post">
        <label for="email">E-mail</label>
        <input type="email" name="email" required>
        <br>

        <label for="password">Password</label>
        <input type="password" name="password" required>
        <br>

        <button type="submit">Logar</button>
    </form>
    <?php 
    if (isset($_SESSION["Message"]))
        echo $_SESSION["Message"];

    $_SESSION["Message"] = "";
    ?>
</body>
</html>