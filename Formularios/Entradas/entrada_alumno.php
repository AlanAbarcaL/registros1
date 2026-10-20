<?php
require __DIR__ . '/../conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $matricula = trim($_POST['matricula'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apaterno = trim($_POST['apaterno'] ?? '');
        $amaterno = trim($_POST['amaterno'] ?? '');
        $domicilio = trim($_POST['domicilio'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $estatus = 'ALTA';
        if ($matricula === '' || $nombre === '' || $apaterno === '') throw new RuntimeException('Faltan datos obligatorios.');
        $s = $conexion->prepare('INSERT INTO alumnos (matricula,nombre,apaterno,amaterno,domicilio,correo,telefono,estatus) VALUES (?,?,?,?,?,?,?,?)');
        $s->bind_param('ssssssss', $matricula, $nombre, $apaterno, $amaterno, $domicilio, $correo, $telefono, $estatus);
        $s->execute();
        $mensaje = 'Alumno registrado correctamente.';
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
            SAE | Alumno
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar" style="background-color:#eaf2f8">
        <h1>
            Entrada: alumno
        </h1>
        <?php require __DIR__ . '/../menu.php'; ?>
        <p>
            <?= h($mensaje) ?>
        </p>
        <hr>
        <h2>
            Registrar alumno
        </h2>
        <form method="post">
            <label>
                Matrícula
                <input name="matricula" required maxlength="20">
            </label>
            <br>
            <br>
            <label>
                Nombre
                <input name="nombre" required maxlength="60">
            </label>
            <br>
            <br>
            <label>
                Apellido paterno
                <input name="apaterno" required maxlength="60">
            </label>
            <br>
            <br>
            <label>
                Apellido materno
                <input name="amaterno" maxlength="60">
            </label>
            <br>
            <br>
            <label>
                Domicilio
                <input name="domicilio" maxlength="150">
            </label>
            <br>
            <br>
            <label>
                Correo
                <input type="email" name="correo" maxlength="100">
            </label>
            <br>
            <br>
            <label>
                Teléfono
                <input name="telefono" maxlength="20">
            </label>
            <br>
            <br>
            <button>
                Guardar alumno
            </button>
        </form>
    </body>
</html>
