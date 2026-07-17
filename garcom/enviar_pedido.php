<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'garcom') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $nome_cliente      = trim($_POST['nome_cliente']);
    $mesa_id           = intval($_POST['mesa_id']);
    $forma_pagamento   = trim($_POST['forma_pagamento']);
    $garcom_id         = $_SESSION['usuario_id'];
    
    $itens_carrinho = json_decode($_POST['dados_itens'], true);

    if (empty($itens_carrinho)) {
        echo "<script>alert('Erro: O carrinho de pedidos está vazio.'); window.location.href='garcom.php';</script>";
        exit;
    }

    
    $conn->beginTransaction();

    try {
        
        $sql_busca_cliente = "SELECT id FROM clientes WHERE nome = :nome LIMIT 1";
        $stmt_busca = $conn->prepare($sql_busca_cliente);
        $stmt_busca->execute([':nome' => $nome_cliente]);
        $dados_cliente = $stmt_busca->fetch();

        if ($dados_cliente) {
            $cliente_id = $dados_cliente['id'];
        } else {
            $sql_cliente = "INSERT INTO clientes (nome) VALUES (:nome)";
            $stmt_cadastra_cli = $conn->prepare($sql_cliente);
            if (!$stmt_cadastra_cli->execute([':nome' => $nome_cliente])) {
                throw new Exception("Erro ao cadastrar o cliente.");
            }
            $cliente_id = $conn->lastInsertId();
        }

        
        $valor_total = 0;
        foreach ($itens_carrinho as $item) {
            $valor_total += ($item['preco'] * $item['quantidade']);
        }

        
        $sql_pedido = "INSERT INTO pedidos (mesa_id, usuario_garcom_id, cliente_id, status_pedido, forma_pagamento, valor_total) 
                       VALUES (:mesa_id, :garcom_id, :cliente_id, 'pendente', :forma_pagamento, :valor_total)";
        
        $stmt_pedido = $conn->prepare($sql_pedido);
        $exec_pedido = $stmt_pedido->execute([
            ':mesa_id'         => $mesa_id,
            ':garcom_id'       => $garcom_id,
            ':cliente_id'      => $cliente_id,
            ':forma_pagamento' => $forma_pagamento,
            ':valor_total'     => $valor_total
        ]);

        if (!$exec_pedido) {
            throw new Exception("Erro ao gerar o cabeçalho do pedido.");
        }
        $pedido_id = $conn->lastInsertId();

        
        $sql_checa_estoque = "SELECT nome_prato, estoque FROM cardapio WHERE id = :id FOR UPDATE";
        $stmt_checa_est    = $conn->prepare($sql_checa_estoque);

        $sql_item = "INSERT INTO itens_pedido (pedido_id, cardapio_id, quantidade, preco_unitario) 
                     VALUES (:pedido_id, :cardapio_id, :quantidade, :preco_unitario)";
        $stmt_item = $conn->prepare($sql_item);

        $sql_baixa_estoque = "UPDATE cardapio SET estoque = estoque - :quantidade WHERE id = :id";
        $stmt_baixa_est    = $conn->prepare($sql_baixa_estoque);

        
        foreach ($itens_carrinho as $item) {
            $cardapio_id     = intval($item['id']);
            $quantidade      = intval($item['quantidade']);
            $preco_unitario  = floatval($item['preco']);

            
            $stmt_checa_est->execute([':id' => $cardapio_id]);
            $produto = $stmt_checa_est->fetch();

            if ($produto) {
                if ($produto['estoque'] < $quantidade) {
                    throw new Exception("Estoque insuficiente para '" . $produto['nome_prato'] . "'. Restam apenas " . $produto['estoque'] . " unidades.");
                }
            } else {
                throw new Exception("Produto ID $cardapio_id não encontrado no cardápio.");
            }

            
            $exec_item = $stmt_item->execute([
                ':pedido_id'      => $pedido_id,
                ':cardapio_id'    => $cardapio_id,
                ':quantidade'     => $quantidade,
                ':preco_unitario' => $preco_unitario
            ]);
            
            if (!$exec_item) {
                throw new Exception("Erro ao inserir itens no pedido.");
            }

            
            $exec_baixa = $stmt_baixa_est->execute([
                ':quantidade' => $quantidade,
                ':id'         => $cardapio_id
            ]);

            if (!$exec_baixa) {
                throw new Exception("Erro ao atualizar o estoque do produto.");
            }
        }

        
        $sql_ocupar_mesa = "UPDATE mesas SET status_mesa = 'ocupada', cliente_atual = :cliente WHERE id = :mesa_id";
        $stmt_mesa = $conn->prepare($sql_ocupar_mesa);
        $stmt_mesa->execute([
            ':cliente' => $nome_cliente,
            ':mesa_id' => $mesa_id
        ]);

        
        $conn->commit();

        echo "<script>alert('Pedido enviado com sucesso para a Cozinha!'); window.location.href='garcom.php';</script>";
        exit;

    } catch (Exception $e) {
        
        $conn->rollBack();
        echo "<script>alert('Falha crítica ao enviar pedido: " . $e->getMessage() . "'); window.location.href='garcom.php';</script>";
        exit;
    }

} else {
    header("Location: garcom.php");
    exit;
}
?>