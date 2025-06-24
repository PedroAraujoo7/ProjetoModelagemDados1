<?php
session_start(); // Inicia a sessão para verificar o login.
if (isset($_SESSION['usuario'])) { // Verifica se o usuário já está logado.
    header("Location: painel.php"); // Redireciona para o painel do usuário logado.
    exit; // Encerra o script.
}

include_once 'includes/Usuario.php'; // Inclui a classe Usuario para realizar operações de login.

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica se o formulário foi enviado via método POST.
    $usuario = new Usuario(); // Cria uma nova instância da classe Usuario.
    $loginSuccess = $usuario->login($_POST['email'], $_POST['senha']); // Tenta autenticar o usuário usando os dados fornecidos.

    if ($loginSuccess) { // Se o login for bem-sucedido:
        header("Location: painel.php"); // Redireciona para o painel do usuário.
        exit; // Encerra o script.
    } else {
        echo "Credenciais inválidas!"; // Exibe uma mensagem de erro caso as credenciais estejam incorretas.
    }
}
?>
<!DOCTYPE html> <!-- Declaração do tipo de documento HTML5 -->
<html lang="pt-br"> <!-- Define o idioma da página como português do Brasil -->
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres como UTF-8 -->
    <title>Login</title> <!-- Título da página que aparece na aba do navegador -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Login</h2> <!-- Cabeçalho da página -->
    <form method="POST" action="">
        <!-- Formulário para login, enviando os dados para o próprio arquivo index.php via POST -->
        <input type="email" name="email" placeholder="Email" required> <!-- Campo para inserir o email -->
        <input type="password" name="senha" placeholder="Senha" required> <!-- Campo para inserir a senha -->
        <button type="submit">Login</button> <!-- Botão para enviar o formulário -->
    </form>
    <p>Não tem uma conta? <a href="cadastro.html">Cadastre-se</a></p>
</body>
</html>