<?php
session_start(); // Inicia a sessão para garantir que o usuário está logado

// Se o usuário não estiver logado, redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include_once 'includes/Usuario.php'; // Inclui a classe Usuario

$usuario = new Usuario(); // Instancia um novo objeto Usuario
$usuario->carregarDados($_SESSION['usuario']); // Carrega os dados do usuário logado

$mensagem = ""; // Variável para armazenar mensagens de sucesso ou erro do upload

// Verifica se o formulário foi enviado e se o campo de imagem está presente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    $targetDir = "uploads/"; // Diretório de destino para o upload
    $fileName = basename($_FILES['imagem']['name']); // Obtém o nome base do arquivo
    $targetFilePath = $targetDir . $fileName; // Caminho final do arquivo

    // Move o arquivo temporário para o diretório de destino
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFilePath)) {
        $usuario->imagem = $fileName; // Atualiza o nome da imagem no objeto usuário
        $mensagem = '<div class="message success">Imagem enviada com sucesso.</div>';
    } else {
        $mensagem = '<div class="message error">Erro ao enviar a imagem.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload de Imagem</title>
    <!-- Inclui o arquivo de estilos do projeto -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Título da página -->
        <h2>Envio de Imagem de Perfil</h2>

        <!-- Exibe a mensagem de sucesso ou erro, se houver -->
        <?= $mensagem ?>

        <!-- Formulário para envio da imagem -->
        <form method="POST" enctype="multipart/form-data">
            <!-- Campo para selecionar o arquivo -->
            <label for="imagem">Selecione uma imagem:</label>
            <input type="file" name="imagem" id="imagem" required>

            <!-- Botão para envio -->
            <button type="submit">Enviar Imagem</button>
        </form>

        <!-- Link para voltar ao painel do usuário -->
        <p><a href="painel.php">← Voltar para o Painel</a></p>
    </div>
</body>
</html>