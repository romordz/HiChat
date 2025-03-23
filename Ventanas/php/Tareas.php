<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas</title>
    <link rel="stylesheet" href="../css/tareas.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <div id="bienvenida">Tareas</div>

    <div class="tareas-container">
        <button class="btn-volver" onclick="window.history.back()">Volver</button>
        <h2>Tareas Pendientes</h2>
        <div class="tareas-grid" id="tareasGrid">
            <!-- Las tareas se cargarán aquí dinámicamente -->
        </div>
        <p id="noTareasMensaje" style="display: none; text-align: center; color: white;">No hay tareas pendientes.</p>
    </div>
    <script type="module" src="../js/loadHeader.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tareasGrid = document.getElementById("tareasGrid");
            const noTareasMensaje = document.getElementById("noTareasMensaje");

            fetch('../backend/fetch_user_tasks.php')
                .then(response => response.json())
                .then(tasks => {
                    tareasGrid.innerHTML = '';
                    if (tasks.length === 0) {
                        noTareasMensaje.style.display = 'block';
                    } else {
                        noTareasMensaje.style.display = 'none';
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

                            const taskGroup = document.createElement('p');
                            taskGroup.classList.add('tarea-grupo');
                            taskGroup.textContent = `Grupo: ${task.grupo_nombre}`;

                            const completeButton = document.createElement('button');
                            completeButton.classList.add('btn-completada');
                            completeButton.textContent = task.completada ? 'Completada' : 'Marcar como completada';
                            completeButton.disabled = task.completada || task.usuario_id === <?php echo $_SESSION['user_id']; ?>;
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
                            taskElement.appendChild(taskGroup);
                            taskElement.appendChild(completeButton);
                            tareasGrid.appendChild(taskElement);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching tasks:', error);
                    noTareasMensaje.style.display = 'block';
                    noTareasMensaje.textContent = 'Error al cargar las tareas.';
                });
        });
    </script>
</body>
</html>