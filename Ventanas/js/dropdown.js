function initializeDropdown() {
    const btnPerfil = document.getElementById('btnPerfil');
    const dropdown = document.querySelector('.perfil .dropdown');

    btnPerfil.addEventListener('click', function (event) {
        console.log('btnPerfil clicked');
        event.preventDefault();
        event.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function (event) {
        if (!btnPerfil.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    dropdown.addEventListener('click', function (event) {
        if (event.target.tagName === 'A') {
            dropdown.style.display = 'none';
        }
    });
}

export { initializeDropdown };