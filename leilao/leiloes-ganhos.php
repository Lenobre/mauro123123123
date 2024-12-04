<?php
session_start();
require_once("pdo.php");

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para visualizar seus leilões ganhos.";
    header("Location: login.php");
    exit();
}

// Recupera os itens que o usuário venceu
$sql = "SELECT * FROM itens WHERE vencedor = :usuario_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$itens_ganhos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leilões Ganhos</title>
</head>
<body>

<h1>Leilões que Você Ganhou</h1>

<?php
// Exibe mensagens de sucesso ou erro
if (isset($_SESSION["Message"])) {
    echo "<p>" . $_SESSION["Message"] . "</p>";
    $_SESSION["Message"] = "";
}

if (count($itens_ganhos) > 0) {
    echo "<table border='1'>
            <tr><th>Nome do Item</th><th>Valor do Lance</th><th>Status</th></tr>";
    
    foreach ($itens_ganhos as $item) {
        // Recupera o valor do lance vencedor
        $sql = "SELECT valor FROM lances WHERE id_item = :item_id AND id_usuario = :usuario_id ORDER BY valor DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item['id'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        $stmt->execute();
        $lance_vencedor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Exibe as informações do item
        echo "<tr>
                <td>" . htmlspecialchars($item['nome']) . "</td>
                <td>R$ " . number_format($lance_vencedor['valor'], 2, ',', '.') . "</td>
                <td>" . htmlspecialchars($item['status']) . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>Você não ganhou nenhum leilão ainda.</p>";
}

?>

</body>
</html>
