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
$conn = new mysqli($servername, $username, $password, $database); // Agrega el nombre de la base de datos al conectar.

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// SQL para crear la tabla si no existe
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envios_audios (
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
    echo "Tabla 'envios_audios' creada o ya existente.";
} else {
    echo "Error al crear la tabla: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaña = $_POST["campaña"];
    if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {
        $file = $_FILES["csv"]["tmp_name"];
        $csv = fopen($file, 'r');
        if ($csv) {
            while (($row = fgetcsv($csv)) !== FALSE) { // Cambia $line a $row
                $numeroCelular = "57" . mysqli_real_escape_string($conn, $row[0]);
                $paciente = mysqli_real_escape_string($conn, $row[1]);
                $especialidad = mysqli_real_escape_string($conn, $row[2]);
                $diacita = mysqli_real_escape_string($conn, $row[3]);
                $horacita = mysqli_real_escape_string($conn, $row[4]);
                $sede = mysqli_real_escape_string($conn, $row[5]);
                $fechaEnvio = date('Y-m-d');
                $to = $numeroCelular; // Corrige la variable $to
                $message = "Fundación IDEAL confirma su cita de $especialidad el día $diacita, a las $horacita, en la $sede, para modificar su cita comunicarse al 4863732"; // Corrige la variable $message

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://dashboard.360nrs.com/api/rest/voice',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode(array( // Utiliza json_encode para crear datos JSON válidos.
                        "to" => [$to],
                        "message" => $message,
                        "gender" => "F",
                        "language" => "es_ES"
                    )),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Basic Vk9aSVBDT0xPTUJJQTpQRWl0NjMhIQ=='
                    ),
                ));

                $response = curl_exec($curl); // Cambia $ch a $curl
                curl_close($curl);

                // Procesa la respuesta de la API según la estructura real de la respuesta
                // Asume que la respuesta es un objeto JSON
                $apiResponse = json_decode($response);

                if ($apiResponse && isset($apiResponse->result) && is_array($apiResponse->result)) {
                    $result = $apiResponse->result[0]; // Tomamos el primer resultado (asumimos que solo hay uno).

                    if (isset($result->accepted) && $result->accepted === true) {
                        $resultadoEnvio = "Audio Enviado";
                    } else {
                        $resultadoEnvio = "Audio No Enviado";
                    }
                } else {
                    $resultadoEnvio = "Error en la respuesta de la API";
                }


                $sqlInsert = "INSERT INTO envios_audios (campana, paciente, numero_telefono, mensaje, fecha_envio, usuario, resultado_envio)
                    VALUES ('$campaña', '$paciente', '$numeroCelular', '$message', '$fechaEnvio', '$nombre_usuario', '$resultadoEnvio')";

                if ($conn->query($sqlInsert) === TRUE) {
                    // echo "Envío exitoso y registro almacenado en la tabla 'envios_audios'.";
                } else {
                    // echo "Error al almacenar el registro en la tabla 'envios_audios': " . $conn->error;
                }
            }
            fclose($csv); // Cierra el archivo CSV.
        } else {
            // Maneja el error de apertura del archivo CSV.
        }
    }
}

header('Location: Envios-Audios.php'); // Debes usar 'Location' en lugar de 'location'.
exit();
