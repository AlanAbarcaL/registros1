<?php
// Menú desplegable original, con rutas que se adaptan a la carpeta del proyecto.
$rutaScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/Formularios/index.php');
$base = dirname($rutaScript);
foreach (['/Entradas/', '/Procesos/', '/Salidas/'] as $seccion) {
    if (strpos($rutaScript, $seccion) !== false) {
        $base = substr($rutaScript, 0, strpos($rutaScript, $seccion));
        break;
    }
}
$base = rtrim($base, '/');
$opciones = [
 'Inicio'=>['Inicio'=>'index.php'],
 'Catálogos'=>[
    'Catálogo de alumnos'=>'Entradas/crud_alumnos.php',
    'Catálogo de grupos'=>'Entradas/crud_grupos.php',
    'Catálogo de materias'=>'Entradas/crud_materias.php',
    'Catálogo de profesores'=>'Entradas/crud_profesores.php'
 ],
 'Procesos'=>[
    'Asignar grupo'=>'Procesos/proceso_asignar_grupo.php',
    'Inscripción a materias'=>'Procesos/proceso_asignar_materia.php',
    'Captura individual de calificaciones'=>'Procesos/captura_individual.php',
    'Captura masiva de calificaciones'=>'Procesos/captura_masiva.php',
    'Dar de baja alumno'=>'Procesos/proceso_baja_alumno.php'
 ],
 'Reportes'=>[
    'Lista de alumnos'=>'Salidas/salida_alumnos.php',
    'Lista de materias'=>'Salidas/salida_materias.php',
    'Materias inscritas'=>'Salidas/salida_inscripciones_materias.php',
    'Lista de grupos'=>'Salidas/salida_grupos.php',
    'Grupos asignados'=>'Salidas/salida_grupos_asignados.php',
    'Boletas'=>'Salidas/salida_boleta.php',
    'Alumnos de baja'=>'Salidas/salida_alumnos_baja.php'
 ]
];
$paginaActual = basename($rutaScript);
?>
<div class="contenedor-menu-visible">
  <nav class="menu-visible" aria-label="Navegación principal">
    <?php foreach ($opciones as $grupo => $rutas): ?>
      <div class="grupo-menu-visible">
        <span class="titulo-grupo-menu"><?= h($grupo) ?></span>
        <?php foreach ($rutas as $etiqueta => $ruta): ?>
          <a class="<?= basename($ruta) === $paginaActual ? 'actual' : '' ?>" href="<?= h($base . '/' . $ruta) ?>"><?= h($etiqueta) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </nav>
</div>
