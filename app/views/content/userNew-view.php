<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario</title>
    <!-- Incluye tus estilos CSS (si tienes) -->
    <style>
        .weak-password {
            color: red;
        }
        .moderate-password {
            color: orange;
        }
        .strong-password {
            color: green;
        }
        .password-mismatch {
            color: red;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weakPasswords = ['123456', 'password', '123456789', '12345678', '12345', '1234567', '1234567890', 'qwerty', 'abc123'];
            const passwordField1 = document.querySelector('input[name="usuario_clave_1"]');
            const passwordField2 = document.querySelector('input[name="usuario_clave_2"]');
            const passwordStrength = document.createElement('p');
            const passwordSuggestions = document.createElement('p');
            const passwordMismatch = document.createElement('p');
            passwordField1.parentNode.appendChild(passwordStrength);
            passwordField1.parentNode.appendChild(passwordSuggestions);
            passwordField2.parentNode.appendChild(passwordMismatch);

            function validatePassword() {
                const password = passwordField1.value;
                let strengthMessage = '';
                let suggestionMessage = '';
                let isWeak = false;

                // Check for weak passwords
                if (weakPasswords.includes(password)) {
                    isWeak = true;
                    strengthMessage = 'Contraseña débil o común.';
                    passwordStrength.className = 'weak-password';
                    suggestionMessage = 'Sugerencia: Usa una combinación de letras mayúsculas, minúsculas, números y caracteres especiales.';
                } else if (password.length < 8) {
                    // Check for length
                    isWeak = true;
                    strengthMessage = 'Contraseña demasiado corta.';
                    passwordStrength.className = 'weak-password';
                    suggestionMessage = 'Sugerencia: Usa al menos 8 caracteres.';
                } else {
                    // Check for combinations
                    const hasUpperCase = /[A-Z]/.test(password);
                    const hasLowerCase = /[a-z]/.test(password);
                    const hasNumbers = /[0-9]/.test(password);
                    const hasSpecialChars = /[$@.-]/.test(password);

                    if (hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars) {
                        strengthMessage = 'Contraseña fuerte.';
                        passwordStrength.className = 'strong-password';
                        suggestionMessage = '';
                    } else {
                        isWeak = true;
                        strengthMessage = 'Contraseña moderada.';
                        passwordStrength.className = 'moderate-password';
                        suggestionMessage = 'Sugerencia: Usa una combinación de letras mayúsculas, minúsculas, números y caracteres especiales.';
                    }
                }

                passwordStrength.textContent = strengthMessage;
                passwordSuggestions.textContent = suggestionMessage;
                return !isWeak;
            }

            function validatePasswordMatch() {
                if (passwordField1.value !== passwordField2.value) {
                    passwordMismatch.textContent = 'Las contraseñas no coinciden.';
                    passwordMismatch.className = 'password-mismatch';
                    return false;
                } else {
                    passwordMismatch.textContent = '';
                    return true;
                }
            }

            passwordField1.addEventListener('input', function() {
                validatePassword();
                validatePasswordMatch();
            });
            passwordField2.addEventListener('input', validatePasswordMatch);

            document.querySelector('.FormularioAjax').addEventListener('submit', function(event) {
                const isPasswordValid = validatePassword();
                const isPasswordMatch = validatePasswordMatch();
                if (!isPasswordValid || !isPasswordMatch) {
                    event.preventDefault();
                    /*alert('La contraseña es demasiado débil o las contraseñas no coinciden. Por favor, use una contraseña más segura.');*/
                }
            });
        });
    </script>
</head>
<body>
    <div class="container is-fluid mb-6">
        <h1 class="title">Usuarios</h1>
        <h2 class="subtitle"><i class="fas fa-user-tie fa-fw"></i> &nbsp; Nuevo usuario</h2>
    </div>

    <div class="container pb-6 pt-6">
        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="modulo_usuario" value="registrar">

            <div class="columns">
                <div class="column">
                    <div class="control">
                        <label>Nombres <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="text" name="usuario_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
                    </div>
                </div>
                <div class="column">
                    <div class="control">
                        <label>Apellidos <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="text" name="usuario_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column">
                    <div class="control">
                        <label>Usuario <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="text" name="usuario_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required>
                    </div>
                </div>
                <div class="column">
                    <div class="control">
                        <label>Email</label>
                        <input class="input" type="email" name="usuario_email" maxlength="70">
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column">
                    <div class="control">
                        <label>Clave <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
                    </div>
                </div>
                <div class="column">
                    <div class="control">
                        <label>Repetir clave <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column">
                    <div class="file has-name is-boxed">
                        <label class="file-label">
                            <input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg">
                            <span class="file-cta">
                                <span class="file-label">
                                    Seleccione una foto
                                </span>
                            </span>
                            <span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
                        </label>
                    </div>
                </div>

                <div class="column">
                    <label>Caja de ventas <?php echo CAMPO_OBLIGATORIO; ?></label><br>
                    <div class="select">
                        <select name="usuario_caja">
                            <option value="" selected="">Seleccione una opción</option>
                            <?php
                                $datos_cajas=$insLogin->seleccionarDatos("Normal","caja","*",0);
                                while($campos_caja=$datos_cajas->fetch()){
                                    echo '<option value="'.$campos_caja['caja_id'].'">Caja No.'.$campos_caja['caja_numero'].' - '.$campos_caja['caja_nombre'].'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <label>Tipo de Usuario<?php echo CAMPO_OBLIGATORIO; ?></label><br>
                    <div class="select">
                        <select name="rol_usuario">
                            <option value="" selected="">Seleccione una opción</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>
            </div>

            <p class="has-text-centered">
                <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
                <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
            </p>
            <p class="has-text-centered pt-6">
                <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
            </p>
        </form>
    </div>
</body>
</html>
