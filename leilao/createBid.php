<?php
require_once("pdo.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para fazer um lance.";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = isset($_POST['item_id']) ? (int) $_POST['item_id'] : null;
    $lance = isset($_POST['lance']) ? (float) $_POST['lance'] : null;

    if ($item_id === null || $lance === null) {
        $_SESSION["Message"] = "Por favor, preencha todos os campos para fazer o lance.";
        header("Location: itens.php");
        exit();
    }

    try {
        $sql = "SELECT minimo FROM itens WHERE id = :item_id AND status = 'aberto'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $_SESSION["Message"] = "Item não encontrado ou não está aberto para lances.";
            header("Location: itens.php");
            exit();
        }

        if ($lance < $item['minimo']) {
            $_SESSION["Message"] = "O lance deve ser maior que o valor mínimo de " . $item['minimo'];
            header("Location: itens.php");
            exit();
        }

        $sql = "INSERT INTO lances (id_item, id_usuario, valor) VALUES (:item_id, :usuario_id, :lance)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        $stmt->bindParam(':lance', $lance, PDO::PARAM_STR);
        $stmt->execute();

        $sql = "UPDATE itens SET minimo = :lance WHERE id = :item_id AND status = 'aberto'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':lance', $lance, PDO::PARAM_STR);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION["Message"] = "Lance feito com sucesso!";
        header("Location: itens.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION["Message"] = "Erro ao fazer o lance: " . $e->getMessage();
        header("Location: itens.php");
        exit();
    }
}
?>
