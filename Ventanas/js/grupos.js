document.addEventListener("DOMContentLoaded", function () {
    const crearGrupoBtn = document.getElementById("crearGrupoBtn");
    const crearGrupoPopup = document.getElementById("crearGrupoPopup");
    const closeBtn = document.querySelector(".close");
    const buscarUsuariosInput = document.getElementById("buscarUsuarios");
    const resultadosBusqueda = document.getElementById("resultadosBusqueda");

    crearGrupoBtn.addEventListener("click", function () {
        crearGrupoPopup.style.display = "block";
    });

    closeBtn.addEventListener("click", function () {
        crearGrupoPopup.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target == crearGrupoPopup) {
            crearGrupoPopup.style.display = "none";
        }
    });

    buscarUsuariosInput.addEventListener("input", function () {
        const query = buscarUsuariosInput.value.trim();
        if (query.length > 0) {
            fetch(`../backend/search_users.php?query=${query}`)
                .then(response => response.json())
                .then(users => {
                    resultadosBusqueda.innerHTML = "";
                    users.forEach(user => {
                        const userElement = document.createElement("div");
                        userElement.textContent = user.nombre_usuario;
                        userElement.dataset.userId = user.id;
                        userElement.classList.add("user-result");
                        resultadosBusqueda.appendChild(userElement);
                    });
                });
        } else {
            resultadosBusqueda.innerHTML = "";
        }
    });

    resultadosBusqueda.addEventListener("click", function (event) {
        if (event.target.classList.contains("user-result")) {
            const userId = event.target.dataset.userId;
            const userName = event.target.textContent;
            const selectedUsers = document.getElementById("selectedUsers");
            const userElement = document.createElement("div");
            userElement.textContent = userName;
            userElement.dataset.userId = userId;
            selectedUsers.appendChild(userElement);
        }
    });

    document.getElementById("crearGrupoForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const nombreGrupo = document.getElementById("nombreGrupo").value;
        const descripcionGrupo = document.getElementById("descripcionGrupo").value;
        const selectedUsers = Array.from(document.getElementById("selectedUsers").children).map(user => user.dataset.userId);

        fetch("../backend/create_group.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                nombreGrupo,
                descripcionGrupo,
                miembros: selectedUsers
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === "success") {
                alert("Grupo creado exitosamente");
                window.location.reload();
            } else {
                alert("Error al crear el grupo: " + result.message);
            }
        });
    });
});