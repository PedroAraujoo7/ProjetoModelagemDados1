<?php
// Inclui a classe base 'Usuario', da qual Admin irá herdar
include_once 'Usuario.php';

// A classe Admin estende os comportamentos da classe Usuario
class Admin extends Usuario {

    // Sobrescreve o método getPainel para exibir uma mensagem personalizada para administradores
    public function getPainel() {
        return "<h3>Painel do Administrador</h3><p>Olá! Aqui você pode gerenciar todos os usuários.</p>";
    }

    // Retorna uma lista de todos os usuários do sistema
    public function listarUsuarios() {
        // Consulta o banco de dados para obter id, nome, email e imagem dos usuários
        $query = "SELECT id, nome, email, imagem FROM usuarios"; // A coluna 'genero' foi removida aqui propositalmente
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna os dados como um array associativo
    }

    // Deleta um usuário com base no ID fornecido
    public function deletarUsuario($id) {
        $query = "DELETE FROM usuarios WHERE id=:id"; // Define o comando SQL
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id); // Substitui o parâmetro :id pelo valor recebido
        $resultado = $stmt->execute(); // Executa o comando

        // Verifica se o usuário deletado é o mesmo da sessão atual
        if ($resultado && isset($_SESSION['usuario']) && $_SESSION['usuario'] == $id) {
            // Encerra a sessão e redireciona para a página de login
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit;
        }

        return $resultado; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
    }
}
?>