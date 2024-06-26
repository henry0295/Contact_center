<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Reporte de Eventos</title>
</head>
<body>
    <h2>Consulta de Reporte de Eventos</h2>
    <form id="reportForm" action="" method="post">
        <label for="sendingId">ID del Envío:</label>
        <input type="text" id="sendingId" name="sendingId" required><br><br>

        <label for="fecha">Fecha y Hora:</label>
        <input type="datetime-local" id="fecha" name="fecha" required><br><br>

        <button type="submit">Consultar Reporte</button>
    </form>

    <div id="resultContainer"></div>

    <?php
    // Manejo de la solicitud POST cuando se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Datos para la autenticación
        $username = 'VOZIPCOLOMBIA';
        $password = 'PEit63!!';

        // ID del envío y fecha del formulario HTML
        $sendingId = $_POST['sendingId'];
        $fecha = $_POST['fecha'];

        // Formatear la fecha para incluirla en la URL de la API
        $formattedDate = date('Y-m-d\TH:i:s', strtotime($fecha));

        // URL de la API
        $url = "https://dashboard.360nrs.com/api/rest/sendings/$sendingId/reports/events?date=" . urlencode($formattedDate);

        // Inicializar cURL para hacer la solicitud con autenticación
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Verificar si la solicitud fue exitosa (código 200)
        if ($httpCode == 200) {
            echo "<script>";
            echo "document.getElementById('resultContainer').innerHTML = 'Reporte obtenido correctamente:<br>' + " . json_encode($response) . ";";
            echo "</script>";
        } else {
            echo "<script>";
            echo "document.getElementById('resultContainer').innerHTML = 'Error al consultar el reporte. Código HTTP: $httpCode';";
            echo "</script>";
        }

        // Cerrar la sesión cURL
        curl_close($curl);
    }
    ?>
</body>
</html>
