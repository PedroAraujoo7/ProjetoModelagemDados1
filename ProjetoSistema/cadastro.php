<?php
// Inclui o arquivo responsável pela conexão com o banco de dados
include_once 'includes/Database.php';

// Inclui a classe que representa um usuário do sistema
include_once 'includes/Usuario.php';

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cria um novo objeto do tipo Usuario
    $usuario = new Usuario();

    // Define as propriedades do usuário com os dados recebidos do formulário
    $usuario->nome = $_POST['nome'];
    $usuario->email = $_POST['email'];
    $usuario->senha = $_POST['senha'];
    $usuario->is_admin = isset($_POST['is_admin']) ? 1 : 0; // Admin = 1, comum = 0

    // Verifica se uma imagem foi enviada no formulário
    if (!empty($_FILES['imagem']['name'])) {
        $targetDir = "uploads/"; // Pasta onde as imagens são armazenadas
        $fileName = basename($_FILES['imagem']['name']); // Nome do arquivo de imagem
        $targetFilePath = $targetDir . $fileName; // Caminho completo do destino do arquivo

        // Move o arquivo enviado para a pasta de uploads
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFilePath)) {
            $usuario->imagem = $fileName; // Atribui o nome da imagem ao usuário
        } else {
            // Se não conseguir mover a imagem, exibe erro e encerra
            echo "Erro ao fazer upload da imagem.";
            exit;
        }
    }

    // Tenta salvar o usuário no banco de dados
    if ($usuario->salvar()) {
        // Redireciona para a página de sucesso após cadastro bem-sucedido
        header("Location: sucesso.php");
        exit;
    } else {
        // Em caso de erro no cadastro, exibe mensagem com estilo
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Erro no Cadastro</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="container">
                <h2 class="message error">Erro ao cadastrar usuário. Verifique os dados e tente novamente.</h2>
                <p><a href="cadastro.html">Voltar para o cadastro</a></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>