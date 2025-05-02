<?php
session_start();
include '../backend/db_connection.php';

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recompensas</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/recompensas.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <div id="bienvenida">Recompensas</div>

    <div class="recompensas-container">
        <button class="btn-volver" onclick="window.history.back()">Volver</button>
        <h2>Recompensas Disponibles</h2>
        <div class="recompensas-grid" id="recompensasGrid">
            <!-- Las recompensas se cargarán aquí dinámicamente -->
        </div>
    </div>
    <script type="module" src="../js/loadHeader.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const recompensasGrid = document.getElementById("recompensasGrid");

            fetch('../backend/fetch_rewards.php')
                .then(response => response.json())
                .then(rewards => {
                    recompensasGrid.innerHTML = '';
                    rewards.forEach(reward => {
                        const rewardElement = document.createElement('div');
                        rewardElement.classList.add('recompensa');

                        const rewardImage = document.createElement('img');
                        rewardImage.src = `../images/${reward.nombre}.png`;
                        rewardImage.alt = `Recompensa ${reward.nombre}`;
                        rewardImage.classList.add('recompensa-img');

                        const rewardInfo = document.createElement('div');
                        rewardInfo.classList.add('info');

                        const rewardProgress = document.createElement('p');
                        rewardProgress.textContent = `Progreso: ${reward.tareas_completadas}/${reward.tareas_requeridas} Tareas completadas`;

                        const claimButton = document.createElement('button');
                        claimButton.classList.add('btn-reclamar');
                        claimButton.textContent = reward.reclamada ? 'Reclamada' : 'Reclamar';
                        claimButton.disabled = reward.reclamada || reward.tareas_completadas < reward.tareas_requeridas;
                        claimButton.addEventListener('click', () => {
                            fetch('../backend/claim_reward.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `reward_id=${reward.id}`
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    alert('Recompensa reclamada exitosamente');
                                    claimButton.disabled = true;
                                    claimButton.textContent = 'Reclamada';
                                } else {
                                    console.error('Error claiming reward:', result.message);
                                }
                            })
                            .catch(error => console.error('Error claiming reward:', error));
                        });

                        rewardInfo.appendChild(rewardProgress);
                        rewardInfo.appendChild(claimButton);
                        rewardElement.appendChild(rewardImage);
                        rewardElement.appendChild(rewardInfo);
                        recompensasGrid.appendChild(rewardElement);
                    });
                })
                .catch(error => console.error('Error fetching rewards:', error));
        });
    </script>
</body>
</html>