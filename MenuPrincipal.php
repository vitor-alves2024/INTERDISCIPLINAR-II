

<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mysqli = new mysqli('localhost', 'root', '', 'agendafast');


// Buscar informações do usuário
$stmt = $mysqli->prepare("SELECT nome FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Buscar próximos compromissos
$stmt = $mysqli->prepare("SELECT evento_id, titulo, data_inicio, localizacao 
                         FROM eventos 
                         WHERE usuario_id = ? AND data_inicio >= NOW() 
                         ORDER BY data_inicio ASC LIMIT 5");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$compromissos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - Agenda Inteligente</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --background-color: #f8f9fa;
            --card-color: #ffffff;
            --text-color: #333333;
            --border-color: #e0e0e0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
        }
        
        .sidebar {
            background-color: var(--card-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-menu li {
            margin-bottom: 10px;
        }
        
        .nav-menu a {
            display: block;
            padding: 10px;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .content-area {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .card {
            background-color: var(--card-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-title {
            margin: 0;
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-record {
            background-color: #2ecc71;
        }
        
        .btn-record:hover {
            background-color: #27ae60;
        }
        
        .event-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .event-item {
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .event-item:last-child {
            border-bottom: none;
        }
        
        .event-info h3 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        .event-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .event-time {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .audio-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            gap: 15px;
        }
        
        .mic-icon {
            width: 80px;
            height: 80px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .mic-icon:hover {
            transform: scale(1.05);
        }
        
        .mic-icon i {
            color: white;
            font-size: 30px;
        }
        
        .recording-status {
            font-size: 0.9rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($usuario['nome'], 0, 1)) ?></div>
                <div>
                    <h2>Olá, <?= htmlspecialchars($usuario['nome']) ?></h2>
                    <p>Bem-vindo à sua agenda inteligente</p>
                </div>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
        </header>
        
        <div class="main-content">
            <aside class="sidebar">
                <ul class="nav-menu">
                    <li><a href="#" class="active"><i class="fas fa-home"></i> Início</a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> Calendário</a></li>
                    <li><a href="#"><i class="fas fa-bell"></i> Lembretes</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Configurações</a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> Ajuda</a></li>
                </ul>
            </aside>
            
            <main class="content-area">
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Adicionar Compromisso por Áudio</h2>
                    </div>
                    <div class="audio-container">
                        <div class="mic-icon" id="micButton">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <p class="recording-status">Clique no microfone e fale seu compromisso</p>
                        <p>Exemplo: "Reunião com equipe amanhã às 15h no escritório"</p>
                    </div>
                </section>
                
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Próximos Compromissos</h2>
                        <a href="compromissos.php" class="btn">Ver Todos</a>
                    </div>
                    
                    <ul class="event-list">
                        <?php if ($compromissos->num_rows > 0): ?>
                            <?php while ($compromisso = $compromissos->fetch_assoc()): ?>
                                <li class="event-item">
                                    <div class="event-info">
                                        <h3><?= htmlspecialchars($compromisso['titulo']) ?></h3>
                                        <p>
                                            <?= $compromisso['localizacao'] ? htmlspecialchars($compromisso['localizacao']) : 'Sem localização' ?>
                                        </p>
                                    </div>
                                    <div class="event-time">
                                        <?= date('H:i', strtotime($compromisso['data_inicio'])) ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="event-item">
                                <p>Nenhum compromisso agendado</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </section>
            </main>
        </div>
    </div>

    <script>
        // Controle do gravador de áudio
        document.getElementById('micButton').addEventListener('click', function() {
            // Iniciar gravação de áudio
            alert('Gravação iniciada! Em uma implementação real, isso capturaria o áudio e enviaria para o servidor.');
            
            // Simulação do processamento
            setTimeout(() => {
                const shouldAdd = confirm('Gravação concluída. Deseja adicionar este compromisso?\n"Reunião com equipe amanhã às 15h no escritório"');
                
                if (shouldAdd) {
                    alert('Compromisso adicionado com sucesso!');
                    // Recarregar a página para mostrar o novo compromisso
                    window.location.reload();
                }
            }, 2000);
        });

        // Exemplo de integração com Web Audio API (para implementação real)
        /*
        let mediaRecorder;
        let audioChunks = [];
        
        async function startRecording() {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                await sendAudioToServer(audioBlob);
                audioChunks = [];
            };
            
            mediaRecorder.start();
        }
        
        function stopRecording() {
            mediaRecorder.stop();
        }
        
        async function sendAudioToServer(audioBlob) {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.wav');
            
            try {
                const response = await fetch('process_audio.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Compromisso adicionado: ' + result.event_title);
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        */
    </script>
</body>
</html>