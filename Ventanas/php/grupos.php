<?php
include '../backend/process_grupos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Grupos</title>
    <link rel="stylesheet" href="../css/grupos.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <div id="bienvenida">Mis Grupos</div>
    
    <div id="listaGrupos">
        <?php if (empty($grupos)): ?>
            <p>No se encontraron grupos.</p>
        <?php else: ?>
            <?php foreach ($grupos as $grupo): ?>
                <a href="chat_grupo.php?grupo_id=<?php echo htmlspecialchars($grupo['id']); ?>" class="grupo">
                    <div class="info">
                        <h3><?php echo htmlspecialchars($grupo['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($grupo['descripcion']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button id="crearGrupoBtn">Crear Grupo</button>

    <div id="crearGrupoPopup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <h2>Crear Grupo</h2>
            <form id="crearGrupoForm">
                <label for="nombreGrupo">Nombre del Grupo:</label>
                <input type="text" id="nombreGrupo" name="nombreGrupo" required>
                
                <label for="descripcionGrupo">Descripción:</label>
                <textarea id="descripcionGrupo" name="descripcionGrupo" required></textarea>
                
                <label for="agregarMiembros">Agregar Miembros:</label>
                <input type="text" id="buscarUsuarios" placeholder="Buscar usuarios...">
                <div id="resultadosBusqueda"></div>
                <div id="selectedUsers"></div>
                
                <button type="submit">Crear</button>
            </form>
        </div>
    </div>

    <script type="module" src="../js/loadHeader.js"></script>
    <script src="../js/grupos.js"></script>
</body>
</html>