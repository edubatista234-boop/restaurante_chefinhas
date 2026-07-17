<?php
session_start();
require_once 'config/conexao.php';


if (isset($_SESSION['usuario_id']) && isset($_SESSION['cargo'])) {
    if ($_SESSION['cargo'] == 'administrador') {
       
        header("Location: admin/admin.php");
        exit;
    } elseif ($_SESSION['cargo'] == 'garcom') {
        
        header("Location: garcom/garcom.php");
        exit;
    } elseif ($_SESSION['cargo'] == 'cozinheiro') {
       
        header("Location: cozinha/cozinha.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurante</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f4f6f9; 
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #080808;
        }
        .login-container {
            background: #d0e4fd;
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(41, 32, 32, 0.37);
            border: 1px solid rgba(199, 79, 79, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #000000;
            font-weight: 600;
        }
        .login-container p {
            text-align: center;
            font-size: 14px;
            color: #000000;
            margin-bottom: 30px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #000000;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: #ffffff;
            color: #000000;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }
        .input-group select option {
            background: #ffffff;
            color: #000000;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #ffffff;
            box-shadow: 0 0 8px rgba(197, 178, 178, 0.5);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #070606;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #ddc8c8;
            transform: translateY(-2px);
        }
        .error-msg {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #ea6153;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Restaurante</h2>
    <p>Painel de Controle Integrado</p>

    <?php if (isset($_GET['erro'])): ?>
        <div class="error-msg">Dados incorretos para o perfil selecionado!</div>
    <?php endif; ?>

    <form action="processa_login.php" method="POST">
        <div class="input-group">
            <label for="cargo">Acessar como:</label>
            <select id="cargo" name="cargo" required>
                <option value="" disabled selected>Selecione seu perfil...</option>
                <option value="administrador">Administrador</option>
                <option value="garcom">Garçom</option>
                <option value="cozinheiro">Cozinheiro</option>
            </select>
        </div>

        <div class="input-group">
            <label for="email">E-mail Profissional</label>
            <input type="email" id="email" name="email" required placeholder="exemplo@gmail.com">
        </div>
        
        <div class="input-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-login">Acessar Sistema</button>
    </form>
</div>

</body>
</html>