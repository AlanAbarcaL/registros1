<?php
require __DIR__ . '/../conexion.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_sae'])) $_SESSION['csrf_sae'] = bin2hex(random_bytes(32));
$tabla = 'alumnos'; $clave = 'id_alumno';
$campos = ['matricula', 'nombre', 'apaterno', 'amaterno', 'domicilio', 'correo', 'telefono'];
$obligatorios = ['matricula', 'nombre', 'apaterno'];
$mensaje = ''; $error = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 try {
  if (!hash_equals($_SESSION['csrf_sae'], (string)($_POST['csrf'] ?? ''))) throw new RuntimeException('Sesión inválida. Recarga la página.');
  $accion = $_POST['accion'] ?? '';
  $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
  if ($accion === 'eliminar') {
   if (!$id || $id < 1) throw new RuntimeException('Registro inválido.');
   $stmt=$conexion->prepare("DELETE FROM `$tabla` WHERE `$clave`=?");
   $stmt->bind_param('i',$id); $stmt->execute();
   $mensaje='Registro eliminado correctamente.';
  } elseif ($accion === 'guardar') {
   $datos=[];
   foreach ($campos as $campo) $datos[$campo]=trim((string)($_POST[$campo] ?? ''));
   foreach ($obligatorios as $campo) if ($datos[$campo] === '') throw new RuntimeException('Completa los campos obligatorios.');
   $valores=array_values($datos);
   $tipos=str_repeat('s',count($valores));
   if ($id && $id > 0) {
    $asignaciones=implode(',',array_map(fn($c)=>"`$c`=?",$campos));
    $stmt=$conexion->prepare("UPDATE `$tabla` SET $asignaciones WHERE `$clave`=?");
    $tipos.='i'; $valores[]=$id;
    $stmt->bind_param($tipos,...$valores); $stmt->execute();
    $mensaje='Registro actualizado correctamente.';
   } else {
    $nombres=implode(',',array_map(fn($c)=>"`$c`",$campos));
    $marcas=implode(',',array_fill(0,count($campos),'?'));
    $stmt=$conexion->prepare("INSERT INTO `$tabla` ($nombres) VALUES ($marcas)");
    $stmt->bind_param($tipos,...$valores); $stmt->execute();
    $mensaje='Registro agregado correctamente.';
   }
  } else throw new RuntimeException('Acción no válida.');
 } catch (Throwable $e) {
  $error=true;
  $mensaje=($e instanceof mysqli_sql_exception) ? 'No se pudo realizar la operación. Revisa si hay datos duplicados o registros relacionados.' : $e->getMessage();
 }
}
$editar=null;
$idEditar=filter_var($_GET['editar'] ?? null,FILTER_VALIDATE_INT);
if ($idEditar && $idEditar > 0) {
 $stmt=$conexion->prepare("SELECT * FROM `$tabla` WHERE `$clave`=?");
 $stmt->bind_param('i',$idEditar); $stmt->execute();
 $editar=$stmt->get_result()->fetch_assoc();
}
$registros=$conexion->query("SELECT * FROM `$tabla` ORDER BY `$clave` DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            SAE | Alumnos
        </title>
        <link rel="stylesheet" href="../estilos.css">
    </head>
    <body class="cesar">
        <main>
            <h1>
                Catálogo de alumnos
            </h1>
            <?php require __DIR__.'/../menu.php'; ?>
            <div class="barra-catalogo">
                <a class="boton" href="crud_alumnos.php?nuevo=1">Registrar alumno</a>
                <a class="boton gris" href="../index.php">Volver al inicio</a>
            </div>
            <?php if ($mensaje): ?>
            <p class="aviso <?= $error ? 'error' : '' ?>">
                <?= h($mensaje) ?>
            </p>
            <?php endif; ?>
            <?php if ($editar || isset($_GET['nuevo'])): ?>
            <section class="alan">
                <h2>
                    <?= $editar ? 'Editar alumno' : 'Registrar alumno' ?>
                </h2>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_sae']) ?>">
                    <input type="hidden" name="accion" value="guardar">
                    <input type="hidden" name="id" value="<?= h($editar['id_alumno'] ?? '') ?>">
                    <label>
                        Matrícula
                        <input type="text" name="matricula" value="<?= h($editar['matricula'] ?? '') ?>" required maxlength="60">
                    </label>
                    <label>
                        Nombre
                        <input type="text" name="nombre" value="<?= h($editar['nombre'] ?? '') ?>" required maxlength="60">
                    </label>
                    <label>
                        Apellido paterno
                        <input type="text" name="apaterno" value="<?= h($editar['apaterno'] ?? '') ?>" required maxlength="60">
                    </label>
                    <label>
                        Apellido materno
                        <input type="text" name="amaterno" value="<?= h($editar['amaterno'] ?? '') ?>" maxlength="60">
                    </label>
                    <label>
                        Domicilio
                        <input type="text" name="domicilio" value="<?= h($editar['domicilio'] ?? '') ?>" maxlength="150">
                    </label>
                    <label>
                        Correo
                        <input type="email" name="correo" value="<?= h($editar['correo'] ?? '') ?>" maxlength="100">
                    </label>
                    <label>
                        Teléfono
                        <input type="text" name="telefono" value="<?= h($editar['telefono'] ?? '') ?>" maxlength="60">
                    </label>
                    <button type="submit">
                        <?= $editar ? 'Guardar cambios' : 'Agregar' ?>
                    </button>
                    <?php if ($editar): ?>
                    <a class="boton gris" href="crud_alumnos.php">
                        Cancelar
                    </a>
                    <?php endif; ?>
                </form>
            </section>
            <?php endif; ?>
            <section class="alan">
                <h2>
                    Registros de alumnos
                </h2>
                <div class="tabla-caja">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    Matrícula
                                </th>
                                <th>
                                    Nombre
                                </th>
                                <th>
                                    Apellido paterno
                                </th>
                                <th>
                                    Apellido materno
                                </th>
                                <th>
                                    Domicilio
                                </th>
                                <th>
                                    Correo
                                </th>
                                <th>
                                    Teléfono
                                </th>
                                <th>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                            <tr>
                                <td>
                                    <?= h($r[$clave]) ?>
                                </td>
                                <td>
                                    <?= h($r['matricula']) ?>
                                </td>
                                <td>
                                    <?= h($r['nombre']) ?>
                                </td>
                                <td>
                                    <?= h($r['apaterno']) ?>
                                </td>
                                <td>
                                    <?= h($r['amaterno']) ?>
                                </td>
                                <td>
                                    <?= h($r['domicilio']) ?>
                                </td>
                                <td>
                                    <?= h($r['correo']) ?>
                                </td>
                                <td>
                                    <?= h($r['telefono']) ?>
                                </td>
                                <td>
                                    <div class="acciones">
                                        <a class="boton" href="crud_alumnos.php?editar=<?= urlencode((string)$r[$clave]) ?>">
                                            Editar
                                        </a>
                                        <form class="enlinea" method="post" onsubmit="return confirm('¿Eliminar este registro definitivamente?');">
                                            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_sae']) ?>">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= h($r[$clave]) ?>">
                                            <button type="submit" class="rojo">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </body>
</html>
