<?php 
session_start();

require_once '../config/conexao.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'garcom') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_cadastro_cliente'])) {
    $nome_novo = trim($_POST['novo_nome'] ?? '');
    $telefone_novo = trim($_POST['novo_telefone'] ?? '');
    
    if (!empty($nome_novo)) {
        try {
            $sql_insere_cli = "INSERT INTO clientes (nome, telefone) VALUES (:nome, :telefone)";
            $stmt = $conn->prepare($sql_insere_cli);
            $sucesso = $stmt->execute([
                ':nome' => $nome_novo,
                ':telefone' => $telefone_novo
            ]);
            
            if ($sucesso) {
                echo 'sucesso';
            } else {
                echo 'erro';
            }
        } catch (PDOException $e) {
            echo 'erro';
        }
    } else {
        echo 'erro';
    }
    exit; 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_registrar_pagamento'])) {
    $pedido_id = intval($_POST['pedido_id'] ?? 0);
    $forma_pag = trim($_POST['forma_pagamento'] ?? '');
    
    if ($pedido_id > 0 && !empty($forma_pag)) {
        try {
            
            $sql_pag = "UPDATE pedidos SET forma_pagamento = :forma, status_pagamento = 'pago' WHERE id = :id";
            $stmt = $conn->prepare($sql_pag);
            $sucesso = $stmt->execute([
                ':forma' => $forma_pag,
                ':id' => $pedido_id
            ]);
            
            if ($sucesso) {
                
                $sql_busca_mesa = "SELECT mesa_id FROM pedidos WHERE id = :pedido_id";
                $stmt_busca = $conn->prepare($sql_busca_mesa);
                $stmt_busca->execute([':pedido_id' => $pedido_id]);
                $pedido_info = $stmt_busca->fetch(PDO::FETCH_ASSOC);

                if ($pedido_info) {
                    $mesa_id_pedido = $pedido_info['mesa_id'];
                    
                    
                    $sql_liberar_mesa = "UPDATE mesas SET status_mesa = 'livre', cliente_atual = NULL WHERE id = :mesa_id";
                    $stmt_liberar = $conn->prepare($sql_liberar_mesa);
                    $stmt_liberar->execute([':mesa_id' => $mesa_id_pedido]);
                }

                echo 'sucesso';
            } else {
                echo 'erro';
            }
        } catch (PDOException $e) {
            echo 'erro';
        }
    } else {
        echo 'erro';
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_detalhes_pedido'])) {
    $pedido_id = intval($_POST['pedido_id'] ?? 0);
    try {
    
        $sql_itens = "SELECT c.nome_prato, ip.quantidade, ip.preco_unitario 
                      FROM itens_pedido ip 
                      JOIN cardapio c ON ip.cardapio_id = c.id 
                      WHERE ip.pedido_id = :pedido_id";
        $stmt = $conn->prepare($sql_itens);
        $stmt->execute([':pedido_id' => $pedido_id]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($itens);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}

$garcom_id = $_SESSION['usuario_id'];


$mesas_sql = "SELECT * FROM mesas ORDER BY numero_mesa ASC";
$mesas_resultado = $conn->query($mesas_sql);

$cardapio_sql = "SELECT id, nome_prato, descricao, preco, categoria, status, estoque FROM cardapio WHERE status = 'ativo' ORDER BY categoria, nome_prato ASC";
$cardapio_resultado = $conn->query($cardapio_sql);
$todos_pratos = $cardapio_resultado->fetchAll(PDO::FETCH_ASSOC);


$pedidos_sql = "SELECT p.id, m.numero_mesa, c.nome as nome_cliente, p.status_pedido, p.valor_total, p.data_horario, p.status_pagamento, p.forma_pagamento 
                FROM pedidos p
                JOIN mesas m ON p.mesa_id = m.id
                JOIN clientes c ON p.cliente_id = c.id
                WHERE p.usuario_garcom_id = :garcom_id AND DATE(p.data_horario) = CURRENT_DATE()
                ORDER BY p.data_horario DESC";
$pedidos_stmt = $conn->prepare($pedidos_sql);
$pedidos_stmt->execute([':garcom_id' => $garcom_id]);
$pedidos = $pedidos_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Garçom - Restaurante das Chefinhas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
        
        header { background: #d479b1; color: #000000; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #75aff5; }
        header h1 { color: #000000; font-size: 22px; }
        .btn-sair { background: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; }
        
        .container { display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; padding: 20px; max-width: 1400px; margin: 0 auto; width: 100%; flex: 1; }
        .painel { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        h3 { margin-bottom: 15px; color: #000000; border-bottom: 2px solid #eee; padding-bottom: 5px; }

        .form-group { margin-bottom: 15px; }
        .form-row-duplo { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="search"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
        
        .btn-cadastrar-btn { background: #357ed6; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; width: 100%; margin-top: 23px; }
        .btn-cadastrar-btn:hover { background: #c4ddfc; }

        .busca-container { margin-bottom: 15px; }
        .grid-pratos { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-height: 430px; overflow-y: auto; padding-right: 5px; }
        
        .card-prato { background: #f8f9fa; border: 1px solid #e9ecef; padding: 10px; border-radius: 6px; display: flex; flex-direction: column; justify-content: space-between; cursor: pointer; transition: background 0.2s; }
        .card-prato:hover { background: #f1f3f5; border-color: #ba43df; }
        .card-prato h4 { font-size: 14px; color: #000000; }
        .card-prato p { font-size: 12px; color: #666; margin: 4px 0; }
        .card-prato .preco { color: #27ae60; font-weight: bold; font-size: 14px; margin-top: 5px; }
        
        .card-prato.sem-estoque { background: #fce8e6 !important; border-color: #f4c7c3 !important; cursor: not-allowed !important; opacity: 0.7; }
        .card-prato.sem-estoque h4 { color: #c0392b !important; }
        .card-prato .estoque-aviso { font-size: 11px; font-weight: bold; display: block; margin-top: 3px; }

        .tabela-pedido { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabela-pedido th, .tabela-pedido td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .tabela-pedido th { background: #f1f1f1; }
        .btn-remover { background: #ff4d4d; color: white; border: none; padding: 2px 8px; border-radius: 4px; cursor: pointer; }
        
        .total-container { margin-top: 20px; padding-top: 15px; border-top: 2px dashed #ddd; text-align: right; font-size: 18px; font-weight: bold; }
        .btn-finalizar { width: 100%; background: #27ae60; color: white; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 15px; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; text-transform: uppercase; display: inline-block; }
        .badge-pendente { background: #e67e22; }
        .badge-preparo { background: #3498db; }
        .badge-finalizado { background: #2ecc71; }
        .badge-entregue { background: #7f8c8d; }

        /* Estilização dos Novos Botões de Ação */
        .btn-acao-print { background: #8e44ad; color: white; border: none; padding: 5px 8px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px; margin-right: 4px; }
        .btn-acao-pagar { background: #27ae60; color: white; border: none; padding: 5px 8px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px; }
        .badge-pago { background: #2ecc71; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }

        /* Estilo da Janela Modal (Fechamento de Conta) */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-conteudo { background: white; padding: 25px; border-radius: 10px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .btn-modal-cancelar { background: #7f8c8d; color: white; padding: 10px; border: none; border-radius: 5px; width: 48%; cursor: pointer; font-weight: bold; margin-top: 15px; }
        .btn-modal-salvar { background: #27ae60; color: white; padding: 10px; border: none; border-radius: 5px; width: 48%; cursor: pointer; font-weight: bold; margin-top: 15px; float: right; }


.comanda-impressao { display: none; }


.comanda-impressao { display: none; }

@media print {
   
    body * { 
        visibility: hidden; 
    }
    
   
    .comanda-impressao, .comanda-impressao * { 
        visibility: visible; 
    }
    
    .comanda-impressao { 
        display: block !important; 
        position: absolute; 
        left: 50%; 
        top: 40px; 
        transform: translateX(-50%); 
        width: 400%; 
        max-width: 500px; 
        padding: 20px;
        box-sizing: border-box;
    }

    @page { 
        size: 80mm auto; 
        margin: 0; 
    }

}   
    </style>
</head>


<body>

<header>
    <h1>Restaurante da Sara - Módulo Garçom</h1>
    <div>
        <span style="margin-right: 15px;">Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong></span>
        <a href="/RESTAURANTE_CHEFINHAS/logout.php" class="btn-sair">Sair do Sistema</a>
    </div>
</header>

<div class="container">
    <div>
        <div class="painel">
            <h3>Cadastrar Novo Cliente</h3>
            <div id="msg_cliente_ajax"></div>
            <div class="form-row-duplo">
                <div class="form-group">
                    <label for="novo_nome">Nome Completo:</label>
                    <input type="text" id="novo_nome" placeholder="Ex: João Silva">
                </div>
                <div class="form-group">
                    <label for="novo_telefone">Telefone:</label>
                    <input type="text" id="novo_telefone" placeholder="(00) 99999-0000">
                </div>
            </div>
            <button type="button" class="btn-cadastrar-btn" onclick="cadastrarClienteModoDireto()">Cadastrar</button>
        </div>

        <div class="painel">
            <form id="formPedido" action="enviar_pedido.php" method="POST">
                <h3>1. Identificação</h3>
                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente:</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" required placeholder="Digite o nome ou use o cadastro acima">
                </div>
                
<div class="form-group">
    <label for="mesa">Número da Mesa:</label>
    <select id="mesa" name="mesa_id" required>
        <option value="" disabled selected>Selecione a mesa (1 a 20)</option>
        
        <?php 
        // Buscamos as mesas e seus status atualizados
        $mesas_sql = "SELECT id, numero_mesa, status_mesa, cliente_atual FROM mesas ORDER BY numero_mesa ASC";
        $mesas_resultado = $conn->query($mesas_sql);
        
        while($mesa = $mesas_resultado->fetch(PDO::FETCH_ASSOC)): 
            $is_ocupada = ($mesa['status_mesa'] === 'ocupada');
        ?>
            <option value="<?php echo $mesa['id']; ?>" <?php echo $is_ocupada ? 'disabled' : ''; ?>>
                Mesa <?php echo $mesa['numero_mesa']; ?> 
                <?php echo $is_ocupada ? ' - 🔴 OCUPADA (' . htmlspecialchars($mesa['cliente_atual']) . ')' : ' - 🟢 LIVRE'; ?>
            </option>
        <?php endwhile; ?>

    </select>
</div>
                <h3>2. Cardápio (Clique no Item)</h3>
                <div class="busca-container">
                    <input type="search" id="busca_prato" placeholder="Digite o nome do prato para filtrar..." onkeyup="filtrarCardapio()">
                </div>

                <div class="grid-pratos" id="lista_pratos">
                    <?php foreach ($todos_pratos as $prato): 
                        $qtd_estoque = intval($prato['estoque'] ?? 0);
                        $is_esgotado = ($qtd_estoque <= 0);
                    ?>
                        <div class="card-prato <?php echo $is_esgotado ? 'sem-estoque' : ''; ?>" 
                             data-nome="<?php echo strtolower(htmlspecialchars($prato['nome_prato'])); ?>" 
                             onclick="if(!this.classList.contains('sem-estoque')) { adicionarItem(<?php echo $prato['id']; ?>, '<?php echo htmlspecialchars($prato['nome_prato'], ENT_QUOTES); ?>', <?php echo $prato['preco']; ?>, <?php echo $qtd_estoque; ?>); }">
                            <div>
                                <h4><?php echo htmlspecialchars($prato['nome_prato']); ?></h4>
                                <p><?php echo htmlspecialchars($prato['descricao']); ?></p>
                            </div>
                            <div>
                                <span class="preco">R$ <?php echo number_format($prato['preco'], 2, ',', '.'); ?></span>
                                <?php if($is_esgotado): ?>
                                    <span class="estoque-aviso" style="color: #c0392b;"> ESGOTADO</span>
                                <?php else: ?>
                                    <span class="estoque-aviso" style="color: #27ae60;"> Estoque: <?php echo $qtd_estoque; ?> un</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>
    </div>

    <div>
        <div class="painel">
            <h3>3. Itens do Pedido Atual</h3>
            <table class="tabela-pedido">
                <thead>
                    <tr>
                        <th>Prato</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="itens_selecionados"></tbody>
            </table>
            <input type="hidden" id="dados_itens" name="dados_itens" required>

            <div class="total-container">
                Total do Pedido: <span id="valor_total_exibido" style="color: #27ae60;">R$ 0,00</span>
            </div>
            <button type="submit" class="btn-finalizar">Finalizar Pedido (Enviar para Cozinha)</button>
            </form>
        </div>

        <div class="painel">
            <h3>4. Acompanhamento de Pedidos (Hoje)</h3>
            <table class="tabela-pedido" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th>Mesa</th>
                        <th>Cliente</th>
                        <th>Horário</th>
                        <th>Status Cozinha</th>
                        <th>Ações / Conta</th>
                    </tr>
                </thead>
                <tbody id="pedidos_reais">
                    <?php if(count($pedidos) === 0): ?>
                        <tr><td colspan="5" style="color:#999; text-align:center;">Nenhum pedido feito hoje.</td></tr>
                    <?php endif; ?>
                    <?php foreach($pedidos as $ped): 
                        $classe_badge = 'badge-pendente';
                        if($ped['status_pedido'] == 'em preparo') $classe_badge = 'badge-preparo';
                        if($ped['status_pedido'] == 'finalizado') $classe_badge = 'badge-finalizado';
                        if($ped['status_pedido'] == 'entregue') $classe_badge = 'badge-entregue';
                        
                        $is_pago = ($ped['status_pagamento'] === 'pago');
                    ?>
                        <tr id="linha-pedido-<?php echo $ped['id']; ?>">
                            <td><strong>Mesa <?php echo htmlspecialchars($ped['numero_mesa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($ped['nome_cliente']); ?></td>
                            <td><?php echo date('H:i', strtotime($ped['data_horario'])); ?></td>
                            <td>
                                <span class="badge <?php echo $classe_badge; ?>">
                                    <?php echo htmlspecialchars($ped['status_pedido']); ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn-acao-print" onclick="abrirComandaImpressao(<?php echo $ped['id']; ?>, '<?php echo $ped['numero_mesa']; ?>', '<?php echo htmlspecialchars($ped['nome_cliente'], ENT_QUOTES); ?>', '<?php echo date('d/m/Y H:i', strtotime($ped['data_horario'])); ?>', <?php echo $ped['valor_total']; ?>)"> Comanda</button>
                                
                                <span class="area-pagamento-status">
                                    <?php if($is_pago): ?>
                                        <span class="badge-pago"> Pago (<?php echo strtoupper(htmlspecialchars($ped['forma_pagamento'])); ?>)</span>
                                    <?php else: ?>
                                        <button type="button" class="btn-acao-pagar" onclick="abrirModalPagamento(<?php echo $ped['id']; ?>, <?php echo $ped['valor_total']; ?>)"> Pagar</button>
                                    <?php endif; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="comanda-imprimir" class="comanda-impressao" style="padding: 20px; box-sizing: border-box; font-family: 'Courier New', Courier, monospace;">
    <div style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 12px; margin-bottom: 10px;">
        <h2 style="margin: 0; text-transform: uppercase; font-size: 18px; letter-spacing: 1px;">Restaurante da Sara</h2>
        <p style="margin: 5px 0 0 0; font-size: 13px; font-weight: bold;">Controle de Consumo</p>
    </div>
    <div style="padding: 5px 0; border-bottom: 1px dashed #000; font-size: 14px; line-height: 1.6; margin-bottom: 10px;">
        <b>Mesa:</b> <span id="print-mesa"></span><br>
        <b>Cliente:</b> <span id="print-cliente"></span><br>
        <b>Garçom:</b> <span><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span><br>
        <b>Horário:</b> <span id="print-horario"></span><br>
    </div>
    <table style="width: 100%; font-size: 14px; margin: 10px 0; border-collapse: collapse; line-height: 1.6;">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="text-align: left; padding-bottom: 5px;">Item</th>
                <th style="text-align: center; padding-bottom: 5px;">Qtd</th>
                <th style="text-align: right; padding-bottom: 5px;">Val.</th>
            </tr>
        </thead>
        <tbody id="print-itens" style="padding-top: 5px;">
            </tbody>
    </table>
    <div style="border-top: 2px dashed #000; padding-top: 10px; margin-top: 10px; text-align: right; font-size: 16px;">
        <b>Total Geral: <span id="print-total"></span></b>
    </div>
    <div style="text-align: center; margin-top: 25px; font-size: 12px; line-height: 1.4;">
        <p style="font-weight: bold;">Agradecemos a preferência!</p>
        <p>*** Sem valor fiscal ***</p>
    </div>
</div>

<div id="modalPagamento" class="modal">
    <div class="modal-conteudo">
        <h3 style="margin-bottom: 10px;">Fechar Conta</h3>
        <p style="margin-bottom: 15px; font-size: 15px;">Valor Total a Pagar: <strong id="modal-valor-exibido" style="color:#27ae60;">R$ 0,00</strong></p>
        
        <input type="hidden" id="modal-pedido-id">
        
        <div class="form-group">
            <label for="modal_forma_pagamento">Escolha a Forma de Pagamento:</label>
            <select id="modal_forma_pagamento">
                <option value="pix" selected>PIX</option>
                <option value="dinheiro">Dinheiro</option>
                <option value="cartao_credito">Cartão de Crédito</option>
                <option value="cartao_debito">Cartão de Débito</option>
            </select>
        </div>
        
        <button type="button" class="btn-modal-cancelar" onclick="fecharModalPagamento()">Cancelar</button>
        <button type="button" class="btn-modal-salvar" onclick="processarPagamentoMercado()">Confirmar e Pagar</button>
    </div>
</div>

<script>
let carrinho = [];

function cadastrarClienteModoDireto() {
    let nome = document.getElementById('novo_nome').value.trim();
    let telefone = document.getElementById('novo_telefone').value.trim();
    let msgDiv = document.getElementById('msg_cliente_ajax');
    
    if(nome === "") {
        alert("Por favor, digite o nome do cliente.");
        return;
    }
    
    let formData = new FormData();
    formData.append('ajax_cadastro_cliente', '1');
    formData.append('novo_nome', nome);
    formData.append('novo_telefone', telefone);
    
    fetch('garcom.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if(data.trim() === 'sucesso') {
            msgDiv.innerHTML = "<p style='color:#27ae60; font-weight:bold; margin-bottom:10px;'>Cliente registrado! Nome enviado para o pedido.</p>";
            document.getElementById('nome_cliente').value = nome;
            document.getElementById('novo_nome').value = '';
            document.getElementById('novo_telefone').value = '';
        } else {
            msgDiv.innerHTML = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:10px;'>Erro ao registrar cliente no banco.</p>";
        }
    })
    .catch(err => {
        msgDiv.innerHTML = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:10px;'>Erro de conexão.</p>";
    });
}

function filtrarCardapio() {
    let termo = document.getElementById('busca_prato').value.toLowerCase();
    let cards = document.querySelectorAll('.card-prato');
    cards.forEach(card => {
        if (card.getAttribute('data-nome').includes(termo)) card.style.display = "flex";
        else card.style.display = "none";
    });
}

function adicionarItem(id, nome, preco, estoqueMaximo) {
    let itemExistente = carrinho.find(item => item.id === id);
    if (itemExistente) {
        if (itemExistente.quantidade >= estoqueMaximo) {
            alert(`Atenção: Não é possível adicionar mais unidades. Limite de estoque (${estoqueMaximo} un) atingido!`);
            return;
        }
        itemExistente.quantidade += 1;
    } else {
        if (estoqueMaximo <= 0) {
            alert("Este produto está esgotado!");
            return;
        }
        carrinho.push({ id: id, nome: nome, preco: preco, quantidade: 1 });
    }
    atualizarInterfacePedido();
}

function removerItem(id) {
    carrinho = carrinho.filter(item => item.id !== id);
    atualizarInterfacePedido();
}

function atualizarInterfacePedido() {
    let tbody = document.getElementById('itens_selecionados');
    tbody.innerHTML = "";
    let total = 0;
    carrinho.forEach(item => {
        let subtotal = item.preco * item.quantidade;
        total += subtotal;
        tbody.innerHTML += `<tr>
            <td>${item.nome}</td>
            <td>${item.quantidade}</td>
            <td>R$ ${item.preco.toFixed(2).replace('.', ',')}</td>
            <td>R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
            <td><button type="button" class="btn-remover" onclick="event.stopPropagation(); removerItem(${item.id})">X</button></td>
        </tr>`;
    });
    document.getElementById('valor_total_exibido').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
    document.getElementById('dados_itens').value = JSON.stringify(carrinho);
}

// Funções do fluxo de Fechamento de Conta Dinâmico
function abrirModalPagamento(id, valorTotal) {
    document.getElementById('modal-pedido-id').value = id;
    document.getElementById('modal-valor-exibido').innerText = `R$ ${valorTotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('modalPagamento').style.display = "flex";
}

function fecharModalPagamento() {
    document.getElementById('modalPagamento').style.display = "none";
}

function processarPagamentoMercado() {
    let id = document.getElementById('modal-pedido-id').value;
    let forma = document.getElementById('modal_forma_pagamento').value;
    
    let formData = new FormData();
    formData.append('ajax_registrar_pagamento', '1');
    formData.append('pedido_id', id);
    formData.append('forma_pagamento', forma);
    
    fetch('garcom.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(res => {
        if (res.trim() === 'sucesso') {
            fecharModalPagamento();
            // Injeta o badge de pago instantaneamente na tabela sem recarregar
            let linha = document.getElementById(`linha-pedido-${id}`);
            if (linha) {
                let areaStatus = linha.querySelector('.area-pagamento-status');
                if (areaStatus) {
                    areaStatus.innerHTML = `<span class="badge-pago">✅ Pago (${forma.toUpperCase()})</span>`;
                }
            }
            alert('Pagamento processado e conta encerrada!');
        } else {
            alert('Falha interna ao salvar dados de fechamento.');
        }
    })
    .catch(err => alert('Erro de comunicação de rede.'));
}

function abrirComandaImpressao(id, mesa, cliente, horario, total) {
    let formData = new FormData();
    formData.append('ajax_detalhes_pedido', '1');
    formData.append('pedido_id', id);
    
    fetch('garcom.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(itens => {
        document.getElementById('print-mesa').innerText = mesa;
        document.getElementById('print-cliente').innerText = cliente;
        document.getElementById('print-horario').innerText = horario;
        document.getElementById('print-total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
        
        let containerItens = document.getElementById('print-itens');
        containerItens.innerHTML = "";
        
        itens.forEach(item => {
            let sub = parseFloat(item.preco_unitario) * parseInt(item.quantidade);
            containerItens.innerHTML += `
                <tr>
                    <td>${item.nome_prato}</td>
                    <td style="text-align:center;">${item.quantidade}</td>
                    <td style="text-align:right;">R$ ${sub.toFixed(2).replace('.', ',')}</td>
                </tr>
            `;
        });
        
        // Dispara o gerenciador de impressão nativo com foco no contêiner estilizado
        window.print();
    })
    .catch(err => alert('Erro ao buscar itens da comanda.'));
}

document.getElementById('formPedido').addEventListener('submit', function(e) {
    if (carrinho.length === 0) {
        e.preventDefault();
        alert('Por favor, adicione pelo menos um prato!');
    }
});
</script>
</body>
</html>