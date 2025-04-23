
<?php
session_start();


// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: MenuPrincipal.php');
    exit;
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {
    // Conecta ao banco de dados
    $mysqli = new mysqli('localhost', 'root', '', 'agendafast');
    
    if ($mysqli->connect_error) {
        die("Erro de conexão: " . $mysqli->connect_error);
    }

    $email = $mysqli->real_escape_string($_POST['email']);
    
    // Busca o usuário pelo email
    $stmt = $mysqli->prepare('SELECT usuario_id, nome, email, senha_hash FROM usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // Verifica a senha (usando password_verify para senhas hasheadas)
        if (password_verify($_POST['senha'], $usuario['senha_hash'])) {
            // Autenticação bem-sucedida
            $_SESSION['usuario_id'] = $usuario['usuario_id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['email'] = $usuario['email'];
            
            // Redireciona para o menu principal
            header('Location: MenuPrincipal.php');
            exit;
        }
    }
    
    // Se chegou aqui, a autenticação falhou
    $erro = 'E-mail ou senha incorretos.';
    
    // Fecha a conexão
    $stmt->close();
    $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Inteligente - Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        
        .titulo {
            display: block;
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .erro {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .input-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .input-group input:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 2rem;
        }
        
        .button {
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .button-primary {
            background-color: #3498db;
            color: white;
        }
        
        .button-primary:hover {
            background-color: #2980b9;
        }
        
        .button-secondary {
            background-color: #ecf0f1;
            color: #2c3e50;
        }
        
        .button-secondary:hover {
            background-color: #d5dbdb;
        }
        
        .esqueci {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #3498db;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .esqueci:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" action="">
            <span class="titulo">Bem-vindo à Agenda Inteligente</span>
            
            <?php if (isset($erro)) : ?>
                <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <div class="button-container">
                <button type="submit" class="button button-primary">Entrar</button>
                <button type="button" class="button button-secondary" onclick="window.location.href='PrimeiroAcesso.php'">Primeiro acesso</button>
            </div>
            
            <span class="esqueci" onclick="window.location.href='RecuperarSenha.php'">Esqueci minha senha</span>
        </form>
    </div>
</body>
</html>