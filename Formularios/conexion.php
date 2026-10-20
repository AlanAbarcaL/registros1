<?php
// En XAMPP normalmente root no tiene contraseña. Cámbiala si configuraste una.
$conexion = new mysqli('localhost', 'root', '', 'sae_escolar');
if ($conexion->connect_error) {
    exit('No se pudo conectar a MySQL. Revisa que MySQL esté iniciado y que hayas importado sae.sql.');
}
$conexion->set_charset('utf8mb4');
function h($dato): string {
    return htmlspecialchars((string)($dato ?? ''), ENT_QUOTES, 'UTF-8');
}
function consultar(string $sql): array {
    global $conexion;
    return $conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
}
