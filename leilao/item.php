<?php 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para cadastrar um item.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Item para Leilão</title>
</head>
<body>
    <h1>Cadastrar Novo Item para Leilão</h1>
    <form method="POST" action="createItem.php" enctype="multipart/form-data">
        <label for="nome">Nome do Item:</label>
        <input type="text" name="nome" required><br><br>

        <label for="imagem">Imagem do Item:</label>
        <input type="file" name="imagem" required><br><br>

        <label for="minimo">Lance Mínimo:</label>
        <input type="number" name="minimo" step="0.01" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <?php
        if (isset($_SESSION["Message"]))
            echo $_SESSION["Message"];

        $_SESSION["Message"] = "";
    ?>
</body>
</html>