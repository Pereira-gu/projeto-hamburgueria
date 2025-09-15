<?php
session_start();
require_once 'conexao.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação CSRF.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if (empty($email) || empty($senha)) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=campos_vazios_login");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: " . BASE_URL . "/cadastro.php?erro=email_invalido");
        exit();
    }

    try {
        $sql_check = "SELECT id, nome, senha, is_admin FROM login WHERE email = ?";
        $stmt = $pdo->prepare($sql_check);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['is_admin'] = (bool)$usuario['is_admin'];
            header("Location: " . BASE_URL . "/index.php");
            exit();
        } else {
            header("Location: " . BASE_URL . "/cadastro.php?erro=login_invalido");
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