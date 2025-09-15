<?php
session_start();
require_once 'conexao.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação CSRF.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;
    $senha_confirm = $_POST['confirmar_senha'];

    if (empty($nome) || empty($email) || empty($senha)) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=campos_vazios");
        exit();
    }
    if ($senha !== $senha_confirm) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=senhas_nao_coincidem");
        exit();
    }
    if (strlen($senha) < 8) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=senha_curta");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=email_invalido");
        exit();
    }

    try {
        $sql_check = "SELECT id FROM login WHERE email = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$email]);

        if ($stmt_check->rowCount() > 0) {
            header("Location: " . BASE_URL . "/cadastro.php?erro=email_existente");
            exit();
        }
        
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql_insert ="INSERT INTO login (nome, email, senha) VALUES (?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);

        if ($stmt_insert->execute([$nome, $email, $senha_hash])) {
            header("Location: " . BASE_URL . "/cadastro.php?status=sucesso");
            exit();
        } else {
            header("Location: " . BASE_URL . "/cadastro.php?erro=db_erro");
            exit();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: " . BASE_URL . "/cadastro.php?erro=db_erro");
        exit();
    }        
} else {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
?>