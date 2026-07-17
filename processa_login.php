<?php

session_set_cookie_params([
    'path' => '/',
    'httponly' => true
]);
session_start();
require_once 'config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $cargo = trim($_POST['cargo']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha']; 

    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND cargo = :cargo LIMIT 1";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':email' => $email,
            ':cargo' => $cargo
        ]);

        $usuario = $stmt->fetch();

        if ($usuario) {
            
            if (password_verify($senha, $usuario['senha'])) {
                
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['cargo']        = $usuario['cargo'];

                
                if ($usuario['cargo'] == 'administrador') {
                    header("Location: admin/admin.php");
                } elseif ($usuario['cargo'] == 'garcom') {
                    header("Location: garcom/garcom.php");
                } elseif ($usuario['cargo'] == 'cozinheiro') {
                    header("Location: cozinha/cozinha.php");
                }
                exit;
                
            } else {
                header("Location: index.php?erro=1");
                exit;
            }
        } else {
            header("Location: index.php?erro=1");
            exit;
        }

    } catch (PDOException $e) {
        die("Erro no banco de dados: " . $e->getMessage());
    }

} else {
    header("Location: index.php");
    exit;
}
?>