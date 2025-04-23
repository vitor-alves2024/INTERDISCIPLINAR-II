<?php
session_start();

if (!isset($_SESSION['email_recuperacao']) || !isset($_SESSION['codigo_validado'])) {
    header('Location: RecuperarSenha.php');
    exit;
}

$email = $_SESSION['email_recuperacao'];

// Conexão com o banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'agendafast');

// Processar nova senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha']) && isset($_POST['confirmar_senha'])) {
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações
    if (strlen($senha) < 8) {
        $erro = "A senha deve ter pelo menos 8 caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        // Atualizar senha no banco de dados
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("UPDATE usuarios SET senha_hash = ? WHERE email = ?");
        $stmt->bind_param("ss", $senha_hash, $email);
        
        if ($stmt->execute()) {
            // Limpar sessão e redirecionar para login
            session_unset();
            session_destroy();
            
            header('Location: Login.php?senha_alterada=1');
            exit;
        } else {
            $erro = "Erro ao atualizar senha. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - Agenda Inteligente</title>
    <style>
        /* Mesmos estilos dos arquivos anteriores */
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
            margin-top: 2rem;
        }
        
        .button {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            background-color: #3498db;
            color: white;
        }
        
        .button:hover {
            background-color: #2980b9;
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
            <span class="titulo">Nova Senha</span>
            
            <?php if (isset($erro)) : ?>
                <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="senha">Nova senha (mínimo 8 caracteres)</label>
                <input type="password" id="senha" name="senha" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="password-strength-bar"></div>
                </div>
            </div>
            
            <div class="input-group">
                <label for="confirmar_senha">Confirmar nova senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            
            <div class="button-container">
                <button type="submit" class="button">Atualizar Senha</button>
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
            
            if (senha.length >= 8) strength += 1;
            if (senha.length >= 12) strength += 1;
            if (/[A-Z]/.test(senha)) strength += 1;
            if (/[0-9]/.test(senha)) strength += 1;
            if (/[^A-Za-z0-9]/.test(senha)) strength += 1;
            
            const width = strength * 25;
            strengthBar.style.width = width + '%';
            
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