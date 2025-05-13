<?php
include("configuracion/conexion.php");

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $password_hash = $_POST['password_hash'];
    $rol_id = $_POST['rol_id'];

    // Insertar en la base de datos
    $query = "INSERT INTO public.usuarios (username, email, nombres, apellidos, password_hash, rol_id) 
              VALUES ('$username', '$email', '$nombres', '$apellidos', '$password_hash', $rol_id)";
    
    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        $mensaje = "✅ Usuario insertado correctamente.";
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
include_once("header.php");
?>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario para Insertar Usuario</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" id="username" name="username" required>
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
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
        </form>
    </div>

<?php include_once("footer.php") ?>
