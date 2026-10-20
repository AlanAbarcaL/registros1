<?php require __DIR__ . '/conexion.php'; ?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sistema Escolar</title><link rel="stylesheet" href="estilos.css"></head>
<body class="cesar sistema-moderno"><header class="encabezado-sistema"><h1>Sistema Escolar</h1></header>
<?php require __DIR__.'/menu.php'; ?>
<main class="contenido-sistema"><h2 class="titulo-bienvenida">Sistema Escolar</h2><p class="subtitulo-bienvenida">Selecciona una opción del menú para comenzar.</p>
<div class="tarjetas-catalogos">
<?php foreach (['Alumnos'=>'crud_alumnos.php','Grupos'=>'crud_grupos.php','Materias'=>'crud_materias.php','Profesores'=>'crud_profesores.php'] as $nombre=>$archivo): ?>
<section class="tarjeta-catalogo"><h3><?= h($nombre) ?></h3><a class="boton" href="Entradas/<?= h($archivo) ?>">Ver catálogo</a></section>
<?php endforeach; ?>
</div><p class="pie-sistema">Sistema Escolar · TecNM GAM II</p></main></body></html>
