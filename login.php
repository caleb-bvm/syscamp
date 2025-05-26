<?php
session_start();

$mensaje = "";
include('configuracion/conexion.php');

if (isset($_SESSION['username'])) {
    switch ($_SESSION['id_rol']) {
        case '1':
            header("Location: home.php");
            exit();
        case '3':
            header("Location: gestor.php");
            exit();
        default:
            header("Location: home.php");
            exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT id_persona, username, clave_persona, id_rol, nombre_persona
              FROM persona
              WHERE username = '$username'";
    $resultado = pg_query($conexion, $query);

    if ($resultado && pg_num_rows($resultado) == 1) {
        $usuario = pg_fetch_assoc($resultado);
        if ($usuario['clave_persona'] === $password) {
            $_SESSION['id_persona'] = $usuario['id_persona'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['id_rol'] = $usuario['id_rol'];
            $_SESSION['nombre_persona'] = $usuario['nombre_persona'];

            switch ($_SESSION['id_rol']) {
                case '1':
                    header("Location: home.php");
                    exit();
                case '3':
                    header("Location: gestor.php");
                    exit();
                default:
                    header("Location: home.php");
                    exit();
            }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --body-bg: #1C1B1F;
            --text-color: #E6E1E5;
            --header-bg: #2B2930;
            --brand-color: #D0BCFF;
            --nav-link-color: #CAC4D0;
            --form-control-bg: #36343B;
            --form-control-border: #8E889C;
            --form-control-text: #E6E1E5;
            --form-control-placeholder: #CAC4D0;
            --btn-primary-color: #D0BCFF;
            --btn-primary-hover: #B69DF8;
            --btn-primary-text-color: #381E72;
            --alert-danger-bg: #4F2F32;
            --alert-danger-text: #FFB4AB;
            --alert-danger-border: #8C2C32;
            --google-btn-bg: #36343B;
            --google-btn-border: #8E889C;
            --google-btn-text: #E6E1E5;
            --google-btn-hover-bg: #4D4853;
            --border-radius: 0.75rem;
            --border-radius-lg: 1.25rem;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--body-bg);
            color: var(--text-color);
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        .content-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
        }

        .form-card {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.4);
            background-color: var(--header-bg);
            border: 1px solid var(--form-control-border);
            color: var(--text-color);
        }

        .hr-text {
            line-height: 1em;
            position: relative;
            border: 0;
            color: var(--nav-link-color);
            text-align: center;
            height: 1.5em;
            opacity: .7;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .hr-text:before {
            content: '';
            background: linear-gradient(to right, transparent, var(--form-control-border), transparent);
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
        }

        .hr-text:after {
            content: attr(data-content);
            position: relative;
            display: inline-block;
            color: var(--nav-link-color);
            padding: 0 .5em;
            background-color: var(--header-bg);
        }

        .btn-google {
            background-color: var(--google-btn-bg);
            color: var(--google-btn-text);
            border: 1px solid var(--google-btn-border);
            border-radius: var(--border-radius-lg);
            padding: 0.85rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-google:hover {
            background-color: var(--google-btn-hover-bg);
            color: var(--google-btn-text);
            border-color: var(--google-btn-hover-bg);
        }

        .btn-google img {
            margin-right: 10px;
            height: 24px;
        }

        .text-brand { color: var(--brand-color) !important; }
        .text-main { color: var(--text-color) !important; }
        .text-subtle { color: var(--nav-link-color) !important; }

        .form-label { color: var(--text-color) !important; }

        .form-control {
            background-color: var(--form-control-bg);
            border-color: var(--form-control-border);
            color: var(--form-control-text);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
        }

        .form-control::placeholder {
            color: var(--form-control-placeholder);
            opacity: 0.7;
        }

        .form-control:focus {
            background-color: var(--form-control-bg);
            border-color: var(--brand-color);
            color: var(--form-control-text);
            box-shadow: 0 0 0 0.25rem rgba(208, 188, 255, 0.25);
        }

        .input-group-text {
            background-color: var(--form-control-bg);
            border-color: var(--form-control-border);
            color: var(--nav-link-color);
            border-radius: var(--border-radius);
        }

        .btn {
            border-radius: var(--border-radius-lg);
            padding: 0.85rem 1.75rem;
            font-size: 1.05rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            color: var(--btn-primary-text-color);
            background-color: var(--btn-primary-color);
            border-color: var(--btn-primary-color);
        }

        .btn-primary:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            box-shadow: 0 4px 8px rgba(208, 188, 255, 0.3);
        }

        .alert-danger {
            background-color: var(--alert-danger-bg);
            color: var(--alert-danger-text);
            border-color: var(--alert-danger-border);
            border-radius: var(--border-radius);
            padding: 1rem 1.25rem;
        }

        .alert-danger .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        a.text-decoration-none.text-subtle {
            color: var(--nav-link-color) !important;
        }

        a.text-decoration-none.text-subtle:hover {
            color: var(--brand-color) !important;
        }
    </style>
</head>
<body>
    <div class="content-wrap">
        <div class="form-card">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-brand">Bienvenido</h3>
                <p class="text-subtle">Inicia sesión para acceder al sistema.</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Tu nombre de usuario" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Tu contraseña" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Ingresar</button>
                </div>

                <div class="hr-text my-4" data-content="O inicia sesión con"></div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-google">
                        <img src="google-brands.svg" alt="Google Icon"> Continuar con Google
                    </button>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none text-subtle">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'footer.php' ?>
</body>
</html>
