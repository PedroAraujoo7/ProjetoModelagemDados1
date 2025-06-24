<?php
// Define a classe Database para configurar a conexão com o banco de dados MySQL usando PDO.
class Database {
    private $host = "localhost"; // Endereço do servidor do banco de dados.
    private $db_name = "sistemausuarios"; // Nome do banco de dados.
    private $username = "root"; // Usuário do banco de dados.
    private $password = ""; // Senha do banco de dados.
    public $conn; // Declaração da variável que armazenará a conexão.

    public function getConnection() {
        // Função para criar e retornar a conexão com o banco de dados.
        $this->conn = null; // Inicializa a variável de conexão como nula.
        try {
            // Cria a conexão usando a classe PDO, passando o host, nome do banco, usuário e senha.
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8"); // Define o conjunto de caracteres como UTF-8.
        } catch (PDOException $exception) {
            // Exibe uma mensagem de erro caso a conexão falhe.
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn; // Retorna a conexão criada.
    }
}
?>