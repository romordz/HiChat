document.addEventListener("DOMContentLoaded", function () {
    const audio = document.getElementById("audio");
    const btnMusica = document.getElementById("btnMusica");
    const btnRegresar = document.getElementById("btnRegresar");
    let isPlaying = false;

    // Función para activar/desactivar música
    btnMusica.addEventListener("click", () => {
        if (isPlaying) {
            audio.pause();
            btnMusica.textContent = "🎵 Activar Música";
        } else {
            audio.play();
            btnMusica.textContent = "🔇 Silenciar Música";
        }
        isPlaying = !isPlaying;
    });

    // Botón de regresar al menú
    btnRegresar.addEventListener("click", () => {
        window.location.href = "menu.html";
    });
});
