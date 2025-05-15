
  <?php include_once('header.php'); ?>

  <div class="container py-5">
    <h2 class="text-center mb-5">📄 Reportes Disponibles</h2>

    <div class="row row-cols-1 row-cols-md-2 g-4">
      <div class="col">
        <a href="reporte_personas.php" class="text-decoration-none">
          <div class="card card-report shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <i class="bi bi-person-lines-fill report-icon"></i>
              <div>
                <h5 class="card-title text-dark mb-0">Reporte de Personas</h5>
                <p class="text-muted mb-0">Visualiza y exporta datos del personal registrado.</p>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="reporte_visitas.php" class="text-decoration-none">
          <div class="card card-report shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <i class="bi bi-clipboard-check report-icon"></i>
              <div>
                <h5 class="card-title text-dark mb-0">Reporte de Visitas</h5>
                <p class="text-muted mb-0">Consulta los registros de visitas por fecha y categoría.</p>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="reporte_instituciones.php" class="text-decoration-none">
          <div class="card card-report shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <i class="bi bi-building report-icon"></i>
              <div>
                <h5 class="card-title text-dark mb-0">Reporte de Instituciones</h5>
                <p class="text-muted mb-0">Listado de centros educativos registrados en el sistema.</p>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="reporte_preguntas.php" class="text-decoration-none">
          <div class="card card-report shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <i class="bi bi-question-circle report-icon"></i>
              <div>
                <h5 class="card-title text-dark mb-0">Reporte de Preguntas</h5>
                <p class="text-muted mb-0">Preguntas por categoría aplicadas durante las visitas.</p>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <?php include_once('footer.php'); ?>

