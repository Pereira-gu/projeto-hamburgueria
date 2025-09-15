<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

// (Em um projeto real, aqui você integraria uma biblioteca de envio de e-mail como PHPMailer)

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    header('Location: ' . BASE_URL . '/esqueci_senha.php?erro=email_invalido');
    exit();
}

try {
    // Verifica se o e-mail existe na base de dados
    $stmt = $pdo->prepare("SELECT id FROM login WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        // Gera um token seguro e único
        $token = bin2hex(random_bytes(32));
        // Define a validade do token (ex: 1 hora)
        $expires = time() + 3600; 

        // Salva o token no banco de dados
        $stmt_insert = $pdo->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt_insert->execute([$email, $token, $expires]);

        // Monta o link de recuperação
        $link = BASE_URL . "/redefinir_senha.php?token=" . $token;

        // AQUI SERIA O CÓDIGO DE ENVIO DE E-MAIL
        // $assunto = "Recuperação de Senha - Saboroso Burger";
        // $corpo_email = "Olá! Clique no link a seguir para redefinir sua senha: " . $link;
        // mail($email, $assunto, $corpo_email);

        // Como não podemos enviar e-mail, vamos exibir o link para fins de teste
        echo "<h2>Link de Recuperação (Simulação)</h2>";
        echo "<p>Em um sistema real, o link abaixo seria enviado para o seu e-mail.</p>";
        echo "<p><strong>Clique aqui para redefinir:</strong> <a href='$link'>$link</a></p>";
        exit();
    }
    
    // Por segurança, mesmo que o e-mail não exista, damos uma resposta genérica.
    header('Location: ' . BASE_URL . '/esqueci_senha.php?status=email_enviado');
    exit();

} catch (PDOException $e) {
    error_log("Erro na solicitação de recuperação: " . $e->getMessage());
    header('Location: ' . BASE_URL . '/esqueci_senha.php?erro=db');
    exit();
}