// Control de música
const audio = document.getElementById("audio");
const btnMusica = document.getElementById("btnMusica");
let isPlaying = true;

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

const imagenes = [
    { src: "../images/banner.jpg", link: "mis_chats.php", title: "Mis Chats" },
    { src: "../images/chatibanner.jpg", link: "grupos.php", title: "Mis Grupos" },
    { src: "../images/fondo.png", link: "Tareas.php", title: "Mis Tareas" },
    { src: "../images/chati.png", link: "biografia.php", title: "Conoce a Chati" },
    { src: "../images/Marco Dorado.png", link: "Recompensas.php", title: "Recompensas" }
];