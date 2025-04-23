<?php
session_start();

// Se o usuário já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header('Location: menu_principal.php');
    exit;
}

// Conexão com o banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'agendafast');

// Processar formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }
    
    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não coincidem.";
    }
    
    // Verificar se e-mail já existe
    if (empty($erros)) {
        $stmt = $mysqli->prepare("SELECT usuario_id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $erros[] = "Este e-mail já está cadastrado.";
        }
        $stmt->close();
    }
    
    // Se não houver erros, cadastra o usuário
    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $senha_hash);
        
        if ($stmt->execute()) {
            // Cadastro bem-sucedido, loga o usuário
            $_SESSION['usuario_id'] = $stmt->insert_id;
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;
            
            header('Location: MenuPrincipal.php');
            exit;
        } else {
            $erros[] = "Erro ao cadastrar. Tente novamente mais tarde.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Inteligente - Primeiro Acesso</title>
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
        
        .erro ul {
            margin: 0;
            padding-left: 1.2rem;
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
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #3498db;
            font-size: 0.9rem;
        }
        
        .login-link a {
            color: inherit;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            height: 5px;
            background-color: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            background-color: #e74c3c;
            transition: width 0.3s, background-color 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" action="">
            <span class="titulo">Criar sua conta</span>
            
            <?php if (!empty($erros)) : ?>
                <div class="erro">
                    <ul>
                        <?php foreach ($erros as $erro) : ?>
                            <li><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" required 
                       value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>
            
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>
            
            <div class="input-group">
                <label for="senha">Senha (mínimo 8 caracteres)</label>
                <input type="password" id="senha" name="senha" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="password-strength-bar"></div>
                </div>
            </div>
            
            <div class="input-group">
                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            
            <div class="button-container">
                <button type="submit" class="button button-primary">Criar conta</button>
                <button type="button" class="button button-secondary" onclick="window.location.href='Login.php'">Voltar para login</button>
            </div>
            
            <div class="login-link">
                Já tem uma conta? <a href="Login.php">Faça login</a>
            </div>
        </form>
    </div>

    <script>
        // Validação de força da senha em tempo real
        const senhaInput = document.getElementById('senha');
        const strengthBar = document.getElementById('password-strength-bar');
        
        senhaInput.addEventListener('input', function() {
            const senha = this.value;
            let strength = 0;
            
            // Verifica o comprimento
            if (senha.length >= 8) strength += 1;
            if (senha.length >= 12) strength += 1;
            
            // Verifica caracteres diversos
            if (/[A-Z]/.test(senha)) strength += 1;
            if (/[0-9]/.test(senha)) strength += 1;
            if (/[^A-Za-z0-9]/.test(senha)) strength += 1;
            
            // Atualiza a barra de força
            const width = strength * 25;
            strengthBar.style.width = width + '%';
            
            // Muda a cor baseado na força
            if (strength < 2) {
                strengthBar.style.backgroundColor = '#e74c3c';
            } else if (strength < 4) {
                strengthBar.style.backgroundColor = '#f39c12';
            } else {
                strengthBar.style.backgroundColor = '#2ecc71';
            }
        });
    </script>
</body>
</html>