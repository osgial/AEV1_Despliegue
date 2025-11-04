<?php
session_start();

$resultado = $_SESSION['won'] ?? false; // Si no existe, toma false por defecto

if ($resultado === true) {
    echo "Has ganado la partida";
} else {
    echo "Has perdido la partida";
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>AEV1 OscarG</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>
<form action="index.php" method="get">
    <input type="submit" value="Volver a jugar">
</form>
</body>
</html>
