<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$nombre_usuario = $_SESSION['nombre'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "contact_center";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Seleccionar la base de datos
$conn->select_db($database);

// SQL para crear la tabla si no existe
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campana VARCHAR(255) NOT NULL,
    paciente VARCHAR(255) NOT NULL,
    numero_telefono VARCHAR(20) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATE NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    resultado_envio TEXT
)";

// Ejecutar la consulta SQL
if ($conn->query($tableCreateSQL) === TRUE) {
    echo "Tabla 'envios' creada o ya existente.";
} else {
    echo "Error al crear la tabla: " . $conn->error;
}

// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaña = $_POST["campaña"];
    $mensaje = $_POST["mensaje"];

    if ($_FILES["csv"]["error"] == 0) {
        $csvFile = $_FILES["csv"]["tmp_name"];
        $csv = array_map("str_getcsv", file($csvFile));

        foreach ($csv as $row) {
            if (count($row) >= 2) {
                $numeroCelular = mysqli_real_escape_string($conn, $row[0]);
                $paciente = mysqli_real_escape_string($conn, $row[1]);
                $fechaEnvio = date('Y-m-d'); // Obtener la fecha actual

                // Realiza la llamada a la API y obtén la respuesta
                $data = array(
                    "token" => "bjt31kuposrxtuqh38spp",
                    "email" => "sms@vozipcolombia.net.co",
                    "type_send" => "2via",
                    "data" => array(
                        array(
                            "cellphone" => $numeroCelular,
                            "message" => $mensaje
                        )
                    )
                );

                $json_data = json_encode($data);

                $url = 'https://contacto-masivo.com/sms/back_sms/public/api/send/sms/json'; // Reemplaza esto con la URL de tu API
                $headers = array('Content-Type: application/json');

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                curl_close($ch);

                // Procesa la respuesta de la API según la estructura real de la respuesta
                // Asume que la respuesta es un objeto JSON
                $apiResponse = json_decode($response);

                if ($apiResponse && isset($apiResponse->status)) {
                    $resultadoEnvio = $apiResponse->status;
                } else {
                    $resultadoEnvio = "Error en la respuesta de la API";
                }

                // Almacena la respuesta de la API en un archivo de registro
                $logFile = 'api_logs_sms.txt';
                $logHandle = fopen($logFile, 'a') or die("No se pudo abrir el archivo de registro");
                $logData = "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
                $logData .= "Respuesta de la API: " . $resultadoEnvio . "\n\n";
                fwrite($logHandle, $logData);
                fclose($logHandle);

                // Inserta los datos en la tabla 'envios' después de validar y limpiar
                $sqlInsert = "INSERT INTO envios (campana, paciente, numero_telefono, mensaje, fecha_envio, usuario, resultado_envio)
                              VALUES ('$campaña', '$paciente', '$numeroCelular', '$mensaje', '$fechaEnvio', '$nombre_usuario', '$resultadoEnvio')";

                if ($conn->query($sqlInsert) === TRUE) {
                    echo "Envío exitoso y registro almacenado en la tabla 'envios'.";
                } else {
                    echo "Error al almacenar el registro en la tabla 'envios': " . $conn->error;
                }
            }
        }
    }
}
// header('location: Envio-mensaje-masivo.php');
exit();

?>
