// videollamada.js - Este archivo contiene toda la lógica de WebRTC

class VideoChat {
    constructor(options) {
        // Elementos DOM
        this.videoLocal = options.videoLocal;
        this.videoRemoto = options.videoRemoto;
        this.statusElement = options.statusElement;
        
        // Parámetros de la llamada
        this.chatId = options.chatId;
        this.userId = options.userId;
        this.signalingUrl = options.signalingUrl || "ws://localhost:8080";
        
        // Estado
        this.localStream = null;
        this.peerConnection = null;
        this.signalingServer = null;
        this.isInitiator = false;
        this.reconnectAttempts = 0;
        this.MAX_RECONNECT_ATTEMPTS = 5;
        
        // Configuración WebRTC
        this.rtcConfig = {
            iceServers: [
                { urls: "stun:stun.l.google.com:19302" },
                { urls: "stun:stun1.l.google.com:19302" }
                // Puedes añadir servidores TURN para mayor confiabilidad
            ]
        };
        
        // Callbacks
        this.onConnected = options.onConnected || function() {};
        this.onDisconnected = options.onDisconnected || function() {};
        this.onError = options.onError || function() {};
    }
    
    // Iniciar la videollamada
    start() {
        this.updateStatus("Iniciando videollamada...");
        this.connectToSignalingServer();
    }
    
    // Conectar al servidor de señalización
    connectToSignalingServer() {
        this.updateStatus("Conectando al servidor...");
        
        try {
            this.signalingServer = new WebSocket(this.signalingUrl);
            
            this.signalingServer.onopen = () => {
                console.log("Conexión WebSocket establecida");
                this.updateStatus("Conexión establecida, iniciando videollamada...");
                
                // Registrar esta conexión con el servidor
                this.sendSignalingMessage({
                    type: "register",
                    chatId: this.chatId,
                    userId: this.userId
                });
                
                // Iniciar el proceso WebRTC
                this.startWebRTC();
            };
            
            this.signalingServer.onmessage = (event) => this.handleSignalingMessage(event);
            
            this.signalingServer.onerror = (error) => {
                console.error("Error en WebSocket:", error);
                this.updateStatus("Error de conexión al servidor");
                this.handleReconnect();
            };
            
            this.signalingServer.onclose = (event) => {
                if (!this.isClosed) {
                    console.warn("Conexión WebSocket cerrada:", event);
                    this.updateStatus("Conexión cerrada");
                    this.handleReconnect();
                }
            };
        } catch (error) {
            console.error("Error al crear WebSocket:", error);
            this.updateStatus("Error al conectar con el servidor");
            this.onError(error);
        }
    }
    
    // Manejar mensajes del servidor de señalización
    handleSignalingMessage(event) {
        try {
            const data = JSON.parse(event.data);
            console.log("Mensaje recibido:", data);
            
            switch (data.type) {
                case "connection":
                    console.log("ID de conexión recibido:", data.clientId);
                    break;
                    
                case "start":
                    this.isInitiator = data.isInitiator;
                    if (this.isInitiator) {
                        this.createOffer();
                    }
                    break;
                    
                case "offer":
                    this.handleOffer(data.offer);
                    break;
                    
                case "answer":
                    this.handleAnswer(data.answer);
                    break;
                    
                case "candidate":
                    this.handleCandidate(data.candidate);
                    break;
                    
                case "error":
                    this.updateStatus(`Error: ${data.message}`);
                    this.onError(new Error(data.message));
                    break;
                    
                default:
                    console.warn("Tipo de mensaje desconocido:", data.type);
            }
        } catch (error) {
            console.error("Error al procesar mensaje:", error);
        }
    }
    
