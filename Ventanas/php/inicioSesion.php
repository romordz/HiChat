<?php
include '../backend/process_login.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Chat Player ver 0.1</title>
    <link rel="icon" href="images/icon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/inicioSesion.css">
</head>
<body>
    <div class="logo-container">
        <img src="../images/logogrande.png" alt="Logo Grande">
    </div>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if ($error_message): ?>
            <div class="text-danger bg-danger p-2"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form id="loginForm" action="inicioSesion.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">
                    Por favor, ingresa un correo electrónico válido.
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">
                    Por favor, ingresa una contraseña.
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button><br>
        </form>

        <!-- Botón para registrarse -->
        <div class="register-container">
            <p>¿No tienes una cuenta?</p>
            <a href="registro.php" class="btn btn-secondary">Únete a Hi ChatPlayer</a>
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
    </script>
</body>
</html>
