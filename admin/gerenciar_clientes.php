<?php
session_start();
require_once '../config/conexao.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$mensagem = "";
$cliente_editando = null;


if (isset($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    
    
    $sql_check = "SELECT id FROM pedidos WHERE cliente_id = $id_excluir LIMIT 1";
    $res_check = $conn->query($sql_check);
    
    if ($res_check && $res_check->num_rows > 0) {
        $mensagem = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:15px;'>Erro: Não é possível excluir este cliente porque ele já possui pedidos registrados no histórico.</p>";
    } else {
        $sql_del = "DELETE FROM clientes WHERE id = $id_excluir";
        if ($conn->query($sql_del)) {
            $mensagem = "<p style='color:#27ae60; font-weight:bold; margin-bottom:15px;'>Cliente excluído com sucesso!</p>";
        } else {
            $mensagem = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:15px;'>Erro ao excluir cliente no banco de dados.</p>";
        }
    }
}


if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql_busca = "SELECT * FROM clientes WHERE id = $id_editar LIMIT 1";
    $res_busca = $conn->query($sql_busca);
    if ($res_busca && $res_busca->num_rows > 0) {
        $cliente_editando = $res_busca->fetch_assoc();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $conn->real_escape_string(trim($_POST['nome']));
    $telefone = $conn->real_escape_string(trim($_POST['telefone']));
    
    if (!empty($nome)) {
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente'])) {
           
            $id_cliente = intval($_POST['id_cliente']);
            $sql_salvar = "UPDATE clientes SET nome = '$nome', telefone = '$telefone' WHERE id = $id_cliente";
            $txt_sucesso = "Cliente atualizado com sucesso!";
        } else {
            
            $sql_salvar = "INSERT INTO clientes (nome, telefone) VALUES ('$nome', '$telefone')";
            $txt_sucesso = "Novo cliente cadastrado com sucesso!";
        }
        
        if ($conn->query($sql_salvar)) {
            $mensagem = "<p style='color:#27ae60; font-weight:bold; margin-bottom:15px;'>$txt_sucesso</p>";
            $cliente_editando = null;
        } else {
            $mensagem = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:15px;'>Erro ao salvar dados no banco de dados. Verifique se a coluna 'telefone' existe.</p>";
        }
    } else {
        $mensagem = "<p style='color:#e74c3c; font-weight:bold; margin-bottom:15px;'>O nome do cliente é obrigatório.</p>";
    }
}


$sql_clientes = "SELECT * FROM clientes ORDER BY nome ASC";
$resultado_clientes = $conn->query($sql_clientes);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes - Administrador</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; color: #333; padding: 20px; }
        
        .voltar-link { display: inline-block; margin-bottom: 20px; color: #ba43df; text-decoration: none; font-weight: bold; font-size: 14px; }
        .voltar-link:hover { text-decoration: underline; }

        .painel { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; max-width: 1000px; margin-left: auto; margin-right: auto; }
        h2, h3 { margin-bottom: 20px; color: #000; border-bottom: 2px solid #eee; padding-bottom: 5px; }
        
        .form-row { display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
        
        .btn-salvar { background: #ba43df; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; transition: background 0.2s; }
        .btn-salvar:hover { background: #9b28be; }
        .btn-cancelar { background: #7f8c8d; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 15px; margin-left: 10px; }

        .tabela { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 15px; }
        .tabela th, .tabela td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .tabela th { background: #f1f1f1; font-weight: bold; }
        
        .btn-acao-edit { background: #3498db; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; margin-right: 5px; }
        .btn-acao-edit:hover { background: #2980b9; }
        .btn-acao-del { background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-acao-del:hover { background: #c0392b; }
    </style>
</head>
<body>

<div class="painel">
    <a href="admin.php" class="voltar-link">← Voltar para o Painel Principal</a>
    
    <h2>Gerenciar Clientes do Sistema</h2>
    <?php echo $mensagem; ?>

    <h3><?php echo $cliente_editando ? "Editar Dados do Cliente" : "Cadastrar Novo Cliente"; ?></h3>
    <form action="gerenciar_clientes.php" method="POST">
        <input type="hidden" name="id_cliente" value="<?php echo $cliente_editando ? $cliente_editando['id'] : ''; ?>">
        
        <div class="form-row">
            <div>
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required placeholder="Ex: Maria Oliveira" value="<?php echo $cliente_editando ? htmlspecialchars($cliente_editando['nome']) : ''; ?>">
            </div>
            <div>
                <label for="telefone">Telefone / Contato:</label>
                <input type="text" id="telefone" name="telefone" placeholder="Ex: (89) 99999-1234" value="<?php echo $cliente_editando ? htmlspecialchars($cliente_editando['telefone']) : ''; ?>">
            </div>
        </div>
        
        <button type="submit" class="btn-salvar">
            <?php echo $cliente_editando ? "Atualizar Dados" : "Salvar Cadastro"; ?>
        </button>
        
        <?php if ($cliente_editando): ?>
            <a href="gerenciar_clientes.php" class="btn-cancelar">Cancelar Edição</a>
        <?php endif; ?>
    </form>
</div>

<div class="painel">
    <h3>Clientes Cadastrados no Banco</h3>
    <table class="tabela">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Cliente</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado_clientes->num_rows === 0): ?>
                <tr><td colspan="4" style="text-align: center; color: #999;">Nenhum cliente cadastrado ainda.</td></tr>
            <?php endif; ?>
            <?php while ($cli = $resultado_clientes->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $cli['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($cli['nome']); ?></strong></td>
                    <td><?php echo !empty($cli['telefone']) ? htmlspecialchars($cli['telefone']) : '<span style="color:#aaa;">Não informado</span>'; ?></td>
                    <td>
                        <a href="gerenciar_clientes.php?editar=<?php echo $cli['id']; ?>" class="btn-acao-edit">Editar</a>
                        <a href="gerenciar_clientes.php?excluir=<?php echo $cli['id']; ?>" class="btn-acao-del" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>