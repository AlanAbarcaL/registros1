<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estatus = 'ACTIVO';
        if ($descripcion === '') throw new RuntimeException('Faltan datos obligatorios.');
        $s = $conexion->prepare('INSERT INTO grupos (descripcion,estatus) VALUES (?,?)');
        $s->bind_param('ss', $descripcion, $estatus);
        $s->execute();
        $mensaje = 'Grupo registrado correctamente.';
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
            SAE | Grupo
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#eaf2f8">
        <h1>
            Entrada: grupo
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <p>
            <?= h($mensaje) ?>
        </p>
        <hr>
        <h2>
            Registrar grupo
        </h2>
        <form method="post">
            <label>
                Descripción
                <input name="descripcion" placeholder="4T1" maxlength="50" required>
            </label>
            <label>
                Estatus
                <select name="estatus">
                    <option>
                        ACTIVO
                    </option>
                    <option>
                        INACTIVO
                    </option>
                </select>
            </label>
            <button>
                Guardar grupo
            </button>
        </form>
    </body>
</html>
