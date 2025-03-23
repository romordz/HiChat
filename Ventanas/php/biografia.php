<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conoce a Chati,La mascota de Hi Chat Player</title>
    <link rel="icon" href="images/chati.png">
    <link rel="stylesheet" href="css/biografia.css">
</head>
<body>

    <!-- Contenedor principal -->
    <div id="bioContainer">

        <!-- GIF del personaje -->
        <div id="gifContainer">
            <img src="images/chati2.gif" alt="GIF del Personaje">
        </div>

        <!-- Información del personaje -->
        <div id="bioText">
            <h1>CHATALINO ROMAN VILLA CUELLAR</h1>
            <p><strong>Edad:</strong> 100 años</p>
            <p><strong>Ocupación:</strong> POLLITO DE MENSAJERIA</p>
            <p><strong>Historia:</strong> Un Pollito que mandaba mensajes durante Las Guerras Mundiales,fue adoptado por 2 solados desertores y lo criaron con mucho amor para Ayudar a la resistencia</p>
        </div>

        <!-- Controles: Música y Regresar -->
        <div id="controls">
            <button id="btnMusica">🎵 Activar Música</button>
            <button id="btnRegresar">⬅ Regresar</button>
        </div>

        <!-- Música de fondo -->
        <audio id="audio" loop>
            <source src="sounds/3dschati.mp3" type="audio/mp3">
            Tu navegador no soporta audio.
        </audio>

    </div>

    <script src="js/biografia.js"></script>

</body>
</html>
