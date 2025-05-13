<?php
include_once('header.php');?>

<?php
include("configuracion/conexion.php");
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario de gestor</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="departamento" class="form-label">Departamento</label>
                <select class="form-select" id="departamento" name="departamento" required>
                    <?php 
                    $query = "SELECT * FROM departamento";
                    $resultado = pg_query($conexion, $query);
                    while ($fila = pg_fetch_assoc($resultado)) {
                        echo "<option value='" . $fila['id_departamento'] . "'>" . $fila['nombre_departamento'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
            </div>

            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>

            <div class="mb-3">
                <label for="password_hash" class="form-label">Contraseña</label>
                <input type="text" class="form-control" id="password_hash" name="password_hash" required>
            </div>

            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select class="form-select" id="rol_id" name="rol_id" required>
                    <option value="">-- Selecciona un rol --</option>
                    <option value="1">Administrador</option>
                    <option value="2">Editor</option>
                    <option value="3">Visor</option>


                    <?php 
                    $query = "SELECT * FROM distrito";
                    $resultado = pg_query($conexion, $query);
                    while ($fila = pg_fetch_assoc($resultado)) {
                        echo "<option value='" . $fila['id_distrito'] . "'>" . $fila;
                        ['nombre_distrito'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
        </form>
    </div>



<?php
include_once('footer.php');?>