    // Iniciar WebRTC y solicitar medios
    startWebRTC() {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                this.localStream = stream;
                this.videoLocal.srcObject = stream;
                this.updateStatus("Cámara y micrófono activados");
                
                // Crear conexión peer
                this.createPeerConnection();
                
                // Enviar medios locales
                this.localStream.getTracks().forEach(track => {
                    this.peerConnection.addTrack(track, this.localStream);
                });
                
                // Avisar al servidor que estamos listos
                this.sendSignalingMessage({
                    type: "ready",
                    chatId: this.chatId,
                    userId: this.userId
                });
                
                // Por defecto, intentamos iniciar la oferta después de un tiempo
                setTimeout(() => {
                    this.createOffer();
                }, 1000);
            })
            .catch(error => {
                console.error("Error al acceder a dispositivos:", error);
                this.updateStatus("Error: No se pudo acceder a la cámara o micrófono");
                this.onError(error);
            });
    }
    
    // Crear la conexión peer
    createPeerConnection() {
        this.peerConnection = new RTCPeerConnection(this.rtcConfig);
        
        // Manejar candidatos ICE
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.sendSignalingMessage({
                    type: "candidate",
                    candidate: event.candidate,
                    chatId: this.chatId,
                    userId: this.userId
                });
            }
        };
        
        // Manejar cambios de estado de la conexión ICE
        this.peerConnection.oniceconnectionstatechange = () => {
            console.log("Estado de conexión ICE:", this.peerConnection.iceConnectionState);
            
            switch (this.peerConnection.iceConnectionState) {
                case "connected":
                case "completed":
                    this.updateStatus("Conexión establecida");
                    this.onConnected();
                    break;
                    
                case "failed":
                case "disconnected":
                    this.updateStatus("Conexión perdida");
                    this.onDisconnected();
                    break;
                    
                case "closed":
                    this.updateStatus("Conexión cerrada");
                    break;
            }
        };
        
        // Manejar streams remotos
        this.peerConnection.ontrack = (event) => {
            if (event.streams && event.streams[0]) {
                this.videoRemoto.srcObject = event.streams[0];
                this.updateStatus("Vídeo remoto conectado");
            }
        };
    }
    
    // Crear y enviar oferta
    createOffer() {
        if (!this.peerConnection) return;
        
        this.peerConnection.createOffer()
            .then(offer => {
                return this.peerConnection.setLocalDescription(offer)
                    .then(() => {
                        this.sendSignalingMessage({
                            type: "offer",
                            offer: offer,
                            chatId: this.chatId,
                            userId: this.userId
                        });
                    });
            })
            .catch(error => {
                console.error("Error al crear oferta:", error);
                this.updateStatus("Error al crear la oferta de conexión");
                this.onError(error);
            });
    }
    
    // Manejar oferta recibida
    handleOffer(offer) {
        if (!this.peerConnection) this.createPeerConnection();
        
        this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer))
            .then(() => {
                this.updateStatus("Oferta recibida, creando respuesta...");
                return this.peerConnection.createAnswer();
            })
            .then(answer => {
                return this.peerConnection.setLocalDescription(answer)
                    .then(() => {
                        this.sendSignalingMessage({
                            type: "answer",
                            answer: answer,
                            chatId: this.chatId,
                            userId: this.userId
                        });
                    });
            })
            .catch(error => {
                console.error("Error al procesar oferta:", error);
                this.updateStatus("Error al procesar la oferta");
                this.onError(error);
            });
    }
    
    // Manejar respuesta recibida
    handleAnswer(answer) {
        this.peerConnection.setRemoteDescription(new RTCSessionDescription(answer))
            .then(() => {
                this.updateStatus("Respuesta recibida, conectando...");
            })
            .catch(error => {
                console.error("Error al procesar respuesta:", error);
                this.updateStatus("Error al procesar la respuesta");
                this.onError(error);
            });
    }
    
    // Manejar candidato ICE recibido
    handleCandidate(candidate) {
        if (!candidate) return;
        
        this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate))
            .catch(error => {
                console.error("Error al añadir candidato ICE:", error);
            });
    }
    
    // Manejar reconexión
    handleReconnect() {
        if (this.reconnectAttempts < this.MAX_RECONNECT_ATTEMPTS) {
            this.reconnectAttempts++;
            
            setTimeout(() => {
                this.updateStatus(`Reintentando conexión (${this.reconnectAttempts}/${this.MAX_RECONNECT_ATTEMPTS})...`);
                this.connectToSignalingServer();
            }, 3000);
        } else {
            this.updateStatus("No se pudo establecer la conexión. Por favor, intente más tarde.");
            this.onError(new Error("Máximo de intentos de reconexión alcanzado"));
        }
    }
    
    // Actualizar estado en la interfaz
    updateStatus(message) {
        if (this.statusElement) {
            this.statusElement.textContent = message;
        }
        console.log("Estado:", message);
    }
    
    // Enviar mensaje al servidor de señalización
    sendSignalingMessage(message) {
        if (this.signalingServer && this.signalingServer.readyState === WebSocket.OPEN) {
            this.signalingServer.send(JSON.stringify(message));
        } else {
            console.warn("No se puede enviar mensaje, WebSocket no está abierto");
        }
    }
    

    // Activar/Desactivar cámara
    toggleCamera() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                return videoTrack.enabled;
            }
        }
        return false;
    }
    
    // Activar/Desactivar micrófono
    toggleMicrophone() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                return audioTrack.enabled;
            }
        }
        return false;
    }
    
    // Finalizar llamada
    endCall() {
        this.isClosed = true;
        
        // Detener todos los tracks
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
        }
        
        // Cerrar conexión peer
        if (this.peerConnection) {
            this.peerConnection.close();
            this.peerConnection = null;
        }
        
        // Cerrar conexión websocket
        if (this.signalingServer) {
            if (this.signalingServer.readyState === WebSocket.OPEN) {
                this.sendSignalingMessage({
                    type: "leave",
                    chatId: this.chatId,
                    userId: this.userId
                });
            }
            
            this.signalingServer.close();
            this.signalingServer = null;
        }
        
        this.updateStatus("Llamada finalizada");
    }
}

// Exportar la clase para uso modular
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VideoChat;
}