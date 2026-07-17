<?php
session_start();
require_once '../config/conexao.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'cozinheiro') {
    header("Location: index.php");
    exit;
}


$sql = "SELECT p.id as pedido_id, p.status_pedido, p.data_horario, 
               m.numero_mesa, c.nome as nome_cliente, u.nome as nome_garcom
        FROM pedidos p
        JOIN mesas m ON p.mesa_id = m.id
        JOIN clientes c ON p.cliente_id = c.id
        JOIN usuarios u ON p.usuario_garcom_id = u.id
        WHERE p.status_pedido != 'entregue'
        ORDER BY p.data_horario ASC";

$resultado = $conn->query($sql);

$pedidos = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Cozinha - Restaurante </title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #e9e4e4; color: #000000; padding: 20px; }
        
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #75aff5; padding-bottom: 15px; }
        header h1 { color: #000000; }
        .btn-sair { background: #b82d2d; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; font-size: 14px; }
        
        .grid-pedidos { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        
       
        .card-pedido { background: #d479b1; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #0562a0; display: flex; flex-direction: column; justify-content: space-between; }
        .status-pendente { border-top-color: #ff0404; }
        .status-preparo { border-top-color: #3498db; }
        .status-finalizado { border-top-color: #2ecc71; }
        
        .meta-dados { font-size: 13px; color: #000000; margin-bottom: 10px; line-height: 1.5; }
        .meta-dados strong { color: #ffffff; }
        
        .itens-lista { background: #ffffff; padding: 10px; border-radius: 5px; margin: 15px 0; list-style: none; }
        .itens-lista li { padding: 5px 0; border-bottom: 1px solid #7a9fdb; font-size: 15px; }
        .itens-lista li:last-child { border: none; }
        
        .grupo-botoes { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; }
        .btn-status { border: none; padding: 8px; border-radius: 4px; font-weight: bold; cursor: pointer; color: #fff; font-size: 12px; transition: 0.2s; }
        
        .btn-pendente { background: #b80c0c; }
        .btn-preparo { background: #2980b9; }
        .btn-finalizado { background: #27ae60; }
        .btn-entregue { background: #1e2020; }
        .btn-status:hover { opacity: 0.8; }
        
        .ativo { outline: 2px solid #ffffff; box-shadow: 0 0 8px #fff; }
    </style>
</head>
<body>

<header>
    <h1>Cozinha - Pedidos Internos</h1>
    <div>
        <span style="margin-right: 15px;">Chef: <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong></span>
        <a href="/RESTAURANTE_CHEFINHAS/logout.php" class="btn-sair">Sair do Sistema</a>
    </div>
</header>

<div class="grid-pedidos">
    <?php if (count($pedidos) === 0): ?>
        <p style="color: #694444; font-style: italic;">Nenhum pedido pendente ou em preparo no momento...</p>
    <?php endif; ?>

    <?php foreach ($pedidos as $pedido): 
        
        $classe_status = 'status-pendente';
        if ($pedido['status_pedido'] == 'em preparo') $classe_status = 'status-preparo';
        if ($pedido['status_pedido'] == 'finalizado') $classe_status = 'status-finalizado';
    ?>
        <div class="card-pedido <?php echo $classe_status; ?>">
            <div>
                <div class="meta-dados">
                    <strong>Pedido #<?php echo htmlspecialchars($pedido['pedido_id']); ?></strong><br>
                    Garçom: <strong><?php echo htmlspecialchars($pedido['nome_garcom']); ?></strong><br>
                    Cliente: <strong><?php echo htmlspecialchars($pedido['nome_cliente']); ?></strong><br>
                    Mesa: <strong style="color: #ffffff; font-size: 16px;">Mesa <?php echo htmlspecialchars($pedido['numero_mesa']); ?></strong><br>
                    Hora: <?php echo date('H:i', strtotime($pedido['data_horario'])); ?>
                </div>

                <ul class="itens-lista">
                    <?php
                    $pid = $pedido['pedido_id'];
                    $itens_sql = "SELECT ip.quantidade, c.nome_prato 
                                  FROM itens_pedido ip 
                                  JOIN cardapio c ON ip.cardapio_id = c.id 
                                  WHERE ip.pedido_id = :pedido_id";
                    
                    
                    $itens_stmt = $conn->prepare($itens_sql);
                    $itens_stmt->execute([':pedido_id' => $pid]);
                    $itens = $itens_stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($itens as $item):
                    ?>
                        <li><strong><?php echo htmlspecialchars($item['quantidade']); ?>x</strong> <?php echo htmlspecialchars($item['nome_prato']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form action="../pedidos/atualizar_status.php" method="POST">
                <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($pedido['pedido_id']); ?>">
                <div class="grupo-botoes">
                    <button type="submit" name="novo_status" value="pendente" class="btn-status btn-pendente <?php echo ($pedido['status_pedido'] == 'pendente') ? 'ativo' : ''; ?>">Pendente</button>
                    <button type="submit" name="novo_status" value="em preparo" class="btn-status btn-preparo <?php echo ($pedido['status_pedido'] == 'em preparo') ? 'ativo' : ''; ?>">Em Preparo</button>
                    <button type="submit" name="novo_status" value="finalizado" class="btn-status btn-finalizado <?php echo ($pedido['status_pedido'] == 'finalizado') ? 'ativo' : ''; ?>">Finalizado</button>
                    <button type="submit" name="novo_status" value="entregue" class="btn-status btn-entregue">Entregue</button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>