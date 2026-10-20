<?php
require __DIR__.'/../conexion.php';
$materia=filter_input(INPUT_GET,'materia',FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST,'materia',FILTER_VALIDATE_INT);
$unidad=filter_input(INPUT_GET,'unidad',FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST,'unidad',FILTER_VALIDATE_INT);
if (!$unidad || $unidad<1 || $unidad>5) $unidad=1;
$mensaje='';$ok=false;
if ($_SERVER['REQUEST_METHOD']==='POST' && $materia) {
 $datos=$_POST['calificaciones']??[];
 if (!is_array($datos)) $datos=[];
 $valores=[];
 foreach($datos as $id=>$nota) {
  if(!ctype_digit((string)$id) || (int)$id<1){$mensaje='Identificador inválido.';break;}
  if(!is_string($nota)){$mensaje='Calificación inválida.';break;}
  $nota=trim($nota);
  if($nota==='') continue;
  if(!is_numeric($nota) || (float)$nota<0 || (float)$nota>100){$mensaje='Todas las calificaciones deben estar entre 0 y 100.';break;}
  $valores[(int)$id]=(float)$nota;
 }
 if($mensaje==='') {
  if(!$valores) $mensaje='Escribe al menos una calificación.';
  else {
   try {
    $conexion->begin_transaction();
    $v=$conexion->prepare("SELECT am.id_asignacion FROM asignacion_materia am JOIN alumnos a ON a.id_alumno=am.id_alumno WHERE am.id_asignacion=? AND am.id_materia=? AND a.estatus='ALTA'");
    $s=$conexion->prepare("INSERT INTO calificaciones_unidad (id_asignacion,unidad,calificacion) VALUES (?,?,?) ON DUPLICATE KEY UPDATE calificacion=VALUES(calificacion)");
    foreach($valores as $id=>$nota) {
     $v->bind_param('ii',$id,$materia);$v->execute();
     if($v->get_result()->num_rows!==1) throw new Exception('Inscripción inválida: no se guardó ningún cambio.');
     $s->bind_param('iid',$id,$unidad,$nota);$s->execute();
    }
    $conexion->commit();$ok=true;$mensaje='Se guardaron '.count($valores).' calificaciones de la unidad '.$unidad.'.';
   } catch(Throwable $e){$conexion->rollback();$mensaje='No se guardaron cambios. Comprueba que importaste el SQL nuevo. Detalle: '.$e->getMessage();}
  }
 }
}
$materias=consultar("SELECT id_materia,descripcion FROM materias ORDER BY descripcion");
$alumnos=[];
if($materia) {
 try {
  $s=$conexion->prepare("SELECT am.id_asignacion,a.matricula,CONCAT(a.nombre,' ',a.apaterno,' ',a.amaterno) alumno,cu.calificacion FROM asignacion_materia am JOIN alumnos a ON a.id_alumno=am.id_alumno LEFT JOIN calificaciones_unidad cu ON cu.id_asignacion=am.id_asignacion AND cu.unidad=? WHERE am.id_materia=? AND a.estatus='ALTA' ORDER BY a.apaterno,a.nombre");
  $s->bind_param('ii',$unidad,$materia);$s->execute();$alumnos=$s->get_result()->fetch_all(MYSQLI_ASSOC);
 }catch(Throwable $e){$mensaje='Falta importar ACTUALIZACION_5_UNIDADES_phpMyAdmin.sql. '.$e->getMessage();}
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Captura masiva por unidad</title><link rel="stylesheet" href="../estilos.css"></head><body class="cesar">
<h1>Captura masiva de calificaciones por unidad</h1><?php require __DIR__.'/../menu.php'; ?><hr>
<p>Elige materia y unidad. Los campos vacíos conservan las calificaciones anteriores.</p>
<form method="get"><label for="materia">Materia</label> <select id="materia" name="materia" required><option value="">Selecciona</option><?php foreach($materias as $m): ?><option value="<?= (int)$m['id_materia'] ?>" <?= (int)$materia===(int)$m['id_materia']?'selected':'' ?>><?= h($m['descripcion']) ?></option><?php endforeach; ?></select>
<label for="unidad">Unidad</label> <select id="unidad" name="unidad"><?php for($u=1;$u<=5;$u++): ?><option value="<?= $u ?>" <?= $unidad===$u?'selected':'' ?>>Unidad <?= $u ?></option><?php endfor; ?></select> <button type="submit">Mostrar alumnos</button></form>
<?php if($mensaje): ?><p style="color:<?= $ok?'green':'darkred' ?>"><?= h($mensaje) ?></p><?php endif; ?>
<?php if($materia): ?><?php if(!$alumnos): ?><p>No hay alumnos inscritos en esta materia o falta importar el SQL nuevo.</p><?php else: ?>
<h2>Unidad <?= $unidad ?></h2><form method="post"><input type="hidden" name="materia" value="<?= (int)$materia ?>"><input type="hidden" name="unidad" value="<?= $unidad ?>">
<table border="1" cellpadding="7"><thead><tr><th>Matrícula</th><th>Alumno</th><th>Calificación actual</th><th>Nueva calificación</th></tr></thead><tbody>
<?php foreach($alumnos as $a): ?><tr><td><?= h($a['matricula']) ?></td><td><?= h($a['alumno']) ?></td><td><?= $a['calificacion']===null?'Pendiente':h($a['calificacion']) ?></td><td><input type="number" min="0" max="100" step="0.01" name="calificaciones[<?= (int)$a['id_asignacion'] ?>]" placeholder="0 a 100" style="width:110px"></td></tr><?php endforeach; ?>
</tbody></table><br><button type="submit">Guardar unidad <?= $unidad ?></button></form><?php endif; ?><?php endif; ?>
<p><a href="../Salidas/salida_boleta.php">Consultar boletas</a></p></body></html>
