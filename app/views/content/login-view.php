<div class="main-container">
    <form class="box login" action="" method="POST" autocomplete="off">
        <p class="has-text-centered">
            <img src="../ventas/app/views/img/logo.png" alt="Logo" style="width: 200px; height: 200px;">
        </p>
        <h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

        <?php
            if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
                $insLogin->iniciarSesionControlador();
            }
        ?>

        <div class="field">
            <label class="label"><i class="fas fa-user-secret"></i> &nbsp; Usuario</label>
            <div class="control">
                <input class="input" type="text" name="login_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required>
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-key"></i> &nbsp; Clave</label>
            <div class="control">
                <input class="input" type="password" id="login_clave" name="login_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input type="checkbox" onclick="togglePassword()"> Mostrar contraseña
                </label>
            </div>
        </div>

        <p class="has-text-centered mb-4 mt-3">
            <button type="submit" class="button is-info is-rounded">LOG IN</button>
        </p>
    </form>
</div>

<script>
    function togglePassword() {
        var passwordField = document.getElementById("login_clave");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>
