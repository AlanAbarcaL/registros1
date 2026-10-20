<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumno = filter_input(INPUT_POST, 'alumno', FILTER_VALIDATE_INT);
    $profesor = filter_input(INPUT_POST, 'profesor', FILTER_VALIDATE_INT);
    $destino = filter_input(INPUT_POST, 'destino', FILTER_VALIDATE_INT);
    if (!$alumno || !$profesor || !$destino) {
        $mensaje = 'Selecciona alumno, profesor y grupo.';
    } else {
        try {
            $s = $conexion->prepare("INSERT INTO asignacion_grupo (id_grupo,id_alumno,id_profesor) SELECT t.id_grupo,a.id_alumno,p.id_profesor FROM grupos t JOIN alumnos a ON a.id_alumno=? JOIN profesores p ON p.id_profesor=? WHERE t.id_grupo=? AND a.estatus='ALTA' AND t.estatus='ACTIVO'");
            $s->bind_param('iii', $alumno, $profesor, $destino);
            $s->execute();
            $mensaje = $s->affected_rows === 1 ? 'Asignación guardada.' : 'Comprueba que el alumno esté de alta y el destino esté disponible.';
        } catch (Throwable $e) {
            $mensaje = 'No se guardó. Puede que este alumno ya tenga grupo o esta materia asignada.';
        }
    }
}
$alumnos = consultar("SELECT id_alumno, matricula, nombre, apaterno FROM alumnos WHERE estatus='ALTA' ORDER BY apaterno, nombre");
$profesores = consultar('SELECT id_profesor, nombre, apaterno FROM profesores ORDER BY apaterno, nombre');
$destinos = consultar("SELECT id_grupo, descripcion FROM grupos WHERE estatus='ACTIVO' ORDER BY descripcion");
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>
            SAE | Asignar grupo
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#fef9e7">
        <h1>
            Proceso: asignar grupo
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <hr>
        <p>
            <?= h($mensaje) ?>
        </p>
        <form method="post">
            <label>
                Alumno
                <select name="alumno" required>
                    <option value="">
                        Selecciona
                    </option>
                    <?php foreach ($alumnos as $a): ?>
                    <option value="<?= h($a['id_alumno']) ?>">
                        <?= h($a['matricula'].' - '.$a['nombre'].' '.$a['apaterno']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br>
            <br>
            <label>
                Grupo
                <select name="destino" required>
                    <option value="">
                        Selecciona
                    </option>
                    <?php foreach ($destinos as $d): ?>
                    <option value="<?= h($d['id_grupo']) ?>">
                        <?= h($d['descripcion']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br>
            <br>
            <label>
                Profesor
                <select name="profesor" required>
                    <option value="">
                        Selecciona
                    </option>
                    <?php foreach ($profesores as $f): ?>
                    <option value="<?= h($f['id_profesor']) ?>">
                        <?= h($f['nombre'].' '.$f['apaterno']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br>
            <br>
            <button>
                Asignar grupo
            </button>
        </form>
    </body>
</html>
