<?php
session_start(); // Inicia a sessão para poder destruí-la.
session_destroy(); // Destroi todos os dados da sessão.
header("Location: index.php"); // Redireciona o usuário para a página de login.
exit; // Encerra o script.
?>