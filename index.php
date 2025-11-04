<?php
session_start();

/*
  Mastermind numérico (versión sencilla, todo en un solo archivo)
  - Genera un número secreto de 3 cifras distintas.
  - El jugador tiene 10 intentos.
*/

$NUM_DIGITS = 3;
$MAX_ATTEMPTS = 10;

// Inicializar o reiniciar el juego
if (!isset($_SESSION['secret']) || isset($_GET['reset'])) {
    $digits = range(0, 9);
    shuffle($digits);
    $_SESSION['secret'] = implode('', array_slice($digits, 0, $NUM_DIGITS));
    $_SESSION['attempts'] = [];
    $_SESSION['finished'] = false;
    $_SESSION['won'] = false;
}

$secret = $_SESSION['secret'];
$attempts = $_SESSION['attempts'];
$finished = $_SESSION['finished'];
$won = $_SESSION['won'];
$message = "";

// Procesar intento si el juego no ha terminado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$finished) {
    $guess = preg_replace('/\D/', '', $_POST['guess'] ?? '');
    if (strlen($guess) != $NUM_DIGITS) {
        $message = "Debes escribir exactamente $NUM_DIGITS cifras.";
    } elseif (strlen(count_chars($guess, 3)) != $NUM_DIGITS) {
        $message = "Las cifras no deben repetirse.";
    } else {
        $exact = 0;
        $partial = 0;
        for ($i = 0; $i < $NUM_DIGITS; $i++) {
            if ($guess[$i] === $secret[$i]) {
                $exact++;
            } elseif (strpos($secret, $guess[$i]) !== false) {
                $partial++;
            }
        }
        $_SESSION['attempts'][] = [
            'guess' => $guess,
            'exact' => $exact,
            'partial' => $partial
        ];

        // Verificar si ganó o perdió
        if ($exact == $NUM_DIGITS) {
            $_SESSION['finished'] = true;
            $_SESSION['won'] = true;
            header("Location: resultado.php");
        } elseif (count($_SESSION['attempts']) >= $MAX_ATTEMPTS) {
            $_SESSION['finished'] = true;
            header("Location: resultado.php");
        }

        header("Location: index.php");
        exit;
    }
}

$remaining = $MAX_ATTEMPTS - count($_SESSION['attempts']);
?>
