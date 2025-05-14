<?php
$mensaje = "";
session_start();
include('configuracion/conexion.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para seleccionar los campos necesarios de la tabla persona
    $query = "SELECT id_persona, username, clave_persona, id_rol, nombre_persona 
              FROM persona 
              WHERE username = '$username'";
    $resultado = pg_query($conexion, $query);
    if ($resultado && pg_num_rows($resultado) == 1) {
        $usuario = pg_fetch_assoc($resultado);
        // Verificar la contraseña usando el campo clave_persona
        if ($usuario['clave_persona'] === $password) {
            $_SESSION['id_persona'] = $usuario['id_persona'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['id_rol'] = $usuario['id_rol'];
            $_SESSION['nombre_persona'] = $usuario['nombre_persona'];

            header("Location: index.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 400px;">
        <h3 class="text-center mb-4">Iniciar Sesión</h3>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <br>
            <button type="submit" class="btn btn-primary w-100" style="height:45px">Ingresar</button>
            <br>
            <div class="text-center my-2">——— o inicia sesión con ———</div>
            <br>
            <button type="submit" class="btn btn-light w-100">
                <img src="google-brands.svg" alt="Google Icon" style="margin-right: 8px; height: 30px;">
            </button>
            <br>
        </form>
    </div>
    </div>
</body>
</html>
