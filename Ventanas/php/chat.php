<?php
include '../backend/process_chat.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo htmlspecialchars($nombre_usuario); ?></title>
    <link rel="stylesheet" href="../css/chat.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <!-- Contenedor del chat -->
    <div id="chatContainer">

        <!-- Encabezado del chat -->
        <div id="chatHeader">
            <a href="perfil.php?user_id=<?php echo $user_id; ?>">
                <div class="marco-perfil-chat" style="background-image: url('../images/<?php echo htmlspecialchars($marco_perfil); ?>.png');">
                    <?php if (!empty($foto_perfil)): ?>
                        <!-- Mostrar la foto de perfil del usuario -->
                        <img src="data:image/jpeg;base64,<?php echo $foto_perfil; ?>" alt="Usuario" id="chatUserImg">
                    <?php else: ?>
                        <!-- Mostrar la imagen predeterminada -->
                        <img src="../images/default.webp" alt="Usuario" id="chatUserImg">
                    <?php endif; ?>
                </div>
            </a>
            <h2>Chat con <?php echo htmlspecialchars($nombre_usuario); ?></h2>
            <button id="cerrarChat">X</button>
        </div>

        <!-- Área de mensajes -->
        <div id="chatMessages"></div>

        <!-- Área de entrada de texto -->
        <div id="chatInput">
            <input type="text" id="mensaje" placeholder="Escribe un mensaje...">
            <button id="enviarMensaje">Enviar</button>
        </div>

        <!-- Música y botón de silenciar -->
        <div id="musica">
            <audio id="audio" loop autoplay>
                <source src="../sounds/chats.mp3" type="audio/mp3">
                Tu navegador no soporta audio.
            </audio>
            <button id="btnMusica">🔊 Silenciar</button>
            <button id="btnUbicacion">📌 Enviar ubicación</button>
        </div>

    </div>

    <script>
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        const chatId = <?php echo json_encode($chat_id); ?>;
        const otherUserId = <?php echo json_encode($user_id); ?>;
    </script>
    <script src="../js/chat.js"></script>
    <script type="module" src="../js/loadHeader.js"></script>
</body>
</html>