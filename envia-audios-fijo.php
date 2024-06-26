<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit(); // Debes detener la ejecución después de redirigir.
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
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envio_audios2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    namecapañana VARCHAR(255) NOT NULL,
    cliente VARCHAR(255) NOT NULL,
    numero_telefono VARCHAR(20) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATE NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    resultado_envio TEXT,
    sending_id VARCHAR(255)
)";

// Ejecutar la consulta SQL
if ($conn->query($tableCreateSQL) === TRUE) {
    echo "Tabla 'envio_audios2' creada o ya existente.";
} else {
    echo "Error al crear la tabla: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaña = $_POST["campaña"];
    $mensaje = $_POST["mensaje"];

    if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {
        $file = $_FILES["csv"]["tmp_name"];
        $csv = fopen($file, 'r');
        if ($csv !== FALSE) { // Comprueba la apertura exitosa del archivo CSV
            while (($row = fgetcsv($csv)) !== FALSE) {
                $numeroCelular = "57" . mysqli_real_escape_string($conn, $row[0]);
                $cliente = mysqli_real_escape_string($conn, $row[1]);
                $fechaEnvio = date('Y-m-d');
                $message = $mensaje;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://dashboard.360nrs.com/api/rest/voice',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode(array(
                        "to" => [$numeroCelular],
                        "message" => $message,
                        "gender" => "F",
                        "language" => "es_ES",
                        "campaignName" => $campaña,
                        "retries" => 1
                    )),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Basic QXRsYXNTZWd1cmlkYWQ6S05oczMyIyU='
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                // Registro de la respuesta de la API en un archivo de logs
                $logData = "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
                $logData .= "Respuesta de la API: " . $response . "\n\n";
                $logFile = "api_logs_voice.txt";
                $handle = fopen($logFile, 'a');
                fwrite($handle, $logData);
                fclose($handle);

                // Procesa la respuesta de la API
                if ($response) {
                    $apiResponse = json_decode($response);
                    if ($apiResponse && isset($apiResponse->error)) {
                        $error = $apiResponse->error;
                        if (isset($error->code) && isset($error->description)) {
                            $resultadoEnvio = $error->description;
                        } else {
                            $resultadoEnvio = "Error desconocido";
                        }
                    } elseif ($apiResponse && isset($apiResponse->result) && is_array($apiResponse->result)) {
                        $result = $apiResponse->result[0];
                        if (isset($result->accepted) && $result->accepted === true) {
                            $resultadoEnvio = "Audio Enviado";
                        } else {
                            // Manejar los códigos de respuesta específicos
                            if (isset($result->errorCode)) {
                                if ($result->errorCode == 402) {
                                    $resultadoEnvio = "Not enough credits";
                                } elseif ($result->errorCode == 400) {
                                    $resultadoEnvio = "No valid recipients";
                                } else {
                                    $resultadoEnvio = "Error desconocido";
                                }
                            } else {
                                $resultadoEnvio = "Error desconocido";
                            }
                        }

                        $sendingId = isset($apiResponse->sendingId) ? $apiResponse->sendingId : "N/A";
                    } else {
                        $resultadoEnvio = "Error en la respuesta de la API";
                        $sendingId = "N/A";
                    }
                } else {
                    $resultadoEnvio = "Error al llamar a la API";
                    $sendingId = "N/A";
                }

                // Guardar en la base de datos
                $sqlInsert = "INSERT INTO reportes_voice (namecapañana, cliente, numero_telefono, mensaje, fecha_envio, usuario, resultado_envio, sending_id)
    VALUES ('$campaña', '$cliente', '$numeroCelular', '$message', '$fechaEnvio', '$nombre_usuario', '$resultadoEnvio', '$sendingId')";

                if ($conn->query($sqlInsert) === TRUE) {
                    echo "Envío exitoso y registro almacenado en la tabla 'reportes_voice'.";
                } else {
                    echo "Error al almacenar el registro en la tabla 'reportes_voice': " . $conn->error;
                }
            }
            fclose($csv);
        } else {
            echo "Error al abrir el archivo CSV.";
        }
    }
}

header('Location: Envios-Audios-fijos.php');
exit();
