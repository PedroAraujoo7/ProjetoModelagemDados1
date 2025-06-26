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

$mensagem = ""; // Variável para mensagens de sucesso ou erro

// Verifica se o formulário foi enviado e se o campo de imagem está presente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    $targetDir = "uploads/"; // Diretório de destino
    $fileName = basename($_FILES['imagem']['name']);
    $targetFilePath = $targetDir . $fileName;

    // Move o arquivo e atualiza o banco
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFilePath)) {
        $usuario->imagem = $fileName;

        // Verifica se existe o método atualizarImagem
        if (method_exists($usuario, 'atualizarImagem') && $usuario->atualizarImagem()) {
            $mensagem = '<div class="message success">Imagem enviada e atualizada com sucesso.</div>';
        } else {
            $mensagem = '<div class="message error">Erro ao atualizar a imagem no banco de dados.</div>';
        }
    } else {
        $mensagem = '<div class="message error">Erro ao fazer upload do arquivo.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload de Imagem</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Envio de Imagem de Perfil</h2>

        <?= $mensagem ?>

        <?php if (!empty($usuario->imagem)): ?>
            <p>Imagem atual:</p>
            <img src="uploads/<?= htmlspecialchars($usuario->imagem) ?>" alt="Imagem atual" width="120"><br><br>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="imagem">Selecione uma nova imagem:</label>
            <input type="file" name="imagem" id="imagem" required>
            <button type="submit">Enviar Imagem</button>
        </form>

        <p><a href="painel.php">← Voltar para o Painel</a></p>
    </div>
</body>
</html>