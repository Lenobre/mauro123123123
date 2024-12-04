<?php
session_start();
require_once("pdo.php");

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para visualizar seus itens.";
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM itens WHERE id_criador = :usuario_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itens do Usuário</title>
</head>
<body>

<h1>Seus Itens em Leilão</h1>

<?php
if (isset($_SESSION["Message"])) {
    echo "<p>" . $_SESSION["Message"] . "</p>";
    $_SESSION["Message"] = "";
}

if (count($itens) > 0) {
    echo "<table border='1'>
            <tr><th>Nome do Item</th><th>Lance Mínimo</th><th>Valor Atual do Lance</th><th>Ação</th><th>Lances</th></tr>";
    
    foreach ($itens as $item) {
        $sql = "SELECT * FROM lances WHERE id_item = :item_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item['id'], PDO::PARAM_INT);
        $stmt->execute();
        $lances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $maior_lance = $item['minimo'];
        $vencedor_id = null;
        
        if (count($lances) > 0) {
            $max_lance = max(array_column($lances, 'valor'));
            $vencedor_id = array_search($max_lance, array_column($lances, 'valor'));
            $maior_lance = $max_lance;
        }

        echo "<tr>
                <td>" . htmlspecialchars($item['nome']) . "</td>
                <td>R$ " . number_format($item['minimo'], 2, ',', '.') . "</td>
                <td>R$ " . number_format($maior_lance, 2, ',', '.') . "</td>
                <td>";
        
        if ($item['status'] == 'aberto') {
            echo "<form method='POST' action=''>
                    <input type='hidden' name='item_id' value='" . $item['id'] . "'>
                    <input type='submit' name='encerrar_leilao' value='Encerrar Leilão'>
                  </form>";
        } else {
            if ($vencedor_id) {
                echo "<p>Vencedor: Usuário ID " . $vencedor_id . "</p>";
            }
            echo "<p>Leilão Encerrado</p>";
        }

        echo "</td><td>";
        if (count($lances) > 0) {
            echo "<ul>";
            foreach ($lances as $lance) {
                echo "<li>Usuário ID: " . $lance['id_usuario'] . " - R$ " . number_format($lance['valor'], 2, ',', '.') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Sem lances até o momento.</p>";
        }
        
        echo "</td></tr>";
    }

    echo "</table>";
} else {
    echo "<p>Você ainda não criou itens para leilão.</p>";
}

if (isset($_POST['encerrar_leilao']) && isset($_POST['item_id'])) {
    $item_id = (int) $_POST['item_id'];

    $sql = "SELECT * FROM itens WHERE id = :item_id AND id_criador = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item && $item['status'] == 'aberto') {
        $sql = "SELECT MAX(valor) AS maior_lance, id_usuario FROM lances WHERE id_item = :item_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
        $lance = $stmt->fetch(PDO::FETCH_ASSOC);

        $vencedor_id = $lance ? $lance['id_usuario'] : null;
        $sql = "UPDATE itens SET status = 'fechado', vencedor = :vencedor_id WHERE id = :item_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':vencedor_id', $vencedor_id, PDO::PARAM_INT);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION["Message"] = "Leilão encerrado com sucesso!";
        header("Location: itens.php");
        exit();
    } else {
        $_SESSION["Message"] = "Erro ao encerrar o leilão ou você não é o dono do item.";
        header("Location: itens.php");
        exit();
    }
}
?>

</body>
</html>
