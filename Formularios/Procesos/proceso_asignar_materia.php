<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
$tipo = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumno = filter_input(INPUT_POST, 'alumno', FILTER_VALIDATE_INT);
    $materia = filter_input(INPUT_POST, 'materia', FILTER_VALIDATE_INT);
    $profesor = filter_input(INPUT_POST, 'profesor', FILTER_VALIDATE_INT);
    if (!$alumno || !$materia || !$profesor) {
        $mensaje = 'Selecciona un alumno, una materia y un profesor.';
    } else {
        try {
            // Solo permite inscribir alumnos existentes que estén de alta.
            $sql = "INSERT INTO asignacion_materia (id_alumno, id_materia, id_profesor)
                    SELECT a.id_alumno, m.id_materia, p.id_profesor
                    FROM alumnos a
                    CROSS JOIN materias m
                    CROSS JOIN profesores p
                    WHERE a.id_alumno = ? AND m.id_materia = ? AND p.id_profesor = ?
                      AND a.estatus = 'ALTA'";
            $consulta = $conexion->prepare($sql);
            $consulta->bind_param('iii', $alumno, $materia, $profesor);
            $consulta->execute();
            if ($consulta->affected_rows === 1) {
                $mensaje = 'Inscripción a materia guardada correctamente.';
                $tipo = 'ok';
            } else {
                $mensaje = 'No se inscribió. Verifica que el alumno esté dado de alta y que los registros existan.';
            }
            $consulta->close();
        } catch (mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                $mensaje = 'Este alumno ya está inscrito en esa materia.';
            } else {
                $mensaje = 'Ocurrió un error al guardar la inscripción. Revisa la base de datos.';
            }
        }
    }
}
$alumnos = consultar("SELECT id_alumno, matricula, nombre, apaterno FROM alumnos WHERE estatus = 'ALTA' ORDER BY apaterno, nombre");
$materias = consultar('SELECT id_materia, descripcion FROM materias ORDER BY descripcion');
$profesores = consultar('SELECT id_profesor, nombre, apaterno FROM profesores ORDER BY apaterno, nombre');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAE | Inscripción a materias</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="cesar" style="background-color:#fef9e7">
    <h1>Proceso: inscripción a materias</h1>
    <?php require __DIR__ . '/../menu.php'; ?>
    <hr>
    <p>Selecciona el alumno, la materia y el profesor para guardar la inscripción.</p>
    <?php if ($mensaje !== ''): ?>
        <p role="status" style="font-weight:bold; color:<?= $tipo === 'ok' ? '#17652c' : '#9c2424' ?>">
            <?= h($mensaje) ?>
        </p>
    <?php endif; ?>
    <form method="post">
        <label for="alumno">Alumno</label>
        <select name="alumno" id="alumno" required>
            <option value="">Selecciona un alumno</option>
            <?php foreach ($alumnos as $a): ?>
                <option value="<?= h($a['id_alumno']) ?>">
                    <?= h($a['matricula'] . ' - ' . $a['nombre'] . ' ' . $a['apaterno']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="materia">Materia</label>
        <select name="materia" id="materia" required>
            <option value="">Selecciona una materia</option>
            <?php foreach ($materias as $m): ?>
                <option value="<?= h($m['id_materia']) ?>"><?= h($m['descripcion']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="profesor">Profesor</label>
        <select name="profesor" id="profesor" required>
            <option value="">Selecciona un profesor</option>
            <?php foreach ($profesores as $p): ?>
                <option value="<?= h($p['id_profesor']) ?>">
                    <?= h($p['nombre'] . ' ' . $p['apaterno']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Inscribir a materia</button>
    </form>
    <p><a href="../Salidas/salida_inscripciones_materias.php">Ver inscripciones registradas</a></p>
</body>
</html>
