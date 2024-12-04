<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para cadastrar um item.";
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    require_once("pdo.php");

    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION["Message"] = "Você precisa estar logado para fazer um lance.";
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT * FROM itens WHERE status = 'aberto'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

<h1>Itens em Leilão</h1>
    <?php
    if (isset($_SESSION["Message"])) {
        echo "<p>" . $_SESSION["Message"] . "</p>";
        $_SESSION["Message"] = "";
    }

    if (count($itens) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Nome</th><th>Imagem</th><th>Lance Mínimo</th><th>Ação</th></tr>";
            
        foreach ($itens as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['nome']) . "</td>";
            echo "<td><img src='" . htmlspecialchars($item['imagem']) . "' alt='Imagem do item' width='100'></td>";
            echo "<td>" . htmlspecialchars($item['minimo']) . "</td>";
            echo "<td>
                    <form method='POST' action='createBid.php'>
                        <input type='hidden' name='item_id' value='" . $item['id'] . "'>
                        <input type='number' name='lance' step='0.01' required>
                        <input type='submit' value='Fazer Lance'>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Não há itens em leilão no momento.</p>";
    }
    ?>
</body>
</html>