<?php
require __DIR__ . '/../conexion.php';
$resultado = $conexion->query("SELECT g.descripcion AS grupo,g.estatus,COUNT(ag.id_alumno) AS alumnos_asignados FROM grupos g LEFT JOIN asignacion_grupo ag ON ag.id_grupo=g.id_grupo GROUP BY g.id_grupo,g.descripcion,g.estatus ORDER BY g.descripcion");
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>
            SAE | Lista de grupos
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#e8f8f5">
        <h1>
            Salida: lista de grupos
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <hr>
        <?php if ($resultado->num_rows === 0): ?>
        <p>
            Sin registros por ahora.
        </p>
        <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <?php foreach ($resultado->fetch_fields() as $campo): ?>
                    <th>
                        <?= h(str_replace('_', ' ', ucfirst($campo->name))) ?>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <?php foreach ($fila as $valor): ?>
                    <td>
                        <?= h($valor) ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </body>
</html>
