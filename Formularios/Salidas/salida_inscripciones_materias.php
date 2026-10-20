<?php
require __DIR__ . '/../conexion.php';
$inscripciones = consultar("SELECT am.id_asignacion, a.matricula,
    CONCAT(a.nombre, ' ', a.apaterno) AS alumno,
    m.descripcion AS materia,
    CONCAT(p.nombre, ' ', p.apaterno) AS profesor
    FROM asignacion_materia am
    INNER JOIN alumnos a ON a.id_alumno = am.id_alumno
    INNER JOIN materias m ON m.id_materia = am.id_materia
    INNER JOIN profesores p ON p.id_profesor = am.id_profesor
    ORDER BY a.apaterno, a.nombre, m.descripcion");
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAE | Inscripciones a materias</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="cesar" style="background-color:#fef9e7">
    <h1>Inscripciones a materias registradas</h1>
    <?php require __DIR__ . '/../menu.php'; ?>
    <hr>
    <p><a href="../Procesos/proceso_asignar_materia.php">Nueva inscripción</a></p>
    <?php if (count($inscripciones) === 0): ?>
        <p>Todavía no hay alumnos inscritos a materias.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead><tr><th>ID</th><th>Matrícula</th><th>Alumno</th><th>Materia</th><th>Profesor</th></tr></thead>
            <tbody>
                <?php foreach ($inscripciones as $fila): ?>
                    <tr>
                        <td><?= h($fila['id_asignacion']) ?></td>
                        <td><?= h($fila['matricula']) ?></td>
                        <td><?= h($fila['alumno']) ?></td>
                        <td><?= h($fila['materia']) ?></td>
                        <td><?= h($fila['profesor']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
