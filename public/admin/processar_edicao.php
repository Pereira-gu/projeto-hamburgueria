<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação CSRF.');
}
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome_produto']);
    $descricao = trim($_POST['descricao_produto']);
    $preco = $_POST['preco_produto'];
    $categoria = trim($_POST['categoria']);
    $opcionais_selecionados = $_POST['opcionais'] ?? []; // Pega os IDs dos opcionais marcados

    if (empty($id) || empty($nome) || empty($descricao) || empty($preco) || empty($categoria)) {
        die("Erro: Todos os campos são obrigatórios.");
    }
    
    $pdo->beginTransaction();
    try {
        // 1. Atualiza os dados principais do produto
        $sql = "UPDATE produtos SET nome_produto = ?, descricao_produto = ?, preco_produto = ?, categoria = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $descricao, $preco, $categoria, $id]);

        // 2. Apaga todos os vínculos de opcionais existentes para este produto
        $stmt_delete_opcionais = $pdo->prepare("DELETE FROM produto_opcionais WHERE produto_id = ?");
        $stmt_delete_opcionais->execute([$id]);

        // 3. Re-insere os novos vínculos selecionados
        if (!empty($opcionais_selecionados)) {
            $sql_insert_opcionais = "INSERT INTO produto_opcionais (produto_id, opcional_id) VALUES (?, ?)";
            $stmt_insert_opcionais = $pdo->prepare($sql_insert_opcionais);
            foreach ($opcionais_selecionados as $opcional_id) {
                $stmt_insert_opcionais->execute([$id, $opcional_id]);
            }
        }
        
        // 4. Lógica de Upload de Imagem (semelhante ao que já existia)
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $stmt_select = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
            $stmt_select->execute([$id]);
            $produto_antigo = $stmt_select->fetch(PDO::FETCH_ASSOC);
            $diretorio_upload = __DIR__ . '/../assets/images/';

            $imagem = $_FILES['imagem'];
            $nome_imagem_db = uniqid() . '_' . basename($imagem['name']);
            $caminho_completo = $diretorio_upload . $nome_imagem_db;

            if (move_uploaded_file($imagem['tmp_name'], $caminho_completo)) {
                // Atualiza o nome da imagem no banco
                $stmt_update_img = $pdo->prepare("UPDATE produtos SET imagem = ? WHERE id = ?");
                $stmt_update_img->execute([$nome_imagem_db, $id]);
                // Apaga a imagem antiga se ela existir
                if ($produto_antigo && file_exists($diretorio_upload . $produto_antigo['imagem'])) {
                    unlink($diretorio_upload . $produto_antigo['imagem']);
                }
            }
        }
        
        $pdo->commit();
        header("Location: " . BASE_URL . "/admin/index.php?status=editado");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erro ao atualizar o produto: " . $e->getMessage());
    }
} else {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}
?>