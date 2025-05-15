<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once("configuracion/conexion.php");

$username = $_SESSION['username'];

// Obtener datos del usuario actual
$query = "SELECT * FROM persona WHERE username = $1";
$resultado = pg_query_params($conexion, $query, array($username));
$usuario = pg_fetch_assoc($resultado);

// Obtener lista de departamentos
$departamentos = [];
$consultaDepto = pg_query($conexion, "SELECT id_departamento, nombre_departamento FROM departamentos");
while ($fila = pg_fetch_assoc($consultaDepto)) {
    $departamentos[] = $fila;
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $documento = $_POST["documento"];
    $clave = $_POST["clave"];
    $departamento = $_POST["departamento"];

    if (!empty($clave)) {
        $update_query = "UPDATE persona SET correo_persona = $1, nombre_persona = $2, apellido_persona = $3, documento_de_identificacion = $4, clave_persona = $5, id_departamento_labora = $6 WHERE username = $7";
        $params = array($correo, $nombre, $apellido, $documento, $clave, $departamento, $username);
    } else {
        $update_query = "UPDATE persona SET correo_persona = $1, nombre_persona = $2, apellido_persona = $3, documento_de_identificacion = $4, id_departamento_labora = $5 WHERE username = $6";
        $params = array($correo, $nombre, $apellido, $documento, $departamento, $username);
    }

    pg_query_params($conexion, $update_query, $params);
    header("Location: ver_usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Editar Perfil</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo_persona']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre_persona']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido_persona']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Documento de Identificación</label>
            <input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($usuario['documento_de_identificacion']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nueva Clave (opcional)</label>
            <input type="password" name="clave" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Departamento donde labora</label>
            <select name="departamento" class="form-select" required>
                <option value="">Seleccione un departamento</option>
                <?php foreach ($departamentos as $d): ?>
                    <option value="<?= $d['id_departamento'] ?>" <?= ($usuario['id_departamento_labora'] == $d['id_departamento']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nombre_departamento']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="home.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
