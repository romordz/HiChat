<?php
include '../backend/process_chat.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo htmlspecialchars($nombre_usuario); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/chat.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <div id="chatContainer">
        <div id="chatHeader">
            <a href="perfil.php?user_id=<?php echo $user_id; ?>">
                <div class="marco-perfil-chat" style="background-image: url('../images/<?php echo htmlspecialchars($marco_perfil); ?>.png');">
                    <?php if (!empty($foto_perfil)): ?>
                        <img src="data:image/jpeg;base64,<?php echo $foto_perfil; ?>" alt="Usuario" id="chatUserImg">
                    <?php else: ?>
                        <img src="../images/default.webp" alt="Usuario" id="chatUserImg">
                    <?php endif; ?>
                </div>
            </a>
            <h2>Chat con <?php echo htmlspecialchars($nombre_usuario); ?></h2>
            <button id="cerrarChat">X</button>
        </div>

        <div id="chatMessages"></div>

        <div id="chatInput">
            <input type="text" id="mensaje" placeholder="Escribe un mensaje...">
            <button id="enviarMensaje">Enviar</button>
        </div>

        <div id="musica">
            <audio id="audio" loop autoplay>
                <source src="../sounds/chats.mp3" type="audio/mp3">
                Tu navegador no soporta audio.
            </audio>
            <button id="btnMusica">🔊 Silenciar</button>
            <button id="btnUbicacion">📌 Enviar ubicación</button>
            <button id="btnVideollamada">📹 Videollamada</button>
            <button id="btnDocumento">📄 Enviar documento</button>
            <input type="file" id="documento" style="display: none;">
        </div>

        <div id="popupVideollamada" class="popup" style="display: none;">
            <div class="popup-content">
                <span class="close" id="closeVideollamada">&times;</span>
                <h2>Videollamada</h2>
                <div id="videoContainer">
                    <video id="videoLocal" autoplay muted></video>
                    <video id="videoRemoto" autoplay></video>
                </div>
                <div id="videoControls">
                    <button id="toggleCam">📷 Cámara</button>
                    <button id="toggleMic">🎤 Micrófono</button>
                    <button id="endCall">❌ Finalizar</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        const chatId = <?php echo json_encode($chat_id); ?>;
        const otherUserId = <?php echo json_encode($user_id); ?>;
    </script>
    <script src="../js/chat.js"></script>
    <script type="module" src="../js/loadHeader.js"></script>
    <script>
        document.getElementById("btnVideollamada").addEventListener("click", function () {
            const chatId = <?php echo json_encode($chat_id); ?>;
            const otherUserId = <?php echo json_encode($user_id); ?>;
            const currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;

            // Enviar mensaje de invitación a videollamada
            fetch('../backend/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    chat_id: chatId,
                    contenido: JSON.stringify({ 
                        tipo: 'videollamada', 
                        nombre_usuario: '<?php echo htmlspecialchars($nombre_usuario); ?>', // Nombre del usuario que RECIBE la llamada
                        url: `videollamada.php?chat_id=${chatId}&user_id=${currentUserId}&initiator=true` 
                    })
                })
            }).then(response => {
                if (response.ok) {
                    // Redirigir al emisor a la página de videollamada inmediatamente
                    window.location.href = `videollamada.php?chat_id=${chatId}&user_id=${currentUserId}&initiator=true`;
                } else {
                    alert("Error al enviar la invitación.");
                }
            });
        });
    </script>
</body>
</html>