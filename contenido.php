<?php include '../config/loginc.php' ?>
<div> 
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php 
    // Páginas internas básicas
    if ($contenido === 'inicio'): ?>
        <div id="home" class="seccion active">
            <?php include 'inicio.php'; ?>
        </div>

    <?php elseif ($contenido === 'contactos'): ?>
        <div id="contact" class="seccion active">
            <?php include 'contacto.php'; ?>
        </div>

    <?php elseif ($contenido === 'acerca'): ?>
        <div id="about" class="seccion active">
            <?php include 'acerca.php'; ?>
        </div>

    
    <?php 
    // Páginas restringidas según rol
    
    // Páginas dinámicas con prefijo 'pagina_'
    elseif (preg_match('/pagina_/', $contenido)): ?>
        <div class="seccion active">
            <?php 
            $archivo_pagina = "../pages/" . str_replace('pagina_', '', $contenido) . ".php";
            if (file_exists($archivo_pagina)) {
                include $archivo_pagina;
            } else {
                echo "<div class='alert alert-warning'>La página solicitada no existe.</div>";
            }
            ?>
        </div>

    <?php else: ?>
        <div class="alert alert-warning">La página solicitada no existe.</div>
    <?php endif; ?>
</div>
