<?php
require_once("pdo.php");
session_start();

$email = isset($_POST["email"]) ? $_POST["email"] : null;
$password = isset($_POST["password"]) ? hash("sha256", $_POST["password"]) : null;

if ($email === null || $password === null) {
    $_SESSION["Message"] = "Informe o e-mail e a senha para logar.";
    header("Location: login.php");
    exit();
}

try {
    $sql = "SELECT id, nome, senha FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        if ($password === $user['senha']) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION["Message"] = "Login bem-sucedido!";
            header("Location: itens.php");
            exit();
        } else {
            $_SESSION["Message"] = "Senha incorreta.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION["Message"] = "Usuário não encontrado.";
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION["Message"] = "Erro ao acessar o banco de dados: " . $e->getMessage();
    header("Location: login.php");
    exit();
}
?>
