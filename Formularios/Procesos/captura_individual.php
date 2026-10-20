<?php
require __DIR__.'/../conexion.php';
$mensaje=''; $ok=false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $id=filter_input(INPUT_POST,'inscripcion',FILTER_VALIDATE_INT);
 $unidad=filter_input(INPUT_POST,'unidad',FILTER_VALIDATE_INT);
 $texto=trim((string)($_POST['calificacion']??''));
 if (!$id || !$unidad || $unidad<1 || $unidad>5 || $texto==='' || !is_numeric($texto) || (float)$texto<0 || (float)$texto>100) {
   $mensaje='Selecciona inscripción, unidad (1 a 5) y calificación entre 0 y 100.';
 } else {
  try {
   $v=$conexion->prepare("SELECT am.id_asignacion FROM asignacion_materia am JOIN alumnos a ON a.id_alumno=am.id_alumno WHERE am.id_asignacion=? AND a.estatus='ALTA'");
   $v->bind_param('i',$id);$v->execute();
   if (!$v->get_result()->num_rows) throw new Exception('La inscripción no existe o el alumno está de baja.');
   $nota=(float)$texto;
   $s=$conexion->prepare("INSERT INTO calificaciones_unidad (id_asignacion,unidad,calificacion) VALUES (?,?,?) ON DUPLICATE KEY UPDATE calificacion=VALUES(calificacion)");
   $s->bind_param('iid',$id,$unidad,$nota);$s->execute();
   $mensaje='Unidad '.$unidad.' guardada correctamente.';$ok=true;
  } catch(Throwable $e) {$mensaje='No se pudo guardar. Comprueba que importaste ACTUALIZACION_5_UNIDADES_phpMyAdmin.sql. Detalle: '.$e->getMessage();}
 }
}
$inscripciones=consultar("SELECT am.id_asignacion,a.matricula,CONCAT(a.nombre,' ',a.apaterno) alumno,m.descripcion materia FROM asignacion_materia am JOIN alumnos a ON a.id_alumno=am.id_alumno JOIN materias m ON m.id_materia=am.id_materia WHERE a.estatus='ALTA' ORDER BY a.apaterno,a.nombre,m.descripcion");
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Captura individual por unidad</title><link rel="stylesheet" href="../estilos.css"></head><body class="cesar">
<h1>Captura individual de calificaciones por unidad</h1><?php require __DIR__.'/../menu.php'; ?><hr>
<p>Selecciona al alumno inscrito, la unidad (1 a 5) y la calificación. Puedes modificarla volviendo a guardar esa misma unidad.</p>
<?php if($mensaje): ?><p style="color:<?= $ok?'green':'darkred' ?>"><?= h($mensaje) ?></p><?php endif; ?>
<form method="post">
<label for="inscripcion">Alumno y materia</label><br><select id="inscripcion" name="inscripcion" required><option value="">Selecciona</option><?php foreach($inscripciones as $i): ?><option value="<?= (int)$i['id_asignacion'] ?>"><?= h($i['matricula'].' - '.$i['alumno'].' | '.$i['materia']) ?></option><?php endforeach; ?></select><br><br>
<label for="unidad">Unidad</label><br><select id="unidad" name="unidad" required><?php for($u=1;$u<=5;$u++): ?><option value="<?= $u ?>">Unidad <?= $u ?></option><?php endfor; ?></select><br><br>
<label for="calificacion">Calificación (0 a 100)</label><br><input type="number" id="calificacion" name="calificacion" min="0" max="100" step="0.01" required><br><br>
<button type="submit">Guardar calificación</button></form>
<p><a href="../Salidas/salida_boleta.php">Ver boletas por unidad</a></p></body></html>
