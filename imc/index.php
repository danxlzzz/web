<?php
$conexion = new mysqli("localhost", "root", "", "bd_imc");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$resultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $sexo = $_POST["sexo"];
    $peso = floatval($_POST["peso"]);
    $altura = floatval($_POST["altura"]);

    $imc = $peso / ($altura * $altura);
    $alturaCm = $altura * 100;

    if ($sexo === "Masculino") {
        $peso_ideal = 50 + 2.3 * (($alturaCm / 2.54) - 60);
        $calorias = 66 + (13.7 * $peso) + (5 * $alturaCm) - (6.8 * 25);
    } else {
        $peso_ideal = 45.5 + 2.3 * (($alturaCm / 2.54) - 60);
        $calorias = 655 + (9.6 * $peso) + (1.8 * $alturaCm) - (4.7 * 25);
    }

    if ($imc < 18.5) $clasificacion = "Bajo peso";
    elseif ($imc < 25) $clasificacion = "Normal";
    elseif ($imc < 30) $clasificacion = "Sobrepeso";
    else $clasificacion = "Obesidad";

    $stmt = $conexion->prepare("INSERT INTO personas (nombre, sexo, peso, altura, imc, peso_ideal, calorias_diarias, clasificacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddddis", $nombre, $sexo, $peso, $altura, $imc, $peso_ideal, $calorias, $clasificacion);
    $stmt->execute();
    $stmt->close();

    $resultado = compact("nombre", "sexo", "peso", "altura", "imc", "peso_ideal", "calorias", "clasificacion");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de IMC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4 w-100" style="max-width: 480px;">
        <h3 class="text-center text-primary mb-4">Calculadora de IMC</h3>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Sexo:</label>
                <select name="sexo" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Peso (kg):</label>
                <input type="number" step="0.01" name="peso" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Altura (m):</label>
                <input type="number" step="0.01" name="altura" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Calcular</button>
        </form>

        <?php if ($resultado): ?>
            <hr>
            <h5 class="text-center mt-3">Resultados</h5>
            <ul class="list-group">
                <li class="list-group-item"><strong>Nombre:</strong> <?= htmlspecialchars($resultado["nombre"]) ?></li>
                <li class="list-group-item"><strong>Sexo:</strong> <?= htmlspecialchars($resultado["sexo"]) ?></li>
                <li class="list-group-item"><strong>IMC:</strong> <?= number_format($resultado["imc"], 2) ?></li>
                <li class="list-group-item"><strong>Clasificación:</strong> <?= htmlspecialchars($resultado["clasificacion"]) ?></li>
                <li class="list-group-item"><strong>Peso ideal:</strong> <?= number_format($resultado["peso_ideal"], 2) ?> kg</li>
                <li class="list-group-item"><strong>Calorías diarias:</strong> <?= round($resultado["calorias"]) ?> kcal</li>
            </ul>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>
