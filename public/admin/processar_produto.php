<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome_produto']);
    $descricao = trim($_POST['descricao_produto']);
    $preco = $_POST['preco_produto'];
    $categoria = trim($_POST['categoria']);

    if (empty($nome) || empty($descricao) || empty($preco) || empty($categoria) || !isset($_FILES['imagem']) || $_FILES['imagem']['error'] != 0) {
        die("Erro: Todos os campos e a imagem são obrigatórios.");
    }

    $imagem = $_FILES['imagem'];
    $diretorio_upload = __DIR__ . '/../assets/images/';
    $nome_imagem = uniqid() . '_' . basename($imagem['name']);
    $caminho_completo = $diretorio_upload . $nome_imagem;

    $check = getimagesize($imagem["tmp_name"]);
    if($check === false) {
        die("Erro: O arquivo enviado não é uma imagem válida.");
    }
    
    if (!move_uploaded_file($imagem['tmp_name'], $caminho_completo)) {
        die("Erro: Falha ao fazer o upload da imagem.");
    }

    try {
        $sql = "INSERT INTO produtos (nome_produto, descricao_produto, preco_produto, categoria, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $descricao, $preco, $categoria, $nome_imagem]);

        header("Location: " . BASE_URL . "/admin/index.php?status=sucesso");
        exit();

    } catch (PDOException $e) {
        die("Erro ao salvar o produto no banco de dados: " . $e->getMessage());
    }
} else {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}
?>