<?php
include_once('header.php');?>

<?php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once("configuracion/conexion.php");

$username = $_SESSION['username'];

// Obtener datos del usuario actual
$query_usuario = "SELECT p.*, d_lab.nombre_departamento AS nombre_departamento_labora, d_res.nombre_distrito AS nombre_distrito_reside
                FROM persona p
                LEFT JOIN departamento d_lab ON p.id_departamento_labora = d_lab.id_departamento
                LEFT JOIN distrito d_res ON p.id_distrito_reside = d_res.id_distrito
                WHERE p.username = $1";
$resultado_usuario = pg_query_params($conexion, $query_usuario, array($username));
$usuario = pg_fetch_assoc($resultado_usuario);

// Obtener lista de departamentos
$departamentos = [];
$consulta_departamentos = pg_query($conexion, "SELECT id_departamento, nombre_departamento FROM departamento ORDER BY nombre_departamento ASC");
while ($fila_departamento = pg_fetch_assoc($consulta_departamentos)) {
    $departamentos[] = $fila_departamento;
}

// Obtener lista de distritos
$distritos = [];
$consulta_distritos = pg_query($conexion, "SELECT id_distrito, nombre_distrito FROM distrito ORDER BY nombre_distrito ASC");
while ($fila_distrito = pg_fetch_assoc($consulta_distritos)) {
    $distritos[] = $fila_distrito;
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $documento = $_POST["documento"];
    $clave = $_POST["clave"];
    $departamento_labora = $_POST["departamento_labora"];
    $distrito_reside = $_POST["distrito_reside"];

    if (!empty($clave)) {
        $update_query = "UPDATE persona SET correo_persona = $1, nombre_persona = $2, apellido_persona = $3, documento_de_identificacion = $4, clave_persona = $5, id_departamento_labora = $6, id_distrito_reside = $7 WHERE username = $8";
        $params = array($correo, $nombre, $apellido, $documento, $clave, $departamento_labora, $distrito_reside, $username);
    } else {
        $update_query = "UPDATE persona SET correo_persona = $1, nombre_persona = $2, apellido_persona = $3, documento_de_identificacion = $4, id_departamento_labora = $5, id_distrito_reside = $6 WHERE username = $7";
        $params = array($correo, $nombre, $apellido, $documento, $departamento_labora, $distrito_reside, $username);
    }

    pg_query_params($conexion, $update_query, $params);
    header("Location: home.php"); // Redirigir a la página principal después de guardar
    exit;
}
?>


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
            <select name="departamento_labora" class="form-select" required>
                <option value="">Seleccione un departamento</option>
                <?php foreach ($departamentos as $depto): ?>
                    <option value="<?= $depto['id_departamento'] ?>" <?= ($usuario['id_departamento_labora'] == $depto['id_departamento']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($depto['nombre_departamento']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Distrito donde reside</label>
            <select name="distrito_reside" class="form-select" required>
                <option value="">Seleccione un distrito</option>
                <?php foreach ($distritos as $dist): ?>
                    <option value="<?= $dist['id_distrito'] ?>" <?= ($usuario['id_distrito_reside'] == $dist['id_distrito']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dist['nombre_distrito']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar Cambios</button>
        <a href="home.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once("footer.php") ?>