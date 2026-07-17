<?php

session_set_cookie_params([
    'path' => '/',
    'httponly' => true
]);
session_start();


date_default_timezone_set('America/Sao_Paulo');  
require_once '../config/conexao.php';


$conn->query("SET time_zone = '-03:00'"); 

if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

$edit_prato = null;
$edit_func = null;
$edit_despesa = null;
$edit_mesa = null;
$edit_cliente = null; 

if (isset($_GET['editar_prato'])) {
    $id = intval($_GET['editar_prato']);
    $stmt = $conn->prepare("SELECT * FROM cardapio WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_prato = $stmt->fetch();
}
if (isset($_GET['editar_funcionario'])) {
    $id = intval($_GET['editar_funcionario']);
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_func = $stmt->fetch();
}
if (isset($_GET['editar_despesa'])) {
    $id = intval($_GET['editar_despesa']);
    $stmt = $conn->prepare("SELECT * FROM despesas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_despesa = $stmt->fetch();
}
if (isset($_GET['editar_mesa'])) {
    $id = intval($_GET['editar_mesa']);
    $stmt = $conn->prepare("SELECT * FROM mesas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_mesa = $stmt->fetch();
}
if (isset($_GET['editar_cliente'])) {
    $id = intval($_GET['editar_cliente']);
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_cliente = $stmt->fetch();
}


if (isset($_GET['desativar_prato'])) {
    $id = intval($_GET['desativar_prato']);
    $stmt = $conn->prepare("UPDATE cardapio SET status = 'inativo' WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}
if (isset($_GET['ativar_prato'])) {
    $id = intval($_GET['ativar_prato']);
    $stmt = $conn->prepare("UPDATE cardapio SET status = 'ativo' WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}

if (isset($_GET['excluir_prato'])) {
    $id = intval($_GET['excluir_prato']);
    $stmt = $conn->prepare("DELETE FROM cardapio WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}
if (isset($_GET['excluir_funcionario'])) {
    $id = intval($_GET['excluir_funcionario']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}
if (isset($_GET['excluir_despesa'])) {
    $id = intval($_GET['excluir_despesa']);
    $stmt = $conn->prepare("DELETE FROM despesas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}
if (isset($_GET['excluir_mesa'])) {
    $id = intval($_GET['excluir_mesa']);
    $stmt = $conn->prepare("DELETE FROM mesas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}
if (isset($_GET['excluir_cliente'])) {
    $id = intval($_GET['excluir_cliente']);
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php"); exit;
}


if (isset($_POST['zerar_caixa'])) {
    $data_hoje = date('Y-m-d');
    $sql_faturamento = "SELECT SUM(valor_total) as total FROM pedidos WHERE DATE(data_horario) = :data_hoje AND status_pedido = 'entregue'";
    $stmt_fat = $conn->prepare($sql_faturamento);
    $stmt_fat->execute([':data_hoje' => $data_hoje]);
    $res_fat = $stmt_fat->fetch();
    $faturamento_dia = $res_fat['total'] ?? 0.00;

    $sql_fechar_caixa = "INSERT INTO caixa_diario (data_dia, valor_abertura, valor_fechamento, status_caixa) 
                         VALUES (:data_dia, 0.00, :fechamento, 'fechado') 
                         ON DUPLICATE KEY UPDATE valor_fechamento = :fechamento_dup, status_caixa = 'fechado'";
    $stmt_fechar = $conn->prepare($sql_fechar_caixa);
    $stmt_fechar->execute([
        ':data_dia' => $data_hoje,
        ':fechamento' => $faturamento_dia,
        ':fechamento_dup' => $faturamento_dia
    ]);
    
    echo "<script>alert('Caixa do dia encerrado e zerado com sucesso!'); window.location.href='admin.php';</script>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    if ($_POST['acao'] === 'cadastrar_prato') {
        $nome = trim($_POST['nome_prato']);
        $desc = trim($_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $cat = trim($_POST['categoria']);
        $status = trim($_POST['status'] ?? 'ativo');
        $estoque = intval($_POST['estoque']);
        
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE cardapio SET nome_prato=:nome, descricao=:desc, preco=:preco, categoria=:cat, status=:status, estoque=:estoque WHERE id=:id");
            $stmt->execute([
                ':nome' => $nome, ':desc' => $desc, ':preco' => $preco, 
                ':cat' => $cat, ':status' => $status, ':estoque' => $estoque, ':id' => $id
            ]);
        } else {
            $stmt = $conn->prepare("INSERT INTO cardapio (nome_prato, descricao, preco, categoria, status, estoque) VALUES (:nome, :desc, :preco, :cat, :status, :estoque)");
            $stmt->execute([
                ':nome' => $nome, ':desc' => $desc, ':preco' => $preco, 
                ':cat' => $cat, ':status' => $status, ':estoque' => $estoque
            ]);
        }
    }
    
    if ($_POST['acao'] === 'cadastrar_funcionario') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);
        $cargo = trim($_POST['cargo']);
        
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            
            if (!empty($senha)) {
                $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET nome=:nome, email=:email, senha=:senha, cargo=:cargo WHERE id=:id");
                $stmt->execute([
                    ':nome' => $nome, 
                    ':email' => $email, 
                    ':senha' => $senha_criptografada, 
                    ':cargo' => $cargo, 
                    ':id' => $id
                ]);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nome=:nome, email=:email, cargo=:cargo WHERE id=:id");
                $stmt->execute([
                    ':nome' => $nome, 
                    ':email' => $email, 
                    ':cargo' => $cargo, 
                    ':id' => $id
                ]);
            }
        } else {
            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cargo) VALUES (:nome, :email, :senha, :cargo)");
            $stmt->execute([
                ':nome' => $nome, 
                ':email' => $email, 
                ':senha' => $senha_criptografada, 
                ':cargo' => $cargo
            ]);
        }
    }
    
    if ($_POST['acao'] === 'cadastrar_despesa') {
        $desc = trim($_POST['descricao']);
        $valor = floatval($_POST['valor']);
        $data = trim($_POST['data_despesa']);
        $cat = trim($_POST['categoria']);
        
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE despesas SET descricao=:desc, valor=:valor, data_despesa=:data, categoria=:cat WHERE id=:id");
            $stmt->execute([':desc' => $desc, ':valor' => $valor, ':data' => $data, ':cat' => $cat, ':id' => $id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO despesas (descricao, valor, data_despesa, categoria) VALUES (:desc, :valor, :data, :cat)");
            $stmt->execute([':desc' => $desc, ':valor' => $valor, ':data' => $data, ':cat' => $cat]);
        }
    }
    
    if ($_POST['acao'] === 'cadastrar_mesa') {
        $num = intval($_POST['numero_mesa']);
        
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE mesas SET numero_mesa=:num WHERE id=:id");
            $stmt->execute([':num' => $num, ':id' => $id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO mesas (numero_mesa) VALUES (:num)");
            $stmt->execute([':num' => $num]);
        }
    }

    if ($_POST['acao'] === 'cadastrar_cliente') {
        $nome = trim($_POST['nome_cliente']);
        $telefone = trim($_POST['telefone_cliente']);
        
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE clientes SET nome=:nome, telefone=:telefone WHERE id=:id");
            $stmt->execute([':nome' => $nome, ':telefone' => $telefone, ':id' => $id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO clientes (nome, telefone) VALUES (:nome, :telefone)");
            $stmt->execute([':nome' => $nome, ':telefone' => $telefone]);
        }
    }

    header("Location: admin.php");
    exit;
}


$data_inicio = $_GET['data_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$data_fim = $_GET['data_fim'] ?? date('Y-m-d');


$stmt_mesas = $conn->prepare("SELECT COUNT(DISTINCT id) as total_mesas FROM pedidos WHERE DATE(data_horario) BETWEEN :inicio AND :fim");
$stmt_mesas->execute(['inicio' => $data_inicio, 'fim' => $data_fim]);
$total_mesas_periodo = $stmt_mesas->fetchColumn() ?: 0;

$stmt_faturamento = $conn->prepare("SELECT COUNT(id) as total_pedidos, SUM(valor_total) as faturamento FROM pedidos WHERE DATE(data_horario) BETWEEN :inicio AND :fim");
$stmt_faturamento->execute(['inicio' => $data_inicio, 'fim' => $data_fim]);
$dados_faturamento = $stmt_faturamento->fetch();
$total_pedidos_periodo = $dados_faturamento['total_pedidos'] ?? 0;
$faturamento_periodo = $dados_faturamento['faturamento'] ?? 0;

try {
    $stmt_itens = $conn->prepare("
        SELECT c.nome_prato, SUM(ip.quantidade) as total_vendido 
        FROM itens_pedido ip
        JOIN cardapio c ON ip.cardapio_id = c.id
        JOIN pedidos ped ON ip.pedido_id = ped.id
        WHERE DATE(ped.data_horario) BETWEEN :inicio AND :fim
        GROUP BY ip.cardapio_id 
        ORDER BY total_vendido DESC 
        LIMIT 5
    ");
    $stmt_itens->execute(['inicio' => $data_inicio, 'fim' => $data_fim]);
    $itens_mais_vendidos = $stmt_itens->fetchAll();
} catch (PDOException $e) {
    
    $itens_mais_vendidos = [];
}


$stmt_pagamento = $conn->prepare("
    SELECT forma_pagamento, COUNT(*) as total 
    FROM pedidos 
    WHERE DATE(data_horario) BETWEEN :inicio AND :fim AND forma_pagamento IS NOT NULL AND forma_pagamento != ''
    GROUP BY forma_pagamento 
    ORDER BY total DESC 
    LIMIT 1
");
$stmt_pagamento->execute(['inicio' => $data_inicio, 'fim' => $data_fim]);
$forma_comum = $stmt_pagamento->fetch();


try {
    $stmt_garcom = $conn->prepare("
        SELECT u.nome as garcom, SUM(p.valor_total) as total_vendas 
        FROM pedidos p
        JOIN usuarios u ON p.usuario_garcom_id = u.id
        WHERE DATE(p.data_horario) BETWEEN :inicio AND :fim
        GROUP BY p.usuario_garcom_id 
        ORDER BY total_vendas DESC 
        LIMIT 1
    ");
    $stmt_garcom->execute(['inicio' => $data_inicio, 'fim' => $data_fim]);
    $melhor_garcom = $stmt_garcom->fetch();
} catch (Exception $e) {
    $melhor_garcom = false;
}


$data_hoje = date('Y-m-d');

$stmt_caixa = $conn->prepare("SELECT SUM(valor_total) as total FROM pedidos WHERE DATE(data_horario) = :data_hoje AND status_pedido = 'entregue'");
$stmt_caixa->execute([':data_hoje' => $data_hoje]);
$res_caixa_hoje = $stmt_caixa->fetch();
$faturamento_hoje = $res_caixa_hoje['total'] ?? 0.00;

$stmt_status_caixa = $conn->prepare("SELECT status_caixa FROM caixa_diario WHERE data_dia = :data_hoje");
$stmt_status_caixa->execute([':data_hoje' => $data_hoje]);
$res_status_caixa = $stmt_status_caixa->fetch();
$caixa_zerado = ($res_status_caixa['status_caixa'] ?? 'aberto') === 'fechado';
if ($caixa_zerado) { $faturamento_hoje = 0.00; }

$sql_logs = "SELECT p.id, u.nome as garcom, p.data_horario, c.nome as cliente, p.valor_total, p.forma_pagamento 
             FROM pedidos p 
             JOIN usuarios u ON p.usuario_garcom_id = u.id 
             JOIN clientes c ON p.cliente_id = c.id
             WHERE p.status_pedido = 'entregue' ORDER BY p.data_horario DESC LIMIT 10";
$logs_resultado = $conn->query($sql_logs);

$cliente_mais = $conn->query("SELECT c.nome, SUM(p.valor_total) as total FROM pedidos p JOIN clientes c ON p.cliente_id = c.id GROUP BY p.cliente_id ORDER BY total DESC LIMIT 1")->fetch();
$cliente_menos = $conn->query("SELECT c.nome, SUM(p.valor_total) as total FROM pedidos p JOIN clientes c ON p.cliente_id = c.id GROUP BY p.cliente_id ORDER BY total ASC LIMIT 1")->fetch();

$planilha_mensal = $conn->query("
    SELECT 
        DATE_FORMAT(p.data_horario, '%m/%Y') as mes,
        SUM(p.valor_total) as faturamento,
        (SELECT SUM(d.valor) FROM despesas d WHERE DATE_FORMAT(d.data_despesa, '%m/%Y') = DATE_FORMAT(p.data_horario, '%m/%Y')) as despesa_total
    FROM pedidos p
    WHERE p.status_pedido = 'entregue'
    GROUP BY DATE_FORMAT(p.data_horario, '%m/%Y')
    ORDER BY p.data_horario DESC
");

$lista_cardapio = $conn->query("SELECT * FROM cardapio ORDER BY categoria, nome_prato ASC");
$lista_funcionarios = $conn->query("SELECT * FROM usuarios WHERE cargo != 'administrador' ORDER BY nome ASC");
$lista_mesas = $conn->query("SELECT * FROM mesas ORDER BY numero_mesa ASC");
$lista_clientes = $conn->query("SELECT * FROM clientes ORDER BY nome ASC"); 
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Restaurante das Chefinhas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
        
        .topbar { background: #00e1ff; color: white; padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #ffffff; }
        .topbar h2 { color: #000000; font-size: 20px; }
        .topbar h2 span { font-size: 12px; color: #000000; display: block; margin-top: -2px; }
        
        .nav-tabs { display: flex; background: #ba43df; padding: 5px 20px 0 20px; border-bottom: 1px solid rgba(153, 58, 58, 0.1); }
        .menu-btn { background: none; border: none; color: #000000; padding: 12px 20px; font-size: 15px; cursor: pointer; border-bottom: 3px solid transparent; transition: 0.2s; margin-right: 10px; }
        .menu-btn:hover { color: #000000; }
        .menu-btn.active { color: #000000; border-bottom-color: #000000; font-weight: bold; }
        
        .btn-sair { background: #f02912; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; transition: 0.2s; }
        .btn-sair:hover { background: #df2814; }

        .content { flex: 1; padding: 30px; max-width: 1400px; margin: 0 auto; width: 100%; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        h3 { color: #1a1a2e; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 2px solid #ddd; }
        
        .grid-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #e2b659; }
        .card h4 { font-size: 14px; color: #777; text-transform: uppercase; margin-bottom: 5px; }
        .card p { font-size: 22px; font-weight: bold; color: #1a1a2e; }

        .form-cadastro { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 13px; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn-salvar { background: #3bbe3b; color: white; border: none; padding: 11px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-salvar:hover { background: #e2b659; color: #1a1a2e; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 10px; margin-bottom: 30px; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        table th { background: #f8f9fa; color: #555; font-weight: 600; }
        
        .btn-zerar { background: #3bbe3b; color: white; border: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; }
        
        .btn-editar { background: #2980b9; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; }
        .btn-editar:hover { background: #1f618d; }
        .btn-excluir { background: #c0392b; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-excluir:hover { background: #922b21; }
    </style>
</head>
<body>

<div class="topbar">
    <h2>Sara <span>Painel Administrativo da Gestão</span></h2>
    <a href="/RESTAURANTE_CHEFINHAS/logout.php" class="btn-sair">Sair do Sistema</a>
</div>

<div class="nav-tabs">
    <button class="menu-btn active" id="tab-btn-dashboard" onclick="switchTab('dashboard')">Visão Geral & Caixa</button>
    <button class="menu-btn" id="tab-btn-cardapio" onclick="switchTab('cardapio')">Gerenciar Cardápio</button>
    <button class="menu-btn" id="tab-btn-funcionarios" onclick="switchTab('funcionarios')">Gerenciar Funcionários</button>
    <button class="menu-btn" id="tab-btn-mesas" onclick="switchTab('mesas')">Gerenciar Mesas</button>
    <button class="menu-btn" id="tab-btn-clientes" onclick="switchTab('clientes')">Gerenciar Clientes</button>
    <button class="menu-btn" id="tab-btn-financeiro" onclick="switchTab('financeiro')">Planilhas Mensais & Custos</button>
</div>

<div class="content">

    <div id="dashboard" class="tab-content active">
        <h3>Fluxo de Caixa & Logs Operacionais</h3>
        <div class="grid-cards">
            <div class="card" style="border-left-color: #27ae60;">
                <h4>Faturamento em Caixa (Hoje)</h4>
                <p>R$ <?php echo number_format($faturamento_hoje, 2, ',', '.'); ?></p>
            </div>
            <div class="card" style="border-left-color: #211dff;">
                <h4>Cliente que Mais Gastou</h4>
                <p style="font-size:16px;"><?php echo htmlspecialchars($cliente_mais['nome'] ?? 'Nenhum'); ?> (R$ <?php echo number_format($cliente_mais['total'] ?? 0, 2, ',', '.'); ?>)</p>
            </div>
            <div class="card" style="border-left-color: #b93329;">
                <h4>Cliente que Menos Gastou</h4>
                <p style="font-size:16px;"><?php echo htmlspecialchars($cliente_menos['nome'] ?? 'Nenhum'); ?> (R$ <?php echo number_format($cliente_menos['total'] ?? 0, 2, ',', '.'); ?>)</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 25px; border-left-color: #3498db; background: #fff;">
            <h4 style="margin-bottom: 12px; color: #3498db;">Filtrar Relatório do Dashboard</h4>
            <form method="GET" action="admin.php" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #555;">Data Inicial</label>
                    <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" required style="padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #555;">Data Final</label>
                    <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" required style="padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <button type="submit" class="btn-salvar" style="padding: 8px 15px; width: auto; background: #3498db;">Filtrar Período</button>
            </form>
        </div>

        <h4 style="margin: 25px 0 10px 0; color: #333; text-transform: uppercase; font-size: 13px;">Indicadores de <?php echo date('d/m/Y', strtotime($data_inicio)); ?> até <?php echo date('d/m/Y', strtotime($data_fim)); ?></h4>
        <div class="grid-cards" style="margin-bottom: 30px;">
            <div class="card" style="border-left-color: #3498db;">
                <h4>Atendimentos / Pedidos</h4>
                <p><?php echo $total_mesas_periodo; ?></p>
            </div>
            <div class="card" style="border-left-color: #9b59b6;">
                <h4>Faturamento no Período</h4>
                <p>R$ <?php echo number_format($faturamento_periodo, 2, ',', '.'); ?></p>
            </div>
            <div class="card" style="border-left-color: #f1c40f;">
                <h4>Pagamento Preferido</h4>
                <p style="font-size:16px;">
                    <?php 
                    if ($forma_comum) {
                        $fp = $forma_comum['forma_pagamento'];
                        if ($fp == 'cartao_credito') echo 'Crédito';
                        elseif ($fp == 'cartao_debito') echo 'Débito';
                        elseif ($fp == 'dinheiro') echo 'Dinheiro';
                        elseif ($fp == 'pix') echo 'PIX';
                        else echo htmlspecialchars(ucfirst($fp));
                        echo " (" . $forma_comum['total'] . "x)";
                    } else {
                        echo "Sem dados";
                    }
                    ?>
                </p>
            </div>
            <div class="card" style="border-left-color: #e67e22;">
                <h4>Melhor Garçom</h4>
                <p style="font-size:16px;">
                    <?php if ($melhor_garcom): ?>
                        <?php echo htmlspecialchars($melhor_garcom['garcom']); ?> <br>
                        <span style="font-size: 12px; color: #27ae60;">(R$ <?php echo number_format($melhor_garcom['total_vendas'], 2, ',', '.'); ?>)</span>
                    <?php else: ?>
                        Nenhum no período
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div style="margin-bottom: 35px;">
            <h4 style="margin-bottom: 10px; color: #333; text-transform: uppercase; font-size: 13px;">Produtos Mais Vendidos no Período</h4>
            <table>
                <thead>
                    <tr>
                        <th>Nome do Prato/Produto</th>
                        <th style="text-align: right;">Quantidade Vendida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itens_mais_vendidos)): ?>
                        <?php foreach ($itens_mais_vendidos as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['nome_prato']); ?></strong></td>
                                <td style="text-align: right; font-weight: bold; color: #2980b9;"><?php echo $item['total_vendido']; ?> un</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="color: #999; text-align: center; padding: 15px;">Nenhum detalhe de itens vendidos para exibir.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-bottom:30px;">
            <form action="admin.php" method="POST">
                <button type="submit" name="zerar_caixa" class="btn-zerar" onclick="return confirm('Deseja realmente fechar o dia e zerar o caixa operacional?')">Finalizar Dia (Zerar Caixa)</button>
            </form>
        </div>

        <h4>Avisos em Tempo Real: Garçons que Finalizaram Pedidos</h4>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Garçom</th>
                    <th>Cliente</th>
                    <th>Data/Hora</th>
                    <th>Valor Pago</th>
                    <th>Forma de Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs_resultado->fetch()): ?>
                    <tr>
                        <td>#<?php echo $log['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($log['garcom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($log['cliente']); ?></td>
                        <td><?php echo date('d/m/H:i', strtotime($log['data_horario'])); ?></td>
                        <td style="color:#27ae60; font-weight:bold;">R$ <?php echo number_format($log['valor_total'], 2, ',', '.'); ?></td>
                        
                        <td>
                            <?php 
                            if (isset($log['forma_pagamento'])) {
                                $fp = $log['forma_pagamento'];
                                if ($fp == 'cartao_credito') echo 'Cartão de Crédito';
                                elseif ($fp == 'cartao_debito') echo 'Cartão de Débito';
                                elseif ($fp == 'dinheiro') echo 'Dinheiro';
                                elseif ($fp == 'pix') echo 'PIX';
                                else echo htmlspecialchars(ucfirst($fp)); 
                            } else {
                                echo '<span style="color:#999;">Não informado</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="cardapio" class="tab-content">
        <h3><?php echo $edit_prato ? 'Editar Prato' : 'Controle do Cardápio Chique'; ?></h3>
        <form class="form-cadastro" action="admin.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar_prato">
            <input type="hidden" name="id" value="<?php echo $edit_prato['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Nome do Prato/Produto</label>
                <input type="text" name="nome_prato" required value="<?php echo htmlspecialchars($edit_prato['nome_prato'] ?? ''); ?>" placeholder="Ex: Risoto de Alho Poró">
            </div>
            <div class="form-group">
                <label>Descrição Gastronômica</label>
                <input type="text" name="descricao" required value="<?php echo htmlspecialchars($edit_prato['descricao'] ?? ''); ?>" placeholder="Ex: Arroz arbóreo com lâminas...">
            </div>
            <div class="form-group">
                <label>Preço Venda (R$)</label>
                <input type="number" step="0.01" name="preco" required value="<?php echo $edit_prato['preco'] ?? ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Qtd em Estoque</label>
                <input type="number" name="estoque" required value="<?php echo $edit_prato['estoque'] ?? '0'; ?>" placeholder="Ex: 50">
            </div>
            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria" required>
                    <option value="Entrada" <?php echo (isset($edit_prato['categoria']) && $edit_prato['categoria'] === 'Entrada') ? 'selected' : ''; ?>>Entrada</option>
                    <option value="Prato Principal" <?php echo (isset($edit_prato['categoria']) && $edit_prato['categoria'] === 'Prato Principal') ? 'selected' : ''; ?>>Prato Principal</option>
                    <option value="Bebida" <?php echo (isset($edit_prato['categoria']) && $edit_prato['categoria'] === 'Bebida') ? 'selected' : ''; ?>>Bebida</option>
                    <option value="Sobremesa" <?php echo (isset($edit_prato['categoria']) && $edit_prato['categoria'] === 'Sobremesa') ? 'selected' : ''; ?>>Sobremesa</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Status do Item</label>
                <select name="status" required>
                    <option value="ativo" <?php echo (isset($edit_prato['status']) && $edit_prato['status'] === 'ativo') ? 'selected' : ''; ?>>Ativo (Visível)</option>
                    <option value="inativo" <?php echo (isset($edit_prato['status']) && $edit_prato['status'] === 'inativo') ? 'selected' : ''; ?>>Inativo (Oculto)</option>
                </select>
            </div>
            
            <button type="submit" class="btn-salvar"><?php echo $edit_prato ? 'Atualizar Prato' : 'Adicionar Prato'; ?></button>
        </form>

        <h4>Itens do Cardápio</h4>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th> <th>Status</th> 
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($prato = $lista_cardapio->fetch()): ?>
                    <tr style="<?php echo $prato['status'] === 'inativo' ? 'opacity: 0.5;' : ''; ?>">
                        <td><strong><?php echo htmlspecialchars($prato['nome_prato']); ?></strong></td>
                        <td><?php echo htmlspecialchars($prato['categoria']); ?></td>
                        <td>R$ <?php echo number_format($prato['preco'], 2, ',', '.'); ?></td>
                        
                        <td>
                            <strong style="color: <?php echo ($prato['estoque'] ?? 0) <= 0 ? '#c0392b' : '#27ae60'; ?>;">
                                <?php echo $prato['estoque'] ?? 0; ?> un
                            </strong>
                        </td>
                        
                        <td>
                            <span style="padding:3px 8px; border-radius:3px; font-size:12px; font-weight:bold; background: <?php echo $prato['status'] === 'inativo' ? '#95a5a6;' : '#2ecc71;'; ?> color:white;">
                                <?php echo isset($prato['status']) && $prato['status'] === 'inativo' ? 'Inativo' : 'Ativo'; ?>
                            </span>
                        </td>
                        
                        <td>
                            <a href="admin.php?editar_prato=<?php echo $prato['id']; ?>" class="btn-editar">Editar</a>
                            
                            <?php if (!isset($prato['status']) || $prato['status'] === 'ativo'): ?>
                                <a href="admin.php?desativar_prato=<?php echo $prato['id']; ?>" class="btn-excluir" style="background:#e67e22;" onclick="return confirm('Ocultar este prato do cardápio?')">Desativar</a>
                            <?php else: ?>
                                <a href="admin.php?ativar_prato=<?php echo $prato['id']; ?>" class="btn-editar" style="background:#2ecc71;" onclick="return confirm('Tornar este prato visível novamente?')">Ativar</a>
                            <?php endif; ?>
                            
                            <a href="admin.php?excluir_prato=<?php echo $prato['id']; ?>" class="btn-excluir" onclick="return confirm('ATENÇÃO: Só use a exclusão total para pratos criados por erro que NUNCA foram vendidos. Deseja mesmo tentar apagar permanentemente?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="funcionarios" class="tab-content">
        <h3><?php echo $edit_func ? 'Editar Funcionário' : 'Controle de Equipe e Recursos Humanos'; ?></h3>
        <form class="form-cadastro" action="admin.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar_funcionario">
            <input type="hidden" name="id" value="<?php echo $edit_func['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required value="<?php echo htmlspecialchars($edit_func['nome'] ?? ''); ?>" placeholder="Nome do colaborador">
            </div>
            <div class="form-group">
                <label>E-mail Corporativo</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($edit_func['email'] ?? ''); ?>" placeholder="exemplo@chefinhas.com">
            </div>
            <div class="form-group">
                <label>Senha Provisória</label>
                <input type="text" name="senha" <?php echo $edit_func ? '' : 'required'; ?> placeholder="<?php echo $edit_func ? 'Deixe vazio para manter a senha atual' : 'Defina a senha inicial'; ?>">
                <?php if ($edit_func): ?>
                    <small style="color: #777; font-size: 11px; margin-top: 4px;">Se não quiser alterar a senha atual do colaborador, basta deixar este campo vazio.</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Cargo Operacional</label>
                <select name="cargo" required>
                    <option value="garcom" <?php echo (isset($edit_func['cargo']) && $edit_func['cargo'] === 'garcom') ? 'selected' : ''; ?>>Garçom</option>
                    <option value="cozinheiro" <?php echo (isset($edit_func['cargo']) && $edit_func['cargo'] === 'cozinheiro') ? 'selected' : ''; ?>>Cozinheiro</option>
                </select>
            </div>
            <button type="submit" class="btn-salvar"><?php echo $edit_func ? 'Atualizar Colaborador' : 'Cadastrar'; ?></button>
        </form>

        <h4>Funcionários Ativos</h4>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($func = $lista_funcionarios->fetch()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($func['nome']); ?></strong></td>
                        <td><?php echo htmlspecialchars($func['email']); ?></td>
                        <td><span style="text-transform: capitalize;"><?php echo htmlspecialchars($func['cargo']); ?></span></td>
                        <td>
                            <a href="admin.php?editar_funcionario=<?php echo $func['id']; ?>" class="btn-editar">Editar</a>
                            <a href="admin.php?excluir_funcionario=<?php echo $func['id']; ?>" class="btn-excluir" onclick="return confirm('Remover funcionário do sistema?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="mesas" class="tab-content">
        <h3><?php echo $edit_mesa ? 'Editar Número da Mesa' : 'Configuração de Mesas do Salão'; ?></h3>
        <form class="form-cadastro" action="admin.php" method="POST" style="grid-template-columns: 1fr 1fr;">
            <input type="hidden" name="acao" value="cadastrar_mesa">
            <input type="hidden" name="id" value="<?php echo $edit_mesa['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Número da Mesa</label>
                <input type="number" name="numero_mesa" required value="<?php echo $edit_mesa['numero_mesa'] ?? ''; ?>" placeholder="Ex: 21">
            </div>
            <button type="submit" class="btn-salvar"><?php echo $edit_mesa ? 'Atualizar Mesa' : 'Adicionar Mesa'; ?></button>
        </form>

        <h4>Mapa do Salão</h4>
        <table>
            <thead>
                <tr>
                    <th>Mesa ID</th>
                    <th>Número Identificador</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = $lista_mesas->fetch()): ?>
                    <tr>
                        <td>#<?php echo $m['id']; ?></td>
                        <td><strong>Mesa <?php echo htmlspecialchars($m['numero_mesa']); ?></strong></td>
                        <td>
                            <a href="admin.php?editar_mesa=<?php echo $m['id']; ?>" class="btn-editar">Editar</a>
                            <a href="admin.php?excluir_mesa=<?php echo $m['id']; ?>" class="btn-excluir" onclick="return confirm('Remover esta mesa do mapa?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="clientes" class="tab-content">
        <h3><?php echo $edit_cliente ? 'Editar Dados do Cliente' : 'Controle Centralizado de Clientes'; ?></h3>
        <form class="form-cadastro" action="admin.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar_cliente">
            <input type="hidden" name="id" value="<?php echo $edit_cliente['id'] ?? ''; ?>">
            
            <div class="form-group">
                <label>Nome do Cliente</label>
                <input type="text" name="nome_cliente" required value="<?php echo htmlspecialchars($edit_cliente['nome'] ?? ''); ?>" placeholder="Ex: Maria Oliveira">
            </div>
            
            <div class="form-group">
                <label>Telefone / Contato</label>
                <input type="text" name="telefone_cliente" value="<?php echo htmlspecialchars($edit_cliente['telefone'] ?? ''); ?>" placeholder="Ex: (89) 99999-1234">
            </div>
            
            <button type="submit" class="btn-salvar"><?php echo $edit_cliente ? 'Atualizar Cliente' : 'Cadastrar Cliente'; ?></button>
        </form>

        <h4>Clientes Registrados no Banco</h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cliente</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cli = $lista_clientes->fetch()): ?>
                    <tr>
                        <td>#<?php echo $cli['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($cli['nome']); ?></strong></td>
                        <td><?php echo !empty($cli['telefone']) ? htmlspecialchars($cli['telefone']) : '<span style="color:#aaa;">Não informado</span>'; ?></td>
                        <td>
                            <a href="admin.php?editar_cliente=<?php echo $cli['id']; ?>" class="btn-editar">Editar</a>
                            <a href="admin.php?excluir_cliente=<?php echo $cli['id']; ?>" class="btn-excluir" onclick="return confirm('Deseja realmente remover este cliente do banco de dados?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="financeiro" class="tab-content">
        <h3><?php echo $edit_despesa ? 'Editar Lançamento de Gasto' : 'Painel de Lançamento de Custos & Despesas'; ?></h3>
        <form class="form-cadastro" action="admin.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar_despesa">
            <input type="hidden" name="id" value="<?php echo $edit_despesa['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Descrição do Gasto</label>
                <input type="text" name="descricao" required value="<?php echo htmlspecialchars($edit_despesa['descricao'] ?? ''); ?>" placeholder="Ex: Compra de insumos carnes">
            </div>
            <div class="form-group">
                <label>Valor Pago (R$)</label>
                <input type="number" step="0.01" name="valor" required value="<?php echo $edit_despesa['valor'] ?? ''; ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Data de Competência</label>
                <input type="date" name="data_despesa" required value="<?php echo $edit_despesa['data_despesa'] ?? date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Categoria Fiscal</label>
                <select name="categoria" required>
                    <option value="Ingredientes" <?php echo (isset($edit_despesa['categoria']) && $edit_despesa['categoria'] === 'Ingredientes') ? 'selected' : ''; ?>>Ingredientes</option>
                    <option value="Luz/Água" <?php echo (isset($edit_despesa['categoria']) && $edit_despesa['categoria'] === 'Luz/Água') ? 'selected' : ''; ?>>Luz/Água</option>
                    <option value="Salários" <?php echo (isset($edit_despesa['categoria']) && $edit_despesa['categoria'] === 'Salários') ? 'selected' : ''; ?>>Salários</option>
                    <option value="Outros" <?php echo (isset($edit_despesa['categoria']) && $edit_despesa['categoria'] === 'Outros') ? 'selected' : ''; ?>>Outros</option>
                </select>
            </div>
            <button type="submit" class="btn-salvar"><?php echo $edit_despesa ? 'Atualizar Gasto' : 'Lançar Despesa'; ?></button>
        </form>

        <h3>Planilha Contábil de Fechamento Mensal</h3>
        <table>
            <thead>
                <tr>
                    <th>Mês de Referência</th>
                    <th>Faturamento Total (Lucros)</th>
                    <th>Despesas Acumuladas</th>
                    <th>Resultado Líquido</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($linha = $planilha_mensal->fetch()): 
                    $lucro_liquido = $linha['faturamento'] - ($linha['despesa_total'] ?? 0);
                    $cor_resultado = $lucro_liquido >= 0 ? '#27ae60' : '#e74c3c';
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($linha['mes']); ?></strong></td>
                        <td style="color:#27ae60;">R$ <?php echo number_format($linha['faturamento'], 2, ',', '.'); ?></td>
                        <td style="color:#e74c3c;">R$ <?php echo number_format($linha['despesa_total'] ?? 0, 2, ',', '.'); ?></td>
                        <td style="color:<?php echo $cor_resultado; ?>; font-weight:bold;">R$ <?php echo number_format($lucro_liquido, 2, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <h4>Histórico Individual de Despesas (Para Edição/Exclusão)</h4>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $todas_despesas = $conn->query("SELECT * FROM despesas ORDER BY data_despesa DESC");
                while ($d = $todas_despesas->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($d['categoria']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($d['data_despesa'])); ?></td>
                        <td style="color:#e74c3c;">R$ <?php echo number_format($d['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="admin.php?editar_despesa=<?php echo $d['id']; ?>" class="btn-editar">Editar</a>
                            <a href="admin.php?excluir_despesa=<?php echo $d['id']; ?>" class="btn-excluir" onclick="return confirm('Excluir esta despesa permanentemente?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.menu-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(tabId).classList.add('active');
    const currentBtn = document.getElementById('tab-btn-' + tabId);
    if(currentBtn) {
        currentBtn.classList.add('active');
    } else {
        event.currentTarget.classList.add('active');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    <?php if ($edit_prato): ?> switchTab('cardapio'); <?php endif; ?>
    <?php if ($edit_func): ?> switchTab('funcionarios'); <?php endif; ?>
    <?php if ($edit_mesa): ?> switchTab('mesas'); <?php endif; ?>
    <?php if ($edit_cliente): ?> switchTab('clientes'); <?php endif; ?>
    <?php if ($edit_despesa): ?> switchTab('financeiro'); <?php endif; ?>
});
</script>
</body>
</html>