<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $descripcion = trim($_POST['descripcion'] ?? '');
        if ($descripcion === '') throw new RuntimeException('Faltan datos obligatorios.');
        $s = $conexion->prepare('INSERT INTO materias (descripcion) VALUES (?)');
        $s->bind_param('s', $descripcion);
        $s->execute();
        $mensaje = 'Materia registrado correctamente.';
    } catch (Throwable $e) {
        $mensaje = 'No se guardó. Revisa los datos y que no exista un registro repetido.';
    }
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>
            SAE | Materia
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#eaf2f8">
        <h1>
            Entrada: materia
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <p>
            <?= h($mensaje) ?>
        </p>
        <hr>
        <h2>
            Registrar materia
        </h2>
        <form method="post">
            <label>
                Descripción
                <input name="descripcion" maxlength="100" required>
            </label>
            <button>
                Guardar materia
            </button>
        </form>
    </body>
</html>
