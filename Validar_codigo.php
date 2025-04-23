<?php
session_start();

if (!isset($_SESSION['email_recuperacao'])) {
    header('Location: RecuperarSenha.php');
    exit;
}
// Conexão com o banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'agendafast');



$email = $_SESSION['email_recuperacao'];
$erro = '';

// Buscar o código gerado (apenas para exibição em desenvolvimento)
$codigo_gerado = '';
$stmt = $mysqli->prepare("SELECT codigo FROM recuperacao_senha 
                         WHERE email = ? ORDER BY data_criacao DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $codigo_gerado = $row['codigo'];
}

// Processar validação do código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = trim($_POST['codigo']);
    
    $stmt = $mysqli->prepare("SELECT id FROM recuperacao_senha 
                             WHERE email = ? AND codigo = ? 
                             AND expiracao > NOW() AND usado = 0");
    $stmt->bind_param("ss", $email, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $dados = $result->fetch_assoc();
        
        // Marcar código como usado
        $stmt = $mysqli->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = ?");
        $stmt->bind_param("i", $dados['id']);
        $stmt->execute();
        
        $_SESSION['codigo_validado'] = true;
        header('Location: nova_senha.php');
        exit;
    } else {
        $erro = "Código inválido ou expirado. Por favor, tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Código - Agenda Inteligente</title>
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
            font-size: 1.5rem;
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
        
        .info-box {
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .dev-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #856404;
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
            text-align: center;
            letter-spacing: 5px;
            font-size: 1.5rem;
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
    </style>
</head>
<body>
    <div class="container">
        <span class="titulo">Verificação de Código</span>
        
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <p>Enviamos um código de 6 dígitos para <strong><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
            <p>Por favor, verifique sua caixa de entrada e digite o código abaixo para continuar com a redefinição de senha.</p>
        </div>
        
        <!-- Apenas para desenvolvimento/testes -->
        <?php if (!empty($codigo_gerado)): ?>
            <div class="dev-note">
                <p><strong>ATENÇÃO (MODO DESENVOLVIMENTO):</strong></p>
                <p>Para fins de teste, o código gerado é: <strong><?= $codigo_gerado ?></strong></p>
                <p>Em produção, este bloco não seria exibido e o código seria enviado apenas por e-mail.</p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="input-group">
                <label for="codigo">Digite o código de verificação</label>
                <input type="text" id="codigo" name="codigo" maxlength="6" required autofocus
                       pattern="[0-9]{6}" title="Por favor, digite exatamente 6 dígitos numéricos">
            </div>
            
            <div class="button-container">
                <button type="submit" class="button">Validar Código</button>
            </div>
        </form>
    </div>

    <script>
        // Auto avançar entre dígitos do código
        document.getElementById('codigo').addEventListener('input', function() {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
        
        // Mostrar alerta com o código (apenas em desenvolvimento)
        <?php if (!empty($codigo_gerado)): ?>
            alert("MODO DESENVOLVIMENTO:\n\nPara testes, use o código: <?= $codigo_gerado ?>\n\nEm produção, este alerta não aparecerá e o código será enviado apenas por e-mail.");
        <?php endif; ?>
    </script>
</body>
</html>