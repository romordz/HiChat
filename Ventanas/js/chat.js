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
        chatMessages.innerHTML = '';
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

            if (contenidoParseado) {
                switch(contenidoParseado.tipo) {
                    case 'videollamada':
                        const invitacionContainer = document.createElement('div');
                        invitacionContainer.classList.add('videollamada-invitacion');
                        
                        // Buscar si esta invitación tiene una respuesta
                        const respuesta = messages.find(m => {
                            try {
                                const contenido = JSON.parse(m.contenido);
                                return contenido.tipo === 'videollamada_respuesta' && 
                                       contenido.invitacionId === message.id;
                            } catch (e) {
                                return false;
                            }
                        });

                        if (message.usuario_id == userId) {
                            // El usuario actual ENVIÓ la invitación
                            if (respuesta) {
                                const respuestaData = JSON.parse(respuesta.contenido);
                                if (respuestaData.respuesta === 'rechazada') {
                                    invitacionContainer.innerHTML = `
                                        <div class="videollamada-mensaje">
                                            <span class="videollamada-icon">📹</span>
                                            Has invitado a ${contenidoParseado.nombre_usuario} a una videollamada
                                        </div>
                                        <div class="videollamada-mensaje rechazada">
                                            <span class="videollamada-icon">📹</span>
                                            ${respuestaData.mensaje}
                                        </div>`;
                                } else {
                                    invitacionContainer.innerHTML = `
                                        <div class="videollamada-mensaje">
                                            <span class="videollamada-icon">📹</span>
                                            Has invitado a ${contenidoParseado.nombre_usuario} a una videollamada
                                        </div>
                                        <div class="videollamada-status">Esperando respuesta...</div>`;
                                }
                            } else {
                                invitacionContainer.innerHTML = `
                                    <div class="videollamada-mensaje">
                                        <span class="videollamada-icon">📹</span>
                                        Has invitado a ${contenidoParseado.nombre_usuario} a una videollamada
                                    </div>
                                    <div class="videollamada-status">Esperando respuesta...</div>`;
                            }
                        } else {
                            // El usuario actual RECIBIÓ la invitación
                            const emisorNombre = message.nombre_usuario;
                            const currentUserName = contenidoParseado.nombre_usuario;

                            if (respuesta) {
                                // Si hay respuesta y el usuario actual fue quien rechazó
                                const respuestaData = JSON.parse(respuesta.contenido);
                                if (respuestaData.rechazadaPor === userId) {
                                    invitacionContainer.innerHTML = `
                                        <div class="videollamada-mensaje">
                                            <span class="videollamada-icon">📹</span>
                                            ${emisorNombre} te invitó a una videollamada
                                        </div>
                                        <div class="videollamada-mensaje rechazada">
                                            <span class="videollamada-icon">📹</span>
                                            Rechazaste la videollamada
                                        </div>`;
                                } else {
                                    return; // No mostrar nada si la respuesta fue del otro usuario
                                }
                            } else {
                                // Si no hay respuesta, mostrar la invitación con botones
                                invitacionContainer.innerHTML = `
                                    <div class="videollamada-mensaje">
                                        <span class="videollamada-icon">📹</span>
                                        ${emisorNombre} te invita a una videollamada
                                    </div>
                                    <div class="videollamada-botones">
                                        <button class="btn-aceptar">✓ Aceptar</button>
                                        <button class="btn-rechazar">✗ Rechazar</button>
                                    </div>`;

                                const btnAceptar = invitacionContainer.querySelector('.btn-aceptar');
                                const btnRechazar = invitacionContainer.querySelector('.btn-rechazar');
                                
                                btnAceptar.addEventListener('click', () => {
                                    fetch('../backend/send_message.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            chat_id: chatId,
                                            contenido: JSON.stringify({
                                                tipo: 'videollamada_respuesta',
                                                respuesta: 'aceptada',
                                                emisor: emisorNombre,
                                                mensaje: `${currentUserName} aceptó la videollamada`,
                                                invitacionId: message.id
                                            })
                                        })
                                    }).then(() => {
                                        window.location.href = contenidoParseado.url;
                                    });
                                });

                                btnRechazar.addEventListener('click', () => {
                                    fetch('../backend/send_message.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            chat_id: chatId,
                                            contenido: JSON.stringify({
                                                tipo: 'videollamada_respuesta',
                                                respuesta: 'rechazada',
                                                emisor: emisorNombre,
                                                mensaje: `${currentUserName} rechazó la videollamada`,
                                                invitacionId: message.id,
                                                rechazadaPor: userId
                                            })
                                        })
                                    }).then(() => {
                                        // Actualizar la UI localmente
                                        const botonesContainer = invitacionContainer.querySelector('.videollamada-botones');
                                        if (botonesContainer) {
                                            botonesContainer.remove();
                                        }
                                        
                                        const mensajeOriginal = invitacionContainer.querySelector('.videollamada-mensaje');
                                        const nuevoMensaje = document.createElement('div');
                                        nuevoMensaje.classList.add('videollamada-mensaje', 'rechazada');
                                        nuevoMensaje.innerHTML = `
                                            <span class="videollamada-icon">📹</span>
                                            Rechazaste la videollamada`;
                                        mensajeOriginal.after(nuevoMensaje);
                                    });
                                });
                            }
                        }
                        contenidoMensaje.appendChild(invitacionContainer);
                        break;

                    case 'videollamada_respuesta':
                        // No renderizamos este tipo de mensajes directamente
                        // Ya que se manejan como parte del mensaje de invitación
                        return;

                    case 'ubicacion':
                        const mapaIframe = document.createElement('iframe');
                        mapaIframe.src = `https://maps.google.com/maps?q=${contenidoParseado.latitud},${contenidoParseado.longitud}&z=15&output=embed`;
                        mapaIframe.width = "100%";
                        mapaIframe.height = "200";
                        mapaIframe.style.border = "none";
                        mapaIframe.style.borderRadius = "10px";
                        contenidoMensaje.appendChild(mapaIframe);
                        break;

                    case 'documento':
                        const docLink = document.createElement('a');
                        docLink.href = '../' + contenidoParseado.url;
                        docLink.textContent = '📄 ' + contenidoParseado.nombre;
                        docLink.target = '_blank';
                        docLink.style.color = 'white';
                        docLink.style.textDecoration = 'none';
                        docLink.style.display = 'block';
                        docLink.style.padding = '5px';
                        contenidoMensaje.appendChild(docLink);
                        break;

                    default:
                        contenidoMensaje.textContent = message.contenido;
                }
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

    // Manejador para el botón de documento
    const btnDocumento = document.getElementById("btnDocumento");
    const inputDocumento = document.getElementById("documento");

    btnDocumento.addEventListener("click", () => {
        inputDocumento.click();
    });

    inputDocumento.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            alert(`El archivo "${file.name}" es demasiado grande.\nTamaño máximo permitido: 5MB\nTamaño del archivo: ${(file.size / (1024 * 1024)).toFixed(2)}MB`);
            inputDocumento.value = '';
            return;
        }

        const formData = new FormData();
        formData.append("documento", file);
        formData.append("chat_id", chatId);

        // Mostrar indicador de carga
        const loadingMessage = document.createElement('div');
        loadingMessage.classList.add('mensaje', 'enviado');
        loadingMessage.textContent = 'Subiendo documento...';
        chatMessages.appendChild(loadingMessage);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        fetch('../backend/upload_document.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            // Remover mensaje de carga
            loadingMessage.remove();
            
            if (result.status === 'success') {
                // Procesar el nombre del archivo para mostrar
                let displayName = file.name;
                const maxLength = 30;
                if (displayName.length > maxLength) {
                    const extension = displayName.split('.').pop();
                    displayName = displayName.substring(0, maxLength - 4) + '...' + extension;
                }

                // Enviar mensaje con la información del documento
                return fetch('../backend/send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        chat_id: chatId,
                        contenido: JSON.stringify({
                            tipo: 'documento',
                            nombre: displayName,
                            nombreOriginal: file.name,
                            url: result.file_url
                        })
                    })
                });
            } else {
                throw new Error(result.message);
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                fetchMessages();
            } else {
                alert("Error al enviar el mensaje del documento: " + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error al subir el documento: " + error.message);
        });

        // Limpiar el input
        inputDocumento.value = '';
    });
});