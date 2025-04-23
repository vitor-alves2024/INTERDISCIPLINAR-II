<?php
session_start();

// Conexão com o banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'agendafast');

// Processar solicitação de recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Verificar se o e-mail existe
    $stmt = $mysqli->prepare("SELECT usuario_id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Gerar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        // Salvar código no banco de dados
        $stmt = $mysqli->prepare("INSERT INTO recuperacao_senha (email, codigo, expiracao) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $codigo, $expiracao);
        $stmt->execute();
        $stmt->close();
        
        // Enviar e-mail (simulação - na prática use PHPMailer ou similar)
        $assunto = "Código de recuperação de senha";
        $mensagem = "Seu código de recuperação é: $codigo\n\nVálido por 30 minutos.";
        mail($email, $assunto, $mensagem);
        
        // Armazenar e-mail na sessão para próxima etapa
        $_SESSION['email_recuperacao'] = $email;
        
        // Redirecionar para página de validação
        header('Location: validar_codigo.php');
        exit;
    } else {
        $erro = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Agenda Inteligente</title>
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
        
        .sucesso {
            color: #27ae60;
            background-color: #d5f5e3;
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
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" action="">
            <span class="titulo">Recuperar Senha</span>
            
            <?php if (isset($erro)) : ?>
                <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="email">Digite seu e-mail cadastrado</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="button-container">
                <button type="submit" class="button">Enviar Código</button>
            </div>
            
            <div class="login-link">
                Lembrou sua senha? <a href="Login.php">Faça login</a>
            </div>
        </form>
    </div>
</body>
</html>