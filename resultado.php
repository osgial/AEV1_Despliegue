<?php

$resultado = $_SESSION('won')

if ($resultado = true) {
    echo "Has ganado la partida"
} else {
    echo "Has perdido la partida"
}

?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mastermind Numérico</title>
</head>
<body>
<form action="index.php">
    <input type="submit">Volver a jugar</input>
</form>
</body>
</html>