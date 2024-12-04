<?php
require_once("pdo.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION["Message"] = "Você precisa estar logado para cadastrar um item.";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $minimo = isset($_POST['minimo']) ? $_POST['minimo'] : null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_destino = 'uploads/' . basename($imagem_nome);

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($imagem_tmp, $imagem_destino)) {
            $_SESSION["Message"] = "Erro ao mover a imagem para o diretório de uploads.";
            header("Location: item.php");
            exit();
        }
    } else {
        $imagem_destino = '';
        $_SESSION["Message"] = "Imagem não enviada corretamente.";
        header("Location: item.php");
        exit();
    }

    if ($nome === null || $minimo === null || $imagem_destino === '') {
        $_SESSION["Message"] = "Todos os campos devem ser preenchidos.";
        header("Location: item.php");
        exit();
    }

    try {
        $sql = "INSERT INTO itens (nome, imagem, minimo, id_criador) VALUES (:nome, :imagem, :minimo, :id_criador)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':imagem', $imagem_destino);
        $stmt->bindParam(':minimo', $minimo);
        $stmt->bindParam(':id_criador', $_SESSION["usuario_id"]);

        $stmt->execute();

        $_SESSION["Message"] = "Item cadastrado com sucesso!";
        header("Location: item.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION["Message"] = "Erro ao cadastrar o item: " . $e->getMessage();
        header("Location: item.php");
        exit();
    }
}
?>
