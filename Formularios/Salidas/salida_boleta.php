<?php
require __DIR__.'/../conexion.php';
$alumno=filter_input(INPUT_GET,'alumno',FILTER_VALIDATE_INT);
$alumnos=consultar("SELECT id_alumno,matricula,CONCAT(nombre,' ',apaterno,' ',amaterno) nombre_completo FROM alumnos ORDER BY apaterno,nombre");
$filas=[];$error='';
if($alumno){
 try{
 $s=$conexion->prepare("SELECT a.matricula,CONCAT(a.nombre,' ',a.apaterno,' ',a.amaterno) alumno,m.descripcion materia,CONCAT(p.nombre,' ',p.apaterno) profesor,am.calificacion anterior,
 MAX(CASE WHEN cu.unidad=1 THEN cu.calificacion END) u1,
 MAX(CASE WHEN cu.unidad=2 THEN cu.calificacion END) u2,
 MAX(CASE WHEN cu.unidad=3 THEN cu.calificacion END) u3,
 MAX(CASE WHEN cu.unidad=4 THEN cu.calificacion END) u4,
 MAX(CASE WHEN cu.unidad=5 THEN cu.calificacion END) u5
 FROM asignacion_materia am JOIN alumnos a ON a.id_alumno=am.id_alumno JOIN materias m ON m.id_materia=am.id_materia JOIN profesores p ON p.id_profesor=am.id_profesor LEFT JOIN calificaciones_unidad cu ON cu.id_asignacion=am.id_asignacion
 WHERE a.id_alumno=? GROUP BY am.id_asignacion,a.matricula,a.nombre,a.apaterno,a.amaterno,m.descripcion,p.nombre,p.apaterno,am.calificacion ORDER BY m.descripcion");
 $s->bind_param('i',$alumno);$s->execute();$filas=$s->get_result()->fetch_all(MYSQLI_ASSOC);
 }catch(Throwable $e){$error='Falta importar ACTUALIZACION_5_UNIDADES_phpMyAdmin.sql. '.$e->getMessage();}
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Boletas por unidad</title><link rel="stylesheet" href="../estilos.css"></head><body class="cesar">
<h1>Boleta de calificaciones por unidad</h1><?php require __DIR__.'/../menu.php'; ?><hr>
<form method="get"><label for="alumno">Alumno</label> <select name="alumno" id="alumno" required><option value="">Selecciona</option><?php foreach($alumnos as $a): ?><option value="<?= (int)$a['id_alumno'] ?>" <?= (int)$alumno===(int)$a['id_alumno']?'selected':'' ?>><?= h($a['matricula'].' - '.$a['nombre_completo']) ?></option><?php endforeach; ?></select> <button type="submit">Ver boleta</button></form>
<?php if($error): ?><p style="color:darkred"><?= h($error) ?></p><?php endif; ?>
<?php if($alumno && !$error): ?><?php if(!$filas): ?><p>Este alumno no tiene materias inscritas.</p><?php else: ?>
<h2>Boleta de <?= h($filas[0]['alumno']) ?></h2><p>Matrícula: <?= h($filas[0]['matricula']) ?></p>
<div style="overflow-x:auto"><table border="1" cellpadding="7"><thead><tr><th>Materia</th><th>Profesor</th><th>U1</th><th>U2</th><th>U3</th><th>U4</th><th>U5</th><th>Promedio</th><th>Estado</th></tr></thead><tbody>
<?php $promedios=[];foreach($filas as $f):
$notas=[];for($u=1;$u<=5;$u++){if($f['u'.$u]!==null)$notas[]=(float)$f['u'.$u];}
$completa=count($notas)===5;
$promedio=$completa?array_sum($notas)/5:null;
if($promedio!==null)$promedios[]=$promedio;
?>
<tr><td><?= h($f['materia']) ?></td><td><?= h($f['profesor']) ?></td>
<?php for($u=1;$u<=5;$u++): ?><td><?= $f['u'.$u]===null?'—':h($f['u'.$u]) ?></td><?php endfor; ?>
<td><?= $promedio===null?'Pendiente':number_format($promedio,2) ?></td>
<td><?= $promedio===null?'En proceso':($promedio>=70?'Aprobado':'Reprobado') ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<p><strong>Promedio general de materias completas:</strong> <?= $promedios?number_format(array_sum($promedios)/count($promedios),2):'Pendiente' ?></p>
<p>El promedio por materia se calcula cuando están capturadas las cinco unidades. Criterio de aprobación: 70/100.</p>
<?php if(array_filter($filas,static fn($f)=>$f['anterior']!==null)): ?><p>Nota: las calificaciones capturadas antes de este cambio siguen conservadas en la columna anterior de MySQL; no se asignaron a ninguna unidad automáticamente.</p><?php endif; ?>
<button type="button" onclick="window.print()">Imprimir boleta</button>
<?php endif; ?><?php endif; ?></body></html>
