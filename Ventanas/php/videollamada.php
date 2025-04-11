<?php
session_start();
$chat_id = $_GET['chat_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$chat_id || !$user_id) {
    die("Faltan parámetros necesarios para la videollamada.");
}

include '../backend/db_connection.php';

// Obtener el ID del otro usuario en el chat
$stmt = $conn->prepare("SELECT usuario1_id, usuario2_id FROM Chats WHERE id = ?");
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$stmt->bind_result($usuario1_id, $usuario2_id);
$stmt->fetch();
$stmt->close();

// Determinar cuál es el otro usuario
$other_user_id = ($usuario1_id == $_SESSION['user_id']) ? $usuario2_id : $usuario1_id;

// Obtener los datos del otro usuario
$stmt = $conn->prepare("SELECT nombre_usuario, foto_perfil, marco_perfil FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $other_user_id);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $foto_perfil, $marco_perfil);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videollamada</title>
    <link rel="icon" href="images/icon.png">
    <link rel="stylesheet" href="../css/videollamada.css">
</head>
<body>

    <div id="videoContainer">
        <div id="videoHeader">
            <div class="marco-perfil-header" style="background-image: url('../images/<?php echo htmlspecialchars($marco_perfil); ?>.png');">
                <?php if (!empty($foto_perfil)): ?>
                    <img src="data:image/jpeg;base64,<?php echo $foto_perfil; ?>" alt="Usuario" id="videoUserImg">
                <?php else: ?>
                    <img src="../images/default.webp" alt="Usuario" id="videoUserImg">
                <?php endif; ?>
            </div>
            <h2>Videollamada con <?php echo htmlspecialchars($nombre_usuario); ?></h2>
            <button id="colgarLlamada">❌</button>
        </div>

        <div id="videoScreen">
            <video id="videoLocal" autoplay muted></video>
            <video id="videoRemoto" autoplay></video>
            <div id="connectionStatus">Conectando...</div>
        </div>

        <div id="videoControls">
            <button id="toggleCam">📷 Cámara</button>
            <button id="toggleMic">🎤 Micrófono</button>
            <button id="colgarLlamadaBottom">❌ Finalizar</button>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const videoLocal = document.getElementById("videoLocal");
            const videoRemoto = document.getElementById("videoRemoto");
            const toggleCam = document.getElementById("toggleCam");
            const toggleMic = document.getElementById("toggleMic");
            const colgarLlamada = document.getElementById("colgarLlamada");
            const colgarLlamadaBottom = document.getElementById("colgarLlamadaBottom");
            const connectionStatus = document.getElementById("connectionStatus");
            
            // Obtener parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const chatId = urlParams.get('chat_id');
            const userId = urlParams.get('user_id');

            let localStream;
            let peerConnection;
            let signalingServer;
            let isInitiator = false;
            let reconnectAttempts = 0;
            const MAX_RECONNECT_ATTEMPTS = 5;

            // Configuración para WebRTC
            const configuration = {
                iceServers: [
                    { urls: "stun:stun.l.google.com:19302" },
                    { urls: "stun:stun1.l.google.com:19302" },
                    { urls: "stun:stun2.l.google.com:19302" }
                    // Añade servidores TURN si es necesario para conexiones más robustas
                ]
            };

            // Conectar al servidor de señalización
            function connectToSignalingServer() {
                connectionStatus.textContent = "Conectando al servidor...";
                signalingServer = new WebSocket("wss://7827-2806-2f0-42c0-d0ab-55e6-4ff6-557f-7f78.ngrok-free.app"); //aqui
                
                signalingServer.onopen = () => {
                    console.log("WebSocket connection established.");
                    connectionStatus.textContent = "Conexión establecida, iniciando videollamada...";
                    
                    // Enviar información del chat y usuario
                    signalingServer.send(JSON.stringify({
                        type: "register",
                        chatId: chatId,
                        userId: userId
                    }));
                    
                    // Iniciar el proceso de WebRTC
                    startWebRTC();
                };

                signalingServer.onerror = (error) => {
                    console.error("WebSocket error:", error);
                    connectionStatus.textContent = "Error de conexión. Reintentando...";
                    handleReconnect();
                };

                signalingServer.onclose = (event) => {
                    console.warn("WebSocket connection closed:", event);
                    connectionStatus.textContent = "Conexión cerrada. Reintentando...";
                    handleReconnect();
                };

                signalingServer.onmessage = handleSignalingMessage;
            }

            // Función para manejar intentos de reconexión
            function handleReconnect() {
                if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                    reconnectAttempts++;
                    setTimeout(() => {
                        connectionStatus.textContent = `Reintentando conexión (${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS})...`;
                        connectToSignalingServer();
                    }, 3000); // Esperar 3 segundos antes de reconectar
                } else {
                    connectionStatus.textContent = "No se pudo establecer la conexión. Por favor, intente más tarde.";
                }
            }

            // Procesar mensajes del servidor de señalización
            function handleSignalingMessage(event) {
                try {
                    // Asegurarse de que el mensaje es una cadena
                    const messageData = typeof event.data === 'string' ? event.data : event.data.toString();
                    console.log("Mensaje raw recibido:", messageData);
                    
                    const data = JSON.parse(messageData);
                    console.log("Mensaje parseado:", data);

                    switch (data.type) {
                        case "connection":
                            console.log("Connection ID received:", data.clientId);
                            break;
                            
                        case "offer":
                            console.log("Received offer. Setting remote description.");
                            peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer))
                                .then(() => {
                                    return peerConnection.createAnswer();
                                })
                                .then(answer => {
                                    return peerConnection.setLocalDescription(answer)
                                        .then(() => {
                                            signalingServer.send(JSON.stringify({
                                                type: "answer",
                                                answer: answer,
                                                chatId: chatId,
                                                userId: userId
                                            }));
                                        });
                                })
                                .catch(error => {
                                    console.error("Error handling offer:", error);
                                    connectionStatus.textContent = "Error en la conexión de video.";
                                });
                            break;

                        case "answer":
                            console.log("Received answer. Setting remote description.");
                            peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer))
                                .catch(error => {
                                    console.error("Error setting remote description:", error);
                                    connectionStatus.textContent = "Error en la conexión de video.";
                                });
                            break;

                        case "candidate":
                            console.log("Received ICE candidate:", data.candidate);
                            if (data.candidate) {
                                peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                                    .catch(error => {
                                        console.error("Error adding ICE candidate:", error);
                                    });
                            }
                            break;
                            
                        default:
                            console.warn("Unknown message type received:", data.type);
                    }
                } catch (error) {
                    console.error("Error processing message:", error, "Raw message:", event.data);
                }
            }

            // Iniciar WebRTC
            function startWebRTC() {
                // Solicitar acceso a cámara y micrófono
                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                    .then(stream => {
                        console.log("Local media stream obtained.");
                        localStream = stream;
                        videoLocal.srcObject = stream;
                        connectionStatus.textContent = "Cámara y micrófono activados. Estableciendo conexión...";

                        // Crear conexión peer
                        peerConnection = new RTCPeerConnection(configuration);

                        // Añadir tracks locales
                        localStream.getTracks().forEach(track => {
                            peerConnection.addTrack(track, localStream);
                        });

                        // Manejar stream remoto
                        peerConnection.ontrack = (event) => {
                            console.log("Remote track received.");
                            if (event.streams && event.streams[0]) {
                                videoRemoto.srcObject = event.streams[0];
                                connectionStatus.textContent = "Conectado!";
                                connectionStatus.style.display = "none";
                            }
                        };

                        // Manejar candidatos ICE
                        peerConnection.onicecandidate = (event) => {
                            if (event.candidate) {
                                console.log("Sending ICE candidate");
                                signalingServer.send(JSON.stringify({
                                    type: "candidate",
                                    candidate: event.candidate,
                                    chatId: chatId,
                                    userId: userId
                                }));
                            }
                        };

                        // Estado de la conexión
                        peerConnection.oniceconnectionstatechange = () => {
                            console.log("ICE connection state:", peerConnection.iceConnectionState);
                            if (peerConnection.iceConnectionState === "connected" || 
                                peerConnection.iceConnectionState === "completed") {
                                connectionStatus.textContent = "Conexión establecida!";
                                setTimeout(() => {
                                    connectionStatus.style.display = "none";
                                }, 2000);
                            } else if (peerConnection.iceConnectionState === "failed" || 
                                       peerConnection.iceConnectionState === "disconnected") {
                                connectionStatus.textContent = "Conexión perdida. Intentando reconectar...";
                                connectionStatus.style.display = "block";
                            }
                        };

                        // Avisar al servidor que estamos listos
                        signalingServer.send(JSON.stringify({
                            type: "ready",
                            chatId: chatId,
                            userId: userId
                        }));

                        // Por defecto, asumimos que somos el iniciador
                        setTimeout(() => {
                            createOffer();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error("Error accessing media devices:", error);
                        connectionStatus.textContent = "Error: No se pudo acceder a la cámara o micrófono.";
                        alert("Error: No se pudo acceder a la cámara o al micrófono. Por favor, verifica los permisos en tu navegador.");
                    });
            }

            // Crear y enviar oferta
            function createOffer() {
                console.log("Creating offer...");
                peerConnection.createOffer()
                    .then(offer => {
                        console.log("Setting local description and sending offer");
                        return peerConnection.setLocalDescription(offer)
                            .then(() => {
                                signalingServer.send(JSON.stringify({
                                    type: "offer",
                                    offer: offer,
                                    chatId: chatId,
                                    userId: userId
                                }));
                            });
                    })
                    .catch(error => {
                        console.error("Error creating offer:", error);
                        connectionStatus.textContent = "Error al crear la oferta de conexión.";
                    });
            }

            // Controles de la interfaz
            toggleCam.addEventListener("click", () => {
                if (localStream) {
                    const videoTrack = localStream.getVideoTracks()[0];
                    videoTrack.enabled = !videoTrack.enabled;
                    toggleCam.style.backgroundColor = videoTrack.enabled ? "green" : "red";
                }
            });

            toggleMic.addEventListener("click", () => {
                if (localStream) {
                    const audioTrack = localStream.getAudioTracks()[0];
                    audioTrack.enabled = !audioTrack.enabled;
                    toggleMic.style.backgroundColor = audioTrack.enabled ? "green" : "red";
                }
            });

            function endCall() {
                // Detener todos los tracks
                if (localStream) {
                    localStream.getTracks().forEach(track => track.stop());
                }
                
                // Cerrar conexión peer
                if (peerConnection) {
                    peerConnection.close();
                }
                
                // Cerrar conexión WebSocket
                if (signalingServer && signalingServer.readyState === WebSocket.OPEN) {
                    signalingServer.close();
                }
                
                // Determinar a qué chat redirigir basado en el ID del usuario actual
                const currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
                const otherUserId = <?php echo json_encode($other_user_id); ?>;
                
                // Si el usuario actual es el mismo que está en la URL (emisor), usar el ID del otro usuario
                // Si no (receptor), usar el ID del otro usuario para el chat
                const redirectUserId = (currentUserId == userId) ? otherUserId : otherUserId;
                
                // Regresar a la página de chat con los IDs correctos
                window.location.href = `chat.php?chat_id=${chatId}&user_id=${redirectUserId}`;
            }

            colgarLlamada.addEventListener("click", endCall);
            colgarLlamadaBottom.addEventListener("click", endCall);

            // Iniciar la conexión
            connectToSignalingServer();
        });
    </script>

</body>
</html>