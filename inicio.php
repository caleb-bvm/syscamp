<?php
include_once('configuracion/conexion.php');

$totalVisitas = 0;
$totalPreguntasRespondidas = 0;
$totalInstitucionesVisitadas = 0;
$tasaCompletitud = 0;
$totalInstitucionesSistema = 0;
$totalGestoresActivos = 0;
$institucionesPorSector = [];

$queryVisitas = "SELECT COUNT(*) AS total_visitas FROM respuestas";
$resultVisitas = pg_query($conexion, $queryVisitas);
if ($resultVisitas && pg_num_rows($resultVisitas) > 0) {
  $rowVisitas = pg_fetch_assoc($resultVisitas);
  $totalVisitas = $rowVisitas['total_visitas'];
}

$queryPreguntasRespondidas = "SELECT COUNT(*) AS total_preguntas_respondidas FROM respuestas_detalladas";
$resultPreguntasRespondidas = pg_query($conexion, $queryPreguntasRespondidas);
if ($resultPreguntasRespondidas && pg_num_rows($resultPreguntasRespondidas) > 0) {
  $rowPreguntasRespondidas = pg_fetch_assoc($resultPreguntasRespondidas);
  $totalPreguntasRespondidas = $rowPreguntasRespondidas['total_preguntas_respondidas'];
}

$queryInstituciones = "SELECT COUNT(DISTINCT id_institucion) AS total_instituciones FROM respuestas";
$resultInstituciones = pg_query($conexion, $queryInstituciones);
if ($resultInstituciones && pg_num_rows($resultInstituciones) > 0) {
  $rowInstituciones = pg_fetch_assoc($resultInstituciones);
  $totalInstitucionesVisitadas = $rowInstituciones['total_instituciones'];
}

$queryTotalPreguntas = "SELECT COUNT(*) AS total_preguntas_sistema FROM preguntas";
$resultTotalPreguntas = pg_query($conexion, $queryTotalPreguntas);
if ($resultTotalPreguntas && pg_num_rows($resultTotalPreguntas) > 0) {
  $rowTotalPreguntas = pg_fetch_assoc($resultTotalPreguntas);
  $totalPreguntasSistema = $rowTotalPreguntas['total_preguntas_sistema'];
  if ($totalVisitas > 0 && $totalPreguntasSistema > 0) {
    $tasaCompletitud = ($totalPreguntasRespondidas / ($totalVisitas * $totalPreguntasSistema)) * 100;
    $tasaCompletitud = round($tasaCompletitud, 2);
  }
}

$queryTotalInstituciones = "SELECT COUNT(*) AS total_instituciones_sistema FROM institucion";
$resultTotalInstituciones = pg_query($conexion, $queryTotalInstituciones);
if ($resultTotalInstituciones && pg_num_rows($resultTotalInstituciones) > 0) {
  $rowTotal = pg_fetch_assoc($resultTotalInstituciones);
  $totalInstitucionesSistema = $rowTotal['total_instituciones_sistema'];
}

$queryGestores = "SELECT COUNT(*) AS total_gestores FROM persona WHERE id_rol = 3";
$resultGestores = pg_query($conexion, $queryGestores);
if ($resultGestores && pg_num_rows($resultGestores) > 0) {
  $rowGestores = pg_fetch_assoc($resultGestores);
  $totalGestoresActivos = $rowGestores['total_gestores'];
}

$querySectores = "SELECT sector_institucion, COUNT(*) AS total FROM institucion GROUP BY sector_institucion";
$resultSectores = pg_query($conexion, $querySectores);
if ($resultSectores && pg_num_rows($resultSectores) > 0) {
  while ($row = pg_fetch_assoc($resultSectores)) {
    $institucionesPorSector[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio - Sistema de Control de Visitas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <section id="inicio" class="position-relative">
    <div class="bg-image position-relative" style="height: 500px; background-image: url('./recursos/images.jpg'); background-size: cover; background-position: center;">
      <div class="position-absolute w-100 h-100" style="background-color: rgba(0,20,60,0.4);"></div>
      <div class="container position-relative h-100">
        <div class="row h-100 align-items-center">
          <div class="col-md-8">
            <h1 class="display-4 fw-bold mb-4 text-white">Sistema de Control de Visitas a Instituciones Educativas</h1>
            <p class="lead mb-4 text-white">Plataforma diseñada para mejorar la supervisión y seguimiento de visitas a centros educativos en todo El Salvador, garantizando la calidad y transparencia del sistema educativo nacional.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light py-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-3 mb-4">
          <div class="card h-100 border-0 shadow-sm card-bg-custom">
            <div class="card-body">
              <h5 class="card-title text-brand">Visitas Realizadas</h5>
              <h1 class="display-4"><?php echo $totalVisitas; ?></h1>
              <p class="text-subtle">Registros en total</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="card h-100 border-0 shadow-sm card-bg-custom">
            <div class="card-body">
              <h5 class="card-title text-brand">Instituciones Visitadas</h5>
              <h1 class="display-4"><?php echo $totalInstitucionesVisitadas; ?></h1>
              <p class="text-subtle">Instituciones únicas</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="card h-100 border-0 shadow-sm card-bg-custom">
            <div class="card-body">
              <h5 class="card-title text-brand">Total Instituciones</h5>
              <h1 class="display-4"><?php echo $totalInstitucionesSistema; ?></h1>
              <p class="text-subtle">Registradas en el sistema</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="card h-100 border-0 shadow-sm card-bg-custom">
            <div class="card-body">
              <h5 class="card-title text-brand">Tasa de Completitud</h5>
              <h1 class="display-4"><?php echo $tasaCompletitud; ?>%</h1>
              <p class="text-subtle">Promedio general</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row text-center">
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm card-bg-custom">
            <div class="card-body">
              <h5 class="card-title text-brand">Gestores Activos</h5>
              <h1 class="display-4"><?php echo $totalGestoresActivos; ?></h1>
              <p class="text-subtle">Registrados con rol de gestión</p>
            </div>
          </div>
        </div>

        <?php foreach ($institucionesPorSector as $sector): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm card-bg-custom">
              <div class="card-body">
                <h5 class="card-title text-brand">Sector: <?php echo ucfirst($sector['sector_institucion']); ?></h5>
                <h1 class="display-4"><?php echo $sector['total']; ?></h1>
                <p class="text-subtle">Instituciones</p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'footer.php'; ?>
</body>
</html>
