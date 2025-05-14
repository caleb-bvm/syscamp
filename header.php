<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Bootcamp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
        }
        body {
            padding-top: 80px;
        }
        .hero {
            background: linear-gradient(to right, #007bff, #6610f2);
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .hero h1 {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
        }
        .hero p {
            font-family: 'Verdana', sans-serif;
        }
    </style>
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

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SYSCAMP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="home.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="visitas.php">Visitar Centro Educativo</a></li>
                    <li class="nav-item"><a class="nav-link" href="form_persona.php">Añadir Gestores</a></li>
                    <li class="nav-item"><a class="nav-link" href="escuelas.php">Centros Educativos</a></li>
                    <li class="nav-item"><a class="nav-link" href="reporte_personas.php">Reportes</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Configuración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="ver_usuarios.php">Ver perfil</a></li>
                            <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="cerrar.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex ms-auto">
                    <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Buscar</button>
                </form>

            </div>
        </div>
    </nav>

    <!-- Sección de bienvenida -->
    <section class="hero">
        <div class="container">
            <h1 class="display-3 fw-bold animate__animated animate__fadeIn">¡Bienvenido a SYSCAMP!</h1>
            <p class="lead fs-4 animate__animated animate__fadeIn animate__delay-1s">Tu plataforma para gestionar visitas, gestores y escuelas de manera eficiente.</p>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous"></script>
</body>
</html>
