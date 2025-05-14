<a?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Bootcamp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">SYSCAMP</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="visitas.php">Visitar Centro Educativo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="form_persona.php">Añadir Gestores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="escuelas.php">Centros Educativos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="listar_preguntas.php">Preguntas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="insertar_pregunta.php">Insertar Pregunta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reportes.php">Reportes</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Configuracion
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="ver_usuarios.php">Ver perfil</a></li>
                                <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="cerrar.php">Cerrar sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <?php if (basename($_SERVER['PHP_SELF']) == 'home.php'): ?>
    <div class="container mt-5">
        <div class="p-5 mb-4 rounded-3 shadow" style="background-color: #222222; border-left: 6px solid #0d6efd;">
            <div class="container-fluid py-3 text-center">
                <h1 class="display-5 fw-bold" style="color: var(--brand-color);">
                    Bienvenido/a a <span style="color: #0d6efd;">SYSCAMP</span>
                </h1>
                <p class="fs-5" style="color: var(--text-color);">
                    Plataforma de gestión para visitas, centros educativos y supervisión de gestores. Optimiza tu trabajo desde un solo lugar.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-VB6QZz3D1cWbO4gK6wr2eIRldmQ58U8yHO2UlS1h5i5gG4CWzX8SIk3z4Me/8eUu" crossorigin="anonymous"></script>
</body>
</html>
