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

audio.play(); // Inicia la música automáticamente

// Galería de imágenes con enlaces a diferentes páginas
const imagenes = [
    { src: "images/banner.jpg", link: "chat.html" },
    { src: "images/chatibanner.jpg", link: "biografia.html" },
    { src: "images/run.png", link: "run.html" }
];

let index = 0;
const imagenActual = document.getElementById("imagenActual");
const enlaceImagen = document.getElementById("enlaceImagen");

function actualizarImagen() {
    imagenActual.src = imagenes[index].src;
    enlaceImagen.href = imagenes[index].link;
}

document.getElementById("prev").addEventListener("click", () => {
    index = (index - 1 + imagenes.length) % imagenes.length;
    actualizarImagen();
});

document.getElementById("next").addEventListener("click", () => {
    index = (index + 1) % imagenes.length;
    actualizarImagen();
});