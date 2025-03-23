<?php
include '../backend/process_profile.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <div id="header-placeholder"></div>
    
    <div id="perfil">
        <div class="marco-perfil" style="background-image: url('../images/<?php echo htmlspecialchars($marco_perfil); ?>.png');">
            <?php
            if ($foto_perfil) {
                echo '<img src="data:image/jpeg;base64,' . $foto_perfil . '" alt="Foto de perfil" id="fotoPerfil">';
            } else {
                echo '<img src="../images/perfil.png" alt="Foto de perfil" id="fotoPerfil">';
            }
            ?>
        </div>
        <h2><?php echo htmlspecialchars($nombre_usuario); ?></h2>
        <p>Correo: <?php echo htmlspecialchars($email); ?></p>
        <p>Miembro desde: <?php echo date('F Y', strtotime($fecha_registro)); ?></p>
        <?php if ($user_id == $_SESSION['user_id']): ?>
            <button id="editarPerfil">Editar Perfil</button>
            <button id="cambiarMarco">Cambiar Marco</button>
        <?php endif; ?>
    </div>

    <div id="infoPerfil">
        <h3>Estadísticas</h3>
        <ul>
            <li>Chats activos: <?php echo htmlspecialchars($num_chats); ?></li>
            <li>Grupos: <?php echo htmlspecialchars($num_grupos); ?></li>
            <li>Tareas completadas: <?php echo htmlspecialchars($tareas_completadas); ?></li>
        </ul>
    </div>

    <!-- Formulario de edición de perfil -->
    <?php if ($user_id == $_SESSION['user_id']): ?>
        <div id="formEditarPerfil" class="form-container" style="display: none;">
            <form action="perfil.php" method="POST" enctype="multipart/form-data">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>" required>
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/jpeg">
                <div class="form-buttons">
                    <button type="submit">Guardar Cambios</button>
                    <button type="button" id="cancelarEdicion">Cancelar</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Popup para cambiar marco -->
    <div id="popupCambiarMarco" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close">&times;</span>
            <h3>Selecciona un Marco</h3>
            <div id="marcosGrid"></div>
        </div>
    </div>

    <script type="module" src="../js/loadHeader.js"></script>
    <script>
        <?php if ($user_id == $_SESSION['user_id']): ?>
            document.getElementById('editarPerfil').addEventListener('click', function() {
                document.getElementById('perfil').style.display = 'none';
                document.getElementById('formEditarPerfil').style.display = 'block';
            });

            document.getElementById('cancelarEdicion').addEventListener('click', function() {
                document.getElementById('formEditarPerfil').style.display = 'none';
                document.getElementById('perfil').style.display = 'block';
            });

            document.getElementById('cambiarMarco').addEventListener('click', function() {
                fetch('../backend/fetch_user_rewards.php')
                    .then(response => response.json())
                    .then(rewards => {
                        const marcosGrid = document.getElementById('marcosGrid');
                        marcosGrid.innerHTML = '';
                        rewards.forEach(reward => {
                            const marcoElement = document.createElement('div');
                            marcoElement.classList.add('marco');
                            marcoElement.style.backgroundImage = `url('../images/${reward.nombre}.png')`;
                            marcoElement.addEventListener('click', () => {
                                fetch('../backend/update_profile_frame.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `marco_perfil=${reward.nombre}`
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.status === 'success') {
                                        alert('Marco actualizado exitosamente');
                                        location.reload();
                                    } else {
                                        console.error('Error updating frame:', result.message);
                                    }
                                })
                                .catch(error => console.error('Error updating frame:', error));
                            });
                            marcosGrid.appendChild(marcoElement);
                        });
                        document.getElementById('popupCambiarMarco').style.display = 'block';
                    })
                    .catch(error => console.error('Error fetching rewards:', error));
            });

            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('popupCambiarMarco').style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == document.getElementById('popupCambiarMarco')) {
                    document.getElementById('popupCambiarMarco').style.display = 'none';
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
