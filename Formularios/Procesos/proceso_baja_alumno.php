<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT);
    if (!$id) {
        $mensaje = 'Selecciona un alumno.';
    } else {
        try {
            $s = $conexion->prepare("UPDATE alumnos SET estatus='BAJA' WHERE id_alumno=? AND estatus='ALTA'");
            $s->bind_param('i', $id);
            $s->execute();
            $mensaje = $s->affected_rows === 1 ? 'Alumno dado de baja correctamente.' : 'Ese alumno ya estaba de baja o no existe.';
        } catch (Throwable $e) {
            $mensaje = 'No se pudo actualizar al alumno.';
        }
    }
}
$alumnos = consultar("SELECT id_alumno, matricula, nombre, apaterno FROM alumnos WHERE estatus='ALTA' ORDER BY apaterno,nombre");
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>
            SAE | Baja de alumno
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#fef9e7">
        <h1>
            Proceso: dar de baja a un alumno
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <hr>
        <p>
            <?= h($mensaje) ?>
        </p>
        <p>
            La baja cambia el estatus a BAJA; conserva los datos del alumno.
        </p>
        <?php if (!$alumnos): ?>
        <p>
            No hay alumnos activos para dar de baja.
        </p>
        <?php else: ?>
        <form method="post" onsubmit="return confirm('¿Dar de baja a este alumno?');">
            <label>
                Alumno
                <select name="id_alumno" required>
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
            <button>
                Dar de baja
            </button>
        </form>
        <?php endif; ?>
        <p>
            <a href="../Salidas/salida_alumnos_baja.php">
                Ver alumnos de baja
            </a>
        </p>
    </body>
</html>
