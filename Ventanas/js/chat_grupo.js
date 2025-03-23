document.addEventListener("DOMContentLoaded", function () {
    const mensajeInput = document.getElementById("mensaje");
    const enviarBtn = document.getElementById("enviarMensaje");
    const chatMessages = document.getElementById("chatMessages");
    const cerrarChatBtn = document.getElementById("cerrarChat");
    const verMiembrosBtn = document.getElementById("verMiembros");
    const gestionarTareasBtn = document.getElementById("gestionarTareas");
    const miembrosPopup = document.getElementById("miembrosPopup");
    const tareasPopup = document.getElementById("tareasPopup");
    const closeBtns = document.querySelectorAll(".close");
    const listaMiembros = document.getElementById("listaMiembros");
    const listaTareas = document.getElementById("listaTareas");
    const crearTareaForm = document.getElementById("crearTareaForm");
    const audio = document.getElementById("audio");
    const btnMusica = document.getElementById("btnMusica");
    const btnUbicacion = document.getElementById("btnUbicacion");
    let isPlaying = true;

    // Variable para almacenar los mensajes ya renderizados
    let mensajesRenderizados = [];

    function fetchMessages() {
        fetch(`../backend/fetch_messages_group.php?grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(messages => {
                // Verificar si los mensajes son diferentes a los ya renderizados
                if (JSON.stringify(mensajesRenderizados) !== JSON.stringify(messages)) {
                    mensajesRenderizados = messages; // Actualizar la lista de mensajes renderizados
                    renderMessages(messages); // Renderizar los mensajes
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
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

            const nombreUsuario = document.createElement('div');
            nombreUsuario.classList.add('nombre-usuario');
            nombreUsuario.textContent = message.nombre_usuario;

            const contenidoMensaje = document.createElement('div');
            contenidoMensaje.classList.add('contenido-mensaje');

            // Verificar si el contenido es un JSON válido
            let contenidoParseado = null;
            try {
                contenidoParseado = JSON.parse(message.contenido);
            } catch (e) {
                // Si no es JSON, se ignora el error y se trata como texto plano
            }

            // Si es un JSON y tiene el tipo "ubicacion", mostrar el mapa
            if (contenidoParseado && contenidoParseado.tipo === "ubicacion") {
                const mapaIframe = document.createElement('iframe');
                mapaIframe.src = `https://maps.google.com/maps?q=${contenidoParseado.latitud},${contenidoParseado.longitud}&z=15&output=embed`;
                mapaIframe.width = "100%";
                mapaIframe.height = "200";
                mapaIframe.style.border = "none";
                mapaIframe.style.borderRadius = "10px";

                contenidoMensaje.appendChild(mapaIframe);
            } else if (message.contenido.startsWith("http")) {
                // Si es una URL, mostrar un enlace
                const enlace = document.createElement('a');
                enlace.href = message.contenido;
                enlace.textContent = "📍 Ver ubicación en Google Maps";
                enlace.target = "_blank"; // Abrir en una nueva pestaña
                contenidoMensaje.appendChild(enlace);
            } else {
                // Si no es JSON o no es una ubicación, mostrar el contenido como texto
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
        chatMessages.scrollTop = chatMessages.scrollHeight; // Desplazar al final del chat
    }

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

                    fetch('../backend/send_message_group.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            grupo_id: grupoId,
                            contenido: JSON.stringify(ubicacionData)
                        })
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 'success') {
                                fetchMessages();
                            } else {
                                console.error('Error sending location:', result.message);
                            }
                        })
                        .catch(error => console.error('Error sending location:', error));
                },
                (error) => {
                    console.error('Error getting location:', error.message);
                }
            );
        } else {
            alert("Tu navegador no soporta la geolocalización.");
        }
    });

    enviarBtn.addEventListener('click', function () {
        const contenido = mensajeInput.value.trim();
        if (contenido) {
            fetch('../backend/send_message_group.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // Enviar datos en formato JSON
                },
                body: JSON.stringify({
                    grupo_id: grupoId,
                    contenido: contenido
                })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        mensajeInput.value = '';
                        fetchMessages();
                    } else {
                        console.error('Error sending message:', result.message);
                    }
                })
                .catch(error => console.error('Error sending message:', error));
        }
    });

    setInterval(fetchMessages, 1000);

    mensajeInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            enviarMensaje();
        }
    });

    function enviarMensaje() {
        const mensajeTexto = mensajeInput.value.trim();
        if (mensajeTexto === "") return;

        const mensajeElemento = document.createElement("div");
        mensajeElemento.classList.add("mensaje", "enviado");

        const contenidoMensaje = document.createElement('div');
        contenidoMensaje.classList.add('contenido-mensaje');
        contenidoMensaje.textContent = mensajeTexto;

        const fechaEnvio = document.createElement('div');
        fechaEnvio.classList.add('fecha-envio');
        fechaEnvio.textContent = new Date().toLocaleString();

        mensajeElemento.appendChild(contenidoMensaje);
        mensajeElemento.appendChild(fechaEnvio);

        chatMessages.appendChild(mensajeElemento);

        mensajeInput.value = "";

        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    cerrarChatBtn.addEventListener("click", () => {
        window.location.href = "grupos.php";
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

    verMiembrosBtn.addEventListener("click", () => {
        fetch(`../backend/fetch_group_members.php?grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(members => {
                listaMiembros.innerHTML = '';
                members.forEach(member => {
                    const memberElement = document.createElement('a');
                    memberElement.href = `perfil.php?user_id=${member.id}`;
                    memberElement.classList.add('miembro');
                    
                    const marcoPerfil = document.createElement('div');
                    marcoPerfil.classList.add('marco-perfil-miembros');
                    marcoPerfil.style.backgroundImage = `url('../images/${member.marco_perfil}.png')`;
                    
                    const memberImage = document.createElement('img');
                    memberImage.src = member.foto_perfil ? `data:image/jpeg;base64,${member.foto_perfil}` : '../images/default.webp';
                    memberImage.alt = member.nombre_usuario;
                    memberImage.classList.add('miembro-img');
                    
                    const memberName = document.createElement('div');
                    memberName.textContent = member.nombre_usuario;
                    memberName.classList.add('miembro-nombre');
                    
                    marcoPerfil.appendChild(memberImage);
                    memberElement.appendChild(marcoPerfil);
                    memberElement.appendChild(memberName);
                    listaMiembros.appendChild(memberElement);
                });
                miembrosPopup.style.display = "block";
            })
            .catch(error => console.error('Error fetching members:', error));
    });

    gestionarTareasBtn.addEventListener("click", () => {
        fetch(`../backend/fetch_group_tasks.php?grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(tasks => {
                listaTareas.innerHTML = '';
                tasks.forEach(task => {
                    const taskElement = document.createElement('div');
                    taskElement.classList.add('tarea');
                    
                    const taskTitle = document.createElement('h3');
                    taskTitle.classList.add('tarea-titulo');
                    taskTitle.textContent = task.titulo;
                    
                    const taskDescription = document.createElement('p');
                    taskDescription.classList.add('tarea-descripcion');
                    taskDescription.textContent = task.descripcion;
                    
                    const taskDate = document.createElement('p');
                    taskDate.classList.add('tarea-fecha');
                    taskDate.textContent = `Fecha límite: ${task.fecha_limite}`;
                    
                    const completeButton = document.createElement('button');
                    completeButton.classList.add('btn-completada');
                    completeButton.textContent = task.completada ? 'Completada' : 'Marcar como completada';
                    completeButton.disabled = task.completada || task.usuario_id === userId;
                    completeButton.addEventListener('click', () => {
                        fetch('../backend/complete_task.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `tarea_id=${task.id}`
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 'success') {
                                completeButton.disabled = true;
                                completeButton.textContent = 'Completada';
                            } else {
                                console.error('Error completing task:', result.message);
                            }
                        })
                        .catch(error => console.error('Error completing task:', error));
                    });
                    
                    taskElement.appendChild(taskTitle);
                    taskElement.appendChild(taskDescription);
                    taskElement.appendChild(taskDate);
                    taskElement.appendChild(completeButton);
                    listaTareas.appendChild(taskElement);
                });
                tareasPopup.style.display = "block";
            })
            .catch(error => console.error('Error fetching tasks:', error));
    });

    crearTareaForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const titulo = document.getElementById('tituloTarea').value;
        const descripcion = document.getElementById('descripcionTarea').value;
        const fechaLimite = document.getElementById('fechaLimite').value;

        fetch('../backend/create_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `grupo_id=${grupoId}&titulo=${encodeURIComponent(titulo)}&descripcion=${encodeURIComponent(descripcion)}&fecha_limite=${fechaLimite}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Tarea creada exitosamente');
                gestionarTareasBtn.click();
            } else {
                console.error('Error creating task:', result.message);
            }
        })
        .catch(error => console.error('Error creating task:', error));
    });

    closeBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            btn.closest('.popup').style.display = "none";
        });
    });

    window.addEventListener("click", (event) => {
        if (event.target == miembrosPopup || event.target == tareasPopup) {
            event.target.style.display = "none";
        }
    });

    audio.play();
});