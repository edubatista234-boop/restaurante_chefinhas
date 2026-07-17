<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'cozinheiro') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id   = intval($_POST['pedido_id']);
    $novo_status = $_POST['novo_status']; 

    
    
    $sql_info = "SELECT p.usuario_garcom_id, m.numero_mesa 
                 FROM pedidos p 
                 JOIN mesas m ON p.mesa_id = m.id 
                 WHERE p.id = :pedido_id LIMIT 1";
                 
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->execute([':pedido_id' => $pedido_id]);
    $pedido_dados = $stmt_info->fetch(PDO::FETCH_ASSOC);
    
 
    if ($pedido_dados) {
        $garcom_id   = $pedido_dados['usuario_garcom_id'];
        $numero_mesa = $pedido_dados['numero_mesa'];

        
        
        $sql_update = "UPDATE pedidos SET status_pedido = :novo_status WHERE id = :pedido_id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([
            ':novo_status' => $novo_status,
            ':pedido_id' => $pedido_id
        ]);


        if ($novo_status === 'entregue') {
            
            $sql_mesa_dispo = "UPDATE pedidos p SET status_pedido = 'entregue' WHERE id = :pedido_id";
            
            
            $sql_mesa = "UPDATE mesas SET status = 'disponivel' WHERE id = (SELECT mesa_id FROM pedidos WHERE id = :pedido_id)";
            $stmt_mesa = $conn->prepare($sql_mesa);
            $stmt_mesa->execute([':pedido_id' => $pedido_id]);
        }


        $mensagem = "O pedido da Mesa $numero_mesa mudou para: *{$novo_status}*";
        
        
        $sql_notificacao = "INSERT INTO notificacoes (pedido_id, garcom_id, mensagem) 
                            VALUES (:pedido_id, :garcom_id, :mensagem)";
        $stmt_notif = $conn->prepare($sql_notificacao);
        $stmt_notif->execute([
            ':pedido_id' => $pedido_id,
            ':garcom_id' => $garcom_id,
            ':mensagem' => $mensagem
        ]);
    }

    
    
    header("Location: ../cozinha/cozinha.php");
    exit;
} else {
    header("Location: ../cozinha/cozinha.php");
    exit;
}
?>