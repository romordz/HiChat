<?php
include '../backend/process_mis_chats.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Chats</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/grupos.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <!-- Mensaje de bienvenida -->
    <div id="bienvenida">Mis Chats</div>

    <!-- Lista de chats -->
    <div id="listaGrupos">
        <?php foreach ($chats as $chat): ?>
            <a href="chat.php?user_id=<?php echo htmlspecialchars($chat['user_id']); ?>" class="grupo">
                <div class="marco-perfil-chat" style="background-image: url('../images/<?php echo htmlspecialchars($chat['marco_perfil']); ?>.png');">
                    <?php if ($chat['foto_perfil']): ?>
                        <img src="data:image/jpeg;base64,<?php echo ($chat['foto_perfil']); ?>" alt="Usuario" class="chatUserImg">
                    <?php else: ?>
                        <img src="../images/default.webp" alt="Usuario" class="chatUserImg">
                    <?php endif; ?>
                </div>
                <div class="info">
                    <h3><?php echo htmlspecialchars($chat['nombre_usuario']); ?></h3>
                    <?php if ($chat['ultimo_mensaje']): ?>
                        <p><?php echo htmlspecialchars($chat['nombre_usuario_mensaje']); ?>: <?php echo htmlspecialchars($chat['ultimo_mensaje']); ?></p>
                        <p><?php echo htmlspecialchars($chat['fecha_ultimo_mensaje']); ?></p>
                    <?php else: ?>
                        <p>No hay mensajes aún</p>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <script type="module" src="../js/loadHeader.js"></script>
</body>
</html>