<?php
session_start(); // Inicia a sessão para verificar se o usuário está logado

// Redireciona para o login se não houver usuário autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// Inclui as classes necessárias para manipulação de usuários e admins
include_once 'includes/Usuario.php';
include_once 'includes/Admin.php';

// Verifica se o usuário logado é um administrador
if ($_SESSION['is_admin'] == 1) {
    $usuario = new Admin(); // Se for admin, instancia a classe Admin
} else {
    $usuario = new Usuario(); // Se não for, instancia a classe Usuario
}

// Carrega os dados do usuário logado a partir do ID da sessão
$usuario->carregarDados($_SESSION['usuario']);

// Exibe as informações do painel (mensagem de boas-vindas, nome, etc.)
echo $usuario->getPainel();

// Se for administrador e foi enviado um ID para deletar, executa a remoção
if ($_SESSION['is_admin'] == 1 && isset($_GET['delete_id'])) {
    $usuario->deletarUsuario($_GET['delete_id']); // Deleta o usuário com o ID especificado
    header("Location: painel.php"); // Recarrega a página após a exclusão
    exit;
}

// Exibição específica para administradores: lista completa de usuários
if ($usuario instanceof Admin) {
    $usuarios = $usuario->listarUsuarios(); // Recupera todos os usuários

    echo '<h3>Lista de Usuários:</h3>';
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Nome</th><th>Email</th><th>Imagem</th><th>Ações</th></tr>';

    // Loop para exibir cada usuário na tabela
    foreach ($usuarios as $u) {
        echo '<tr>';
        echo '<td>' . $u['id'] . '</td>';
        echo '<td>' . $u['nome'] . '</td>';
        echo '<td>' . $u['email'] . '</td>';
        echo '<td>';
        // Exibe imagem se houver, ou mensagem "Sem imagem"
        echo $u['imagem'] ? '<img src="uploads/' . $u['imagem'] . '" alt="Imagem de Perfil" width="50">' : 'Sem imagem';
        echo '</td>';
        echo '<td>';
        // Botão para deletar o usuário com confirmação
        echo '<a href="painel.php?delete_id=' . $u['id'] . '" onclick="return confirm(\'Tem certeza que deseja deletar este usuário?\');">Deletar</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    // Para usuários comuns, exibe eventos
    echo '<h3>Seus Eventos</h3>';
    echo '<a href="eventos.php?action=criar">Adicionar Evento</a>'; // Link para criar novo evento

    // Verifica se o método listarEventos existe (pode depender da classe)
    if (method_exists($usuario, 'listarEventos')) {
        $eventos = $usuario->listarEventos(); // Obtém os eventos do usuário
    } else {
        echo "Erro: Método listarEventos não existe na classe " . get_class($usuario);
    }

    // Loop para exibir cada evento do usuário com cor de fundo dependendo do status
    foreach ($eventos as $evento) {
        $corFundo = $evento['status'] ? '#e6f9e6' : '#ffe6e6'; // Verde para concluído, vermelho para pendente
        echo "<div style='background-color: $corFundo; padding: 10px; margin-bottom: 10px; border-radius: 5px;'>";
        echo "<strong>{$evento['titulo']}</strong> - {$evento['data_evento']}<br>{$evento['descricao']}";
        echo "<br><a href='eventos.php?action=concluir&id={$evento['id']}'>Concluir</a>";
        echo " | <a href='eventos.php?action=deletar&id={$evento['id']}'>Excluir</a>";
        echo "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link para o CSS externo -->
</head>
<body>
    <!-- Navegação do painel -->
    <a href="upload.php">Alterar Imagem de Perfil</a>
    <a href="logout.php">Sair</a>
</body>
</html>