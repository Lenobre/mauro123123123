<?php
require_once("pdo.php");
session_start();

$name = isset($_POST["name"]) ? $_POST["name"] : null;
$password = isset($_POST["password"]) ? hash("sha256", $_POST["password"]) : null;
$email = isset($_POST["email"]) ? $_POST["email"] : null;

if ($name === null || $password === null || $email === null) {
    echo json_encode(["Message" => "Não foi possível realizar o cadastro"]);
    exit();
}
$sql = "INSERT INTO users (nome, email, senha) VALUES (:name, :email, :senha);";
$stmt = $conn->prepare($sql);

$stmt->bindParam(':name', $name);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':senha', $password);

try {
    $stmt->execute();
    echo json_encode(["Message" => "Usuário cadastrado com sucesso"]);
} catch (PDOException $e) {
    echo json_encode(["Message" => "Erro ao cadastrar usuário: " . $e->getMessage()]);
}
?>
