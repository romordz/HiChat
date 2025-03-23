<?php
include '../backend/process_chat_grupo.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat del Grupo: <?php echo htmlspecialchars($nombre_grupo); ?></title>
    <link rel="stylesheet" href="../css/chat.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <!-- Contenedor del chat -->
    <div id="chatContainer">

        <!-- Encabezado del chat -->
        <div id="chatHeader">
            <h2>Chat del Grupo: <?php echo htmlspecialchars($nombre_grupo); ?></h2>
            <button id="cerrarChat">X</button>
            <button id="verMiembros">Ver Miembros</button>
            <button id="gestionarTareas">Gestionar Tareas</button>
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

    <!-- Popup para ver miembros -->
    <div id="miembrosPopup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <h2>Miembros del Grupo</h2>
            <div id="listaMiembros"></div>
        </div>
    </div>

    <!-- Popup para gestionar tareas -->
    <div id="tareasPopup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <h2>Gestionar Tareas</h2>
            <form id="crearTareaForm">
                <label for="tituloTarea">Título de la Tarea:</label>
                <input type="text" id="tituloTarea" name="tituloTarea" required>
                
                <label for="descripcionTarea">Descripción:</label>
                <textarea id="descripcionTarea" name="descripcionTarea" required></textarea>
                
                <label for="fechaLimite">Fecha Límite:</label>
                <input type="date" id="fechaLimite" name="fechaLimite" required>
                
                <button type="submit">Crear Tarea</button>
            </form>
            <br>
            <div id="listaTareas"></div>
        </div>
    </div>

    <script>
        const grupoId = <?php echo json_encode($grupo_id); ?>;
        const userId = <?php echo json_encode($current_user_id); ?>;
    </script>
    <script src="../js/chat_grupo.js"></script>
    <script type="module" src="../js/loadHeader.js"></script>
</body>
</html>