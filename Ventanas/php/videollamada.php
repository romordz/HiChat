<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videollamada</title>
    <link rel="icon" href="images/icon.png">
    <link rel="stylesheet" href="css/videollamada.css">
</head>
<body>

    <!-- Contenedor de la videollamada -->
    <div id="videoContainer">

        <!-- Encabezado con usuario y botón de colgar -->
        <div id="videoHeader">
            <img src="images/user.png" alt="Usuario" id="videoUserImg">
            <h2>Videollamada con Usuario</h2>
            <button id="colgarLlamada">❌</button>
        </div>

        <!-- Área de video -->
        <div id="videoScreen">
            <video id="videoLocal" autoplay muted></video>
            <video id="videoRemoto" autoplay></video>
        </div>

        <!-- Controles de la llamada -->
        <div id="videoControls">
            <button id="toggleCam">📷 Cámara</button>
            <button id="toggleMic">🎤 Micrófono</button>
            <button id="colgarLlamadaBottom">❌ Finalizar</button>
        </div>

    </div>

</body>
</html>
