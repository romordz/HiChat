document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();
        if (query.length > 0) {
            fetch(`../backend/search_users.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(users => {
                    searchResults.innerHTML = '';
                    users.forEach(user => {
                        const userElement = document.createElement('div');
                        userElement.classList.add('search-result');
                        userElement.textContent = user.nombre_usuario;
                        userElement.addEventListener('click', function() {
                            window.location.href = `chat.php?user_id=${user.id}`;
                        });
                        searchResults.appendChild(userElement);
                    });
                });
        } else {
            searchResults.innerHTML = '';
        }
    });
});