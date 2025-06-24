<?php
session_start(); // Inicia a sessão para controlar e acessar dados do usuário logado

include_once 'includes/Usuario.php'; // Inclui a classe que manipula os dados e métodos do usuário

// Verifica se o usuário está logado; se não, redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php"); // Redireciona para login se não estiver autenticado
    exit; // Encerra a execução do script
}

// Cria um novo objeto do tipo Usuario
$usuario = new Usuario();

// Carrega os dados do usuário logado (possivelmente nome, email, ID, etc.)
$usuario->carregarDados($_SESSION['usuario']);

// Verifica se a ação foi enviada via GET, como ?action=criar
if (!isset($_GET['action'])) {
    echo "Erro: Nenhuma ação foi especificada."; // Exibe erro se nenhuma ação for informada
    exit;
}

$action = $_GET['action']; // Captura a ação da URL
$dataHoje = date('Y-m-d'); // Armazena a data atual (usada no input de data e validação)
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"> <!-- Suporte a caracteres UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Eventos</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Aplica estilo visual externo -->
</head>
<body>
    <div class="container">
        <h2>Eventos</h2>

        <?php
        // Controla o fluxo com base no valor de $action
        switch ($action) {
            case 'criar': // Se o usuário estiver criando um evento
                if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica se o formulário foi enviado
                    $titulo = $_POST['titulo'];
                    $data_evento = $_POST['data_evento'];
                    $descricao = $_POST['descricao'];

                    // Validação da data: não pode ser anterior à atual
                    if ($data_evento < date('Y-m-d')) {
                        echo "<div class='message error'>A data do evento não pode ser anterior à data atual.</div>";
                        exit;
                    }

                    // Tenta criar o evento e redireciona em caso de sucesso
                    if ($usuario->criarEvento($titulo, $data_evento, $descricao)) {
                        echo "<div class='message success'>Evento criado com sucesso!</div>";
                        header("Location: painel.php");
                        exit;
                    } else {
                        echo "<div class='message error'>Erro ao criar evento.</div>"; // Erro na criação
                    }

                } else {
                    // Exibe o formulário se ainda não enviado
                    echo '<form action="eventos.php?action=criar" method="POST">
                            <label for="titulo">Título:</label>
                            <input type="text" name="titulo" required><br>
                            
                            <label for="data_evento">Data:</label>
                            <input type="date" name="data_evento" min="' . $dataHoje . '" required><br>
                            
                            <label for="descricao">Descrição:</label>
                            <textarea name="descricao" required></textarea><br>
                            
                            <button type="submit">Criar Evento</button>
                          </form>';
                }
                break;

                        case 'listar': // Exibe todos os eventos cadastrados pelo usuário
                $eventos = $usuario->listarEventos(); // Busca todos os eventos do banco de dados

                echo "<h3>Lista de Eventos</h3>";
                foreach ($eventos as $evento) {
                    // Mostra o título, data e descrição de cada evento com estilo conforme o status
                    echo "<div class='evento " . ($evento['status'] ? "success" : "error") . "'>";
                    echo "<strong>{$evento['titulo']}</strong> - {$evento['data_evento']}<br>{$evento['descricao']}";
                    // Adiciona links para concluir ou excluir o evento
                    echo "<br><a href='eventos.php?action=concluir&id={$evento['id']}'>Concluir</a>";
                    echo " | <a href='eventos.php?action=deletar&id={$evento['id']}'>Excluir</a>";
                    echo "</div>";
                }
                break;
            case 'concluir': // Marca um evento como concluído
                if (isset($_GET['id'])) { // Verifica se o ID foi passado pela URL
                    if ($usuario->concluirEvento($_GET['id'])) {
                        echo "<div class='message success'>Evento concluído!</div>";
                        header("Location: painel.php"); // Redireciona para o painel após sucesso
                        exit;
                    } else {
                        echo "<div class='message error'>Erro ao concluir evento.</div>";
                    }
                }
                break;

            case 'deletar': // Exclui um evento
                if (isset($_GET['id'])) {
                    if ($usuario->deletarEvento($_GET['id'])) {
                        echo "<div class='message success'>Evento excluído!</div>";
                        header("Location: painel.php"); // Redireciona após exclusão
                        exit;
                    } else {
                        echo "<div class='message error'>Erro ao excluir evento.</div>";
                    }
                }
                break;

            default: // Caso a ação passada não seja reconhecida
                echo "<div class='message error'>Erro: Ação inválida.</div>";
        }
        ?>
    </div>
</body>
</html>