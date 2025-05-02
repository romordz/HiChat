document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();
        
        // Limpiar el timeout anterior
        clearTimeout(searchTimeout);
        
        if (query.length > 0) {
            // Establecer un nuevo timeout para evitar múltiples peticiones
            searchTimeout = setTimeout(() => {
                fetch(`../backend/search_users.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(users => {
                        // Crear un fragmento para mejorar el rendimiento
                        const fragment = document.createDocumentFragment();
                        
                        users.forEach(user => {
                            const userElement = document.createElement('div');
                            userElement.classList.add('search-result');
                            userElement.textContent = user.nombre_usuario;
                            userElement.addEventListener('click', function() {
                                window.location.href = `chat.php?user_id=${user.id}`;
                            });
                            fragment.appendChild(userElement);
                        });

                        // Limpiar y añadir resultados de una sola vez
                        searchResults.innerHTML = '';
                        searchResults.appendChild(fragment);
                    });
            }, 300); // Esperar 300ms antes de hacer la búsqueda
        } else {
            searchResults.innerHTML = '';
        }
    });

    // Cerrar resultados cuando se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.innerHTML = '';
        }
    });
});