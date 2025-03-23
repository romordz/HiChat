document.addEventListener("DOMContentLoaded", function () {
    const mensajeInput = document.getElementById("mensaje");
    const enviarBtn = document.getElementById("enviarMensaje");
    const chatMessages = document.getElementById("chatMessages");
    const cerrarChatBtn = document.getElementById("cerrarChat");
    const audio = document.getElementById("audio");
    const btnMusica = document.getElementById("btnMusica");
    const btnUbicacion = document.getElementById("btnUbicacion");
    let isPlaying = true;

    const usuario2Id = otherUserId;

    let mensajesRenderizados = [];

    function fetchMessages() {
        fetch(`../backend/fetch_messages.php?chat_id=${chatId}`)
            .then(response => response.json())
            .then(messages => {
                if (JSON.stringify(mensajesRenderizados) !== JSON.stringify(messages)) {
                    mensajesRenderizados = messages;
                    renderMessages(messages);
                }
            });
    }

    function renderMessages(messages) {
        chatMessages.innerHTML = '';
        messages.forEach(message => {
            const messageElement = document.createElement('div');
            messageElement.classList.add('mensaje');

            if (message.usuario_id == userId) {
                messageElement.classList.add('enviado');
            } else {
                messageElement.classList.add('recibido');
            }

            const nombreUsuario = document.createElement('div');
            nombreUsuario.classList.add('nombre-usuario');
            nombreUsuario.textContent = message.nombre_usuario;

            const contenidoMensaje = document.createElement('div');
            contenidoMensaje.classList.add('contenido-mensaje');

            let contenidoParseado = null;
            try {
                contenidoParseado = JSON.parse(message.contenido);
            } catch (e) {
                // Si no es JSON, se ignora el error y se trata como texto plano
            }

            if (contenidoParseado && contenidoParseado.tipo === "ubicacion") {
                const mapaIframe = document.createElement('iframe');
                mapaIframe.src = `https://maps.google.com/maps?q=${contenidoParseado.latitud},${contenidoParseado.longitud}&z=15&output=embed`;
                mapaIframe.width = "100%";
                mapaIframe.height = "200";
                mapaIframe.style.border = "none";
                mapaIframe.style.borderRadius = "10px";

                contenidoMensaje.appendChild(mapaIframe);
            } else if (message.contenido.startsWith("http")) {
                const enlace = document.createElement('a');
                enlace.href = message.contenido;
                enlace.textContent = "📍 Ver ubicación en Google Maps";
                enlace.target = "_blank";
                contenidoMensaje.appendChild(enlace);
            } else {
                contenidoMensaje.textContent = message.contenido;
            }

            const fechaEnvio = document.createElement('div');
            fechaEnvio.classList.add('fecha-envio');
            fechaEnvio.textContent = new Date(message.fecha_envio).toLocaleString();

            if (message.usuario_id != userId) {
                messageElement.appendChild(nombreUsuario);
            }
            messageElement.appendChild(contenidoMensaje);
            messageElement.appendChild(fechaEnvio);

            chatMessages.appendChild(messageElement);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    enviarBtn.addEventListener('click', function () {
        const contenido = mensajeInput.value.trim();
        if (contenido) {
            fetch('../backend/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    chat_id: chatId,
                    contenido: contenido
                })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        mensajeInput.value = '';
                        fetchMessages();
                    } else {
                        alert("Error al enviar: " + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Error de conexión.");
                });
        }
    });

    setInterval(fetchMessages, 1000);

    mensajeInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            enviarBtn.click();
        }
    });

    btnUbicacion.addEventListener("click", function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitud = position.coords.latitude;
                    const longitud = position.coords.longitude;

                    const ubicacionData = {
                        tipo: "ubicacion",
                        latitud: latitud,
                        longitud: longitud,
                        mensaje: "📍 Mi ubicación actual"
                    };

                    fetch('../backend/send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            chat_id: chatId,
                            contenido: JSON.stringify(ubicacionData)
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text);
                                });
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.status === 'success') {
                                fetchMessages();
                            } else {
                                alert("Error al enviar la ubicación: " + result.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error completo:', error.message);
                            alert("Error de conexión: " + error.message);
                        });
                },
                (error) => {
                    alert("No se pudo obtener la ubicación: " + error.message);
                }
            );
        } else {
            alert("Tu navegador no soporta la geolocalización.");
        }
    });

    cerrarChatBtn.addEventListener("click", () => {
        window.location.href = "menu.php";
    });

    btnMusica.addEventListener("click", () => {
        if (isPlaying) {
            audio.pause();
            btnMusica.textContent = "🔇 Reproducir";
        } else {
            audio.play();
            btnMusica.textContent = "🔊 Silenciar";
        }
        isPlaying = !isPlaying;
    });
    audio.play();
});