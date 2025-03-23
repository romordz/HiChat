<?php
include '../backend/process_registration.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Chat Player ver 0.1</title>
    <link rel="icon" href="images/icon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/inicioSesion.css">
</head>
<body>
    <div class="logo-container">
        <img src="../images/logogrande.png" alt="Logo Grande">
    </div>
    <div class="login-container">
        <h2>Registro</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form id="registerForm" action="registro.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <?php if (isset($errors['username'])): ?>
                    <div class="text-danger bg-danger p-2"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="text-danger bg-danger p-2"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="text-danger bg-danger p-2"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                <?php if (isset($errors['confirmPassword'])): ?>
                    <div class="text-danger bg-danger p-2"><?php echo $errors['confirmPassword']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="profilePicture">Foto de Perfil</label>
                <input type="file" class="form-control-file" id="profilePicture" name="profilePicture" accept="image/jpeg">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>

        <div class="register-container">
            <p>¿Ya tienes una cuenta?</p>
            <a href="inicioSesion.php" class="btn btn-secondary">Iniciar Sesión</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        document.getElementById("registerForm").addEventListener("submit", function(event) {
            let password = document.getElementById("password").value.trim();
            let confirmPassword = document.getElementById("confirmPassword").value.trim();

            if (password !== confirmPassword) {
                document.getElementById("confirmPassword").nextElementSibling.textContent = "Las contraseñas no coinciden.";
                event.preventDefault();
            }

            if (!validatePassword(password)) {
                document.getElementById("password").nextElementSibling.textContent = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.";
                event.preventDefault();
            }
        });

        document.getElementById("email").addEventListener("change", function(event) {
            let email = event.target.value.trim();
            if (!validateEmail(email)) {
                document.getElementById("email").nextElementSibling.textContent = "Por favor, ingresa un correo electrónico válido.";
                event.preventDefault();
            } else {
                checkEmailInUse(email);
            }
        });

        function validatePassword(password) {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
            return regex.test(password);
        }

        function validateEmail(email) {
            const regex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com|hotmail\.com)$/;
            return regex.test(email);
        }

        function checkEmailInUse(email) {
            fetch(`../backend/check_email.php?email=${email}`)
                .then(response => response.json())
                .then(data => {
                    if (data.in_use) {
                        document.getElementById("email").nextElementSibling.textContent = "Este correo electrónico ya está en uso.";
                    }
                });
        }
    </script>
</body>
</html>