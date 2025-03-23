<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu De Inicio - Chat Player ver 0.1</title>
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>

    <!-- Mensaje de bienvenida -->
    <div id="bienvenida">
        <h2>Bienvenido, <?php echo $_SESSION['nombre_usuario'] ?? 'Usuario'; ?></h2>
    </div>

    <!-- Barra de búsqueda de usuarios -->
    <div id="searchContainer">
        <input type="text" id="searchInput" placeholder="Buscar usuarios...">
        <div id="searchResults"></div>
    </div>

    <!-- Reproductor de música -->
    <div id="musica">
        <audio id="audio" loop>
            <source src="../sounds/noti3ds.mp3" type="audio/mp3">
            Tu navegador no soporta audio.
        </audio>
        <button id="btnMusica">🔊 Silenciar</button>
    </div>

    <!-- Galería de imágenes con enlaces -->
    <div id="galeria">
        <button id="prev">⬅</button>
        <a id="enlaceImagen1" href="chat.html">
            <img id="imagenActual1" src="../images/banner.jpg" alt="Imagen 1">
        </a>
        <a id="enlaceImagen2" href="chat.html" style="display: none;">
            <img id="imagenActual2" src="../images/fondo.png" alt="Imagen 2">
        </a>
        <a id="enlaceImagen3" href="chat.html" style="display: none;">
            <img id="imagenActual3" src="../images/banner.jpg" alt="Imagen 3">
        </a>
        <a id="enlaceImagen4" href="chat.html" style="display: none;">
            <img id="imagenActual4" src="../images/fondo.png" alt="Imagen 4">
        </a>
        <a id="enlaceImagen5" href="run.html" style="display: none;">
            <img id="imagenActual5" src="../images/run.png" alt="Imagen 5">
        </a>
        <button id="next">➡</button>
    </div>

    <script src="../js/musicamenu.js"></script>
    <script src="../js/search.js"></script>
    <script type="module" src="../js/loadHeader.js"></script>
    <script type="module">
        const images = document.querySelectorAll('#galeria a');
        let currentIndex = 0;

        document.getElementById('prev').addEventListener('click', () => {
            images[currentIndex].style.display = 'none';
            currentIndex = (currentIndex - 1 + images.length) < 0 ? images.length - 1 : (currentIndex - 1 + images.length) % images.length;
            images[currentIndex].style.display = 'block';
        });

        document.getElementById('next').addEventListener('click', () => {
            images[currentIndex].style.display = 'none';
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].style.display = 'block';
        });
    </script>
</body>
</html>
