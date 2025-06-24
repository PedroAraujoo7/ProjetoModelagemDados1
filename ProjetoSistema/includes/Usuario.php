<?php
// Inclui a conexão com o banco de dados
include_once 'Database.php';

class Usuario {
    // Conexão com o banco protegida (usada internamente pela classe)
    protected $conn;
    
    // Propriedades públicas do usuário
    public $nome;
    public $email;
    public $senha;
    public $imagem;
    public $is_admin = 0;

    // Construtor: estabelece conexão com o banco de dados ao instanciar a classe
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Salva os dados do usuário no banco
    public function salvar() {
        $query = "INSERT INTO usuarios SET nome=:nome, email=:email, senha=:senha, imagem=:imagem, is_admin=:is_admin";
        $stmt = $this->conn->prepare($query);

        // Vincula os valores recebidos às variáveis da query
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT); // Gera hash seguro da senha
        $stmt->bindParam(":senha", $senhaHash);
        $stmt->bindParam(":imagem", $this->imagem);
        $stmt->bindParam(":is_admin", $this->is_admin);

        return $stmt->execute(); // Executa o insert
    }

    // Realiza login a partir do e-mail e senha informados
    public function login($email, $senha) {
        $query = "SELECT * FROM usuarios WHERE email=:email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Verifica se encontrou um usuário com o e-mail fornecido
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verifica se a senha confere com a hash armazenada
            if (password_verify($senha, $row['senha'])) {
                session_start();
                $_SESSION['usuario'] = $row['id'];
                $_SESSION['is_admin'] = isset($row['is_admin']) ? (int)$row['is_admin'] : 0;
                return true;
            }
        }
        return false;
    }

    // Retorna HTML básico do painel do usuário
    public function getPainel() {
        return "<h3>Painel do Usuário</h3><p>Bem-vindo, {$this->nome}!</p>";
    }

    // Carrega os dados do usuário com base no ID
    public function carregarDados($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        // Se houver resultado, preenche os dados do objeto
        if ($stmt->rowCount() > 0) {
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->nome = $dados['nome'];
            $this->email = $dados['email'];
            $this->senha = $dados['senha']; // Atenção: senha já vem criptografada
            $this->imagem = $dados['imagem'];
            $this->is_admin = $dados['is_admin'];
        }
    }

    // Cria um novo evento associado ao usuário logado
    public function criarEvento($titulo, $data_evento, $descricao) {
        $usuario_id = $_SESSION['usuario'];

        $query = "INSERT INTO eventos (usuario_id, titulo, data_evento, descricao) 
                  VALUES (:usuario_id, :titulo, :data_evento, :descricao)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":data_evento", $data_evento);
        $stmt->bindParam(":descricao", $descricao);
        return $stmt->execute();
    }

    // Retorna todos os eventos do usuário logado
    public function listarEventos() {
        $usuario_id = $_SESSION['usuario'];

        $query = "SELECT * FROM eventos WHERE usuario_id = :usuario_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marca o evento como concluído
    public function concluirEvento($evento_id) {
        $usuario_id = $_SESSION['usuario'];

        $query = "UPDATE eventos SET status = TRUE WHERE id = :evento_id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        return $stmt->execute();
    }

    // Deleta um evento do usuário
    public function deletarEvento($evento_id) {
        $usuario_id = $_SESSION['usuario'];

        $query = "DELETE FROM eventos WHERE id = :evento_id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        return $stmt->execute();
    }
}
?>