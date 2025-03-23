<?php
session_start();
?>
<link rel="stylesheet" href="../css/header.css">
<header id="menu">
    <div id="logo">
        <img src="../images/logogrande.png" alt="Logo">
        <img id="chati" src="../images/chati.png" alt="Chati">
    </div>
    <ul>
        <li class="item-r"><a href="menu.php">Inicio</a></li>
        <li><a href="mis_chats.php">Mis Chats</a></li>
        <li><a href="grupos.php">Mis Grupos</a></li>
        <li><a href="Tareas.php">Mis Tareas</a></li>
        <!-- Botón de perfil con dropdown -->
        <li class="perfil">
            <a href="#" id="btnPerfil">
                <div class="marco-perfil-header" style="background-image: url('../images/<?php echo htmlspecialchars($_SESSION['marco_perfil'] ?? 'default'); ?>.png');">
                    <?php
                    $foto_perfil = $_SESSION['foto_perfil'] ?? null;
                    if ($foto_perfil) {
                        echo '<img src="data:image/jpeg;base64,' . $foto_perfil . '" alt="Perfil" id="perfilIcono">';
                    } else {
                        echo '<img src="../images/default.webp" alt="Perfil" id="perfilIcono">';
                    }
                    ?>
                </div>
                <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
            </a>
            <!-- Dropdown -->
            <ul class="dropdown" style="display: none;">
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="Recompensas.php">Recompensas</a></li>
                <li><a href="inicioSesion.php">Cerrar Sesión</a></li>
            </ul>
        </li>
    </ul>
</header>