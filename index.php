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
            header("resultado.php")
        } elseif (count($_SESSION['attempts']) >= $MAX_ATTEMPTS) {
            $_SESSION['finished'] = true;
            header("resultado.php")
        }

        header("Location: index.php");
        exit;
    }
}

$remaining = $MAX_ATTEMPTS - count($_SESSION['attempts']);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mastermind Numérico</title>
</head>
<body>

<div class="container">
  <h2>Mastermind Numérico</h2>
  <p>Adivina el número de <?= $NUM_DIGITS ?> cifras distintas.<br>
     Tienes <?= $MAX_ATTEMPTS ?> intentos.</p>

  <?php if (!$finished): ?>
    <form method="post">
      <input type="text" name="guess" maxlength="<?= $NUM_DIGITS ?>" required autofocus placeholder="Ej: 123">
      <button type="submit">Intentar</button>
    </form>
    <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <p>Intentos restantes: <?= $remaining ?></p>
  <?php else: ?>
    <div class="result">
      <?php if ($won): ?>
        <h3>🎉 ¡Correcto! El número era <?= htmlspecialchars($secret) ?>.</h3>
      <?php else: ?>
        <h3>❌ Sin intentos. El número era <?= htmlspecialchars($secret) ?>.</h3>
      <?php endif; ?>
      <p><a href="?reset=1"><button>Jugar otra vez</button></a></p>
    </div>
  <?php endif; ?>

  <?php if ($attempts): ?>
  <h3>Intentos anteriores</h3>
  <table>
    <tr><th>#</th><th>Intento</th><th>Exactos</th><th>Parciales</th></tr>
    <?php foreach ($attempts as $i => $a): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($a['guess']) ?></td>
        <td><?= $a['exact'] ?></td>
        <td><?= $a['partial'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>