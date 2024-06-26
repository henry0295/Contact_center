<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "contact_center";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// SQL para crear la tabla si no existe
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envios_fijos (
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
if ($conn->query($tableCreateSQL) !== TRUE) {
    die("Error al crear la tabla: " . $conn->error);
}

// Ruta al archivo de registro
$logFilePath = "api_logs_sms.txt";

// Función para escribir en el archivo de registro
function writeToLog($message)
{
    global $logFilePath;
    $currentDateTime = date("Y-m-d H:i:s");
    $logMessage = "[$currentDateTime] $message" . PHP_EOL;
    file_put_contents($logFilePath, $logMessage, FILE_APPEND);
}

// Verificar si se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaña = mysqli_real_escape_string($conn, $_POST["campaña"]);

    // Verificar si se cargó el archivo CSV sin errores
    if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {
        $csvFile = $_FILES["csv"]["tmp_name"];
        $csv = array_map("str_getcsv", file($csvFile));

        foreach ($csv as $row) {
            // Verificar que la fila contenga al menos 6 columnas
            if (count($row) >= 6) {
                $numeroCelular = mysqli_real_escape_string($conn, $row[0]);
                $paciente = mysqli_real_escape_string($conn, $row[1]);
                $especialidad = mysqli_real_escape_string($conn, $row[2]);
                $diacita = mysqli_real_escape_string($conn, $row[3]);
                $horacita = mysqli_real_escape_string($conn, $row[4]);
                $sede = mysqli_real_escape_string($conn, $row[5]);
                $fechaEnvio = date('Y-m-d'); // Obtener la fecha actual

                // Mensaje a enviar
                $mensaje = "Fundación IDEAL confirma su cita de $especialidad el día $diacita, a las $horacita, en la $sede. Desea Confirmar (Envía Si). Cancelar (Envía No). Evalúa nuestro servicio: https://forms.office.com/r/1tnHSxtSUr";

                // Preparar datos para la API
                $data = array(
                    "token" => "z4q1o00jm6qixjxi2uskc",
                    "email" => "soporte@vozipcolombia.net.co",
                    "type_send" => "2via",
                    "data" => array(
                        array(
                            "cellphone" => $numeroCelular,
                            "message" => $mensaje
                        )
                    )
                );

                $json_data = json_encode($data);

                // Llamada a la API
                $url = 'https://contacto-masivo.com/sms/back_sms/public/api/send/sms/json'; // Reemplaza esto con la URL de tu API
                $headers = array('Content-Type: application/json');

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                curl_close($ch);

                // Procesar la respuesta de la API
                $apiResponse = json_decode($response);

                if ($apiResponse && isset($apiResponse->status)) {
                    $resultadoEnvio = $apiResponse->status;
                } else {
                    $resultadoEnvio = "Error en la respuesta de la API";
                }

                // Guardar el resultado en el archivo de registro
                writeToLog("Respuesta API para el número $numeroCelular: $response");

                // Insertar los datos en la base de datos
                $sqlInsert = "INSERT INTO envios_fijos (campana, paciente, numero_telefono, mensaje, fecha_envio, usuario, resultado_envio)
                              VALUES ('$campaña', '$paciente', '$numeroCelular', '$mensaje', '$fechaEnvio', '$nombre_usuario', '$resultadoEnvio')";

                if ($conn->query($sqlInsert) !== TRUE) {
                    writeToLog("Error al almacenar el registro en la tabla 'envios_fijos': " . $conn->error);
                }
            } else {
                writeToLog("Registro CSV incompleto: " . implode(', ', $row));
            }
        }
    } else {
        writeToLog("Error en la carga del archivo CSV: " . $_FILES["csv"]["error"]);
    }
}

// Redirigir a otra página después del procesamiento
header('Location: Envio-mensaje.php');
exit();
