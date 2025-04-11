document.addEventListener("DOMContentLoaded", function () {
    const mensajeInput = document.getElementById("mensaje");
    const enviarBtn = document.getElementById("enviarMensaje");
    const chatMessages = document.getElementById("chatMessages");
    const cerrarChatBtn = document.getElementById("cerrarChat");
    const audio = document.getElementById("audio");
    const btnMusica = document.getElementById("btnMusica");
    const btnUbicacion = document.getElementById("btnUbicacion");
    const btnVideollamada = document.getElementById("btnVideollamada");
    const popupVideollamada = document.getElementById("popupVideollamada");
    const closeVideollamada = document.getElementById("closeVideollamada");
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
        chatMessages.innerHTML = ''; // Limpiar el contenedor de mensajes
        messages.forEach(message => {
            const messageElement = document.createElement('div');
            messageElement.classList.add('mensaje');

            if (message.usuario_id == userId) {
                messageElement.classList.add('enviado');
            } else {
                messageElement.classList.add('recibido');
            }

            const contenidoMensaje = document.createElement('div');
            contenidoMensaje.classList.add('contenido-mensaje');

            let contenidoParseado = null;
            try {
                contenidoParseado = JSON.parse(message.contenido);
            } catch (e) {
                // Si no es JSON, se ignora el error y se trata como texto plano
            }

            if (contenidoParseado && contenidoParseado.tipo === 'videollamada') {
                const invitacion = document.createElement('div');
                if (message.usuario_id == userId) {
                    invitacion.textContent = `Has invitado a ${contenidoParseado.nombre_usuario || 'el usuario'} a una videollamada.`;
                } else {
                    invitacion.textContent = contenidoParseado.mensaje;

                    const botonAceptar = document.createElement('button');
                    botonAceptar.textContent = 'Aceptar Invitación';
                    botonAceptar.style.backgroundColor = 'green';
                    botonAceptar.style.color = 'white';
                    botonAceptar.style.border = 'none';
                    botonAceptar.style.padding = '5px 10px';
                    botonAceptar.style.borderRadius = '5px';
                    botonAceptar.style.cursor = 'pointer';
                    botonAceptar.addEventListener('click', () => {
                        window.location.href = contenidoParseado.url;
                    });

                    contenidoMensaje.appendChild(botonAceptar);
                }

                contenidoMensaje.appendChild(invitacion);
            } else if (contenidoParseado && contenidoParseado.tipo === "ubicacion") {
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
                const nombreUsuario = document.createElement('div');
                nombreUsuario.classList.add('nombre-usuario');
                nombreUsuario.textContent = message.nombre_usuario || 'Usuario';
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

    document.getElementById("btnVideollamada").addEventListener("click", function () {
        const chatId = document.getElementById("chatId").value; // Obtener el ID del chat desde un elemento oculto o variable
        const otherUserId = document.getElementById("otherUserId").value; // Obtener el ID del otro usuario desde un elemento oculto o variable

        if (chatId && otherUserId) {
            window.location.href = `videollamada.php?chat_id=${chatId}&user_id=${otherUserId}`;
        } else {
            alert("No se pudieron obtener los IDs necesarios para la videollamada.");
        }
    });

    // document.getElementById("btnVideollamada").addEventListener("click", function () {
    //     const chatId = /* tu chatId */;
    //     const otherUserId = /* tu otherUserId */;
    //     const currentUserId = /* tu currentUserId */;
        
    //     // Obtener info del usuario actual para incluir en la notificación
    //     fetch(`../backend/get_user_info.php?user_id=${currentUserId}`)
    //         .then(response => response.json())
    //         .then(userData => {
    //             // Enviar mensaje especial de tipo videollamada
    //             fetch('../backend/send_message.php', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json'
    //                 },
    //                 body: JSON.stringify({
    //                     chat_id: chatId,
    //                     contenido: JSON.stringify({
    //                         tipo: 'videollamada',
    //                         mensaje: `${userData.nombre_usuario} te está invitando a una videollamada`,
    //                         nombre_usuario: userData.nombre_usuario,
    //                         url: `videollamada.php?chat_id=${chatId}&user_id=${currentUserId}`
    //                     })
    //                 })
    //             })
    //             .then(response => response.json())
    //             .then(result => {
    //                 if (result.status === 'success') {
    //                     alert("Invitación a videollamada enviada. Esperando que acepte...");
    //                     // Redirigir al emisor a la sala de videollamada
    //                     window.location.href = `videollamada.php?chat_id=${chatId}&user_id=${otherUserId}`;
    //                 } else {
    //                     alert("Error al enviar la invitación: " + result.message);
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //                 alert("Error de conexión al enviar la invitación");
    //             });
    //         })
    //         .catch(error => {
    //             console.error('Error al obtener información del usuario:', error);
    //             alert("Error al obtener información del usuario");
    //         });
    // });

    audio.play();
});