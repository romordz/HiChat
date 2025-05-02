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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Burbujas flotantes -->
    <div class="bubbles"></div>

    <!-- Header placeholder -->
    <div id="header-placeholder"></div>

    <!-- Mensaje de bienvenida -->
    <div id="bienvenida">
        <h2>Bienvenido, <?php echo $_SESSION['nombre_usuario'] ?? 'Usuario'; ?></h2>
    </div>

    <!-- Barra de búsqueda de usuarios -->
    <div id="searchContainer">
        <input type="text" id="searchInput" placeholder="🔍 Buscar usuarios...">
        <div id="searchResults"></div>
    </div>

    <!-- Reproductor de música -->
    <div id="musica">
        <audio id="audio" loop>
            <source src="../sounds/noti3ds.mp3" type="audio/mp3">
            Tu navegador no soporta audio.
        </audio>
        <button id="btnMusica">🎵 Música</button>
    </div>

    <!-- Galería de imágenes con enlaces -->
    <div id="galeria">
        <button id="prev">⬅</button>
        <a id="enlaceImagen1" href="mis_chats.php">
            <img id="imagenActual1" src="../images/banner.jpg" alt="Mis Chats">
        </a>
        <a id="enlaceImagen2" href="grupos.php" style="display: none;">
            <img id="imagenActual2" src="../images/chatibanner.jpg" alt="Mis Grupos">
        </a>
        <a id="enlaceImagen3" href="Tareas.php" style="display: none;">
            <img id="imagenActual3" src="../images/fondo.png" alt="Mis Tareas">
        </a>
        <a id="enlaceImagen4" href="biografia.php" style="display: none;">
            <img id="imagenActual4" src="../images/chati.png" alt="Conoce a Chati">
        </a>
        <a id="enlaceImagen5" href="Recompensas.php" style="display: none;">
            <img id="imagenActual5" src="../images/Marco Dorado.png" alt="Recompensas">
        </a>
        <button id="next">➡</button>
    </div>

    <script src="../js/musicamenu.js"></script>
    <script src="../js/search.js"></script>
    <script type="module" src="../js/loadHeader.js"></script>
    <script>
        // Crear burbujas flotantes
        function createBubbles() {
            const bubblesContainer = document.querySelector('.bubbles');
            const bubbleCount = 15;
            
            for (let i = 0; i < bubbleCount; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                
                // Tamaño aleatorio
                const size = Math.random() * 100 + 50;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Posición aleatoria
                bubble.style.left = `${Math.random() * 100}vw`;
                bubble.style.top = `${Math.random() * 100}vh`;
                
                // Retraso aleatorio en la animación
                bubble.style.animationDelay = `${Math.random() * 8}s`;
                
                bubblesContainer.appendChild(bubble);
            }
        }

        // Controles del carrusel
        const images = document.querySelectorAll('#galeria a');
        let currentIndex = 0;

        function showImage(index, direction) {
            images[currentIndex].style.display = 'none';
            currentIndex = index;
            const nextImage = images[currentIndex];
            nextImage.style.display = 'block';
            
            // Aplicar animación según la dirección
            nextImage.style.animation = direction === 'right' ? 
                'slideLeft 0.5s ease forwards' : 
                'slideRight 0.5s ease forwards';
        }

        document.getElementById('prev').addEventListener('click', () => {
            const newIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(newIndex, 'left');
        });

        document.getElementById('next').addEventListener('click', () => {
            const newIndex = (currentIndex + 1) % images.length;
            showImage(newIndex, 'right');
        });

        // Inicializar burbujas cuando se carga la página
        //document.addEventListener('DOMContentLoaded', createBubbles);
    </script>
</body>
</html>
