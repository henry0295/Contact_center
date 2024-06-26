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
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envios_correos_fijos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    namecampaña VARCHAR(255) NOT NULL,
    cliente VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL, 
    mensaje TEXT NOT NULL,
    fecha_envio DATE NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    resultado_envio TEXT,
    sending_id VARCHAR(250)
)";

// Ejecutar la consulta SQL
if ($conn->query($tableCreateSQL) === TRUE) {
    echo "Tabla 'envios_correos' creada o ya existente.";
} else {
    echo "Error al crear la tabla: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaña = $_POST["campaña"];
    $mensaje = $_POST["mensaje"];

    if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {
        $file = $_FILES["csv"]["tmp_name"];
        $csv = fopen($file, 'r');
        if ($csv) {
            while (($row = fgetcsv($csv)) !== FALSE) {
                $to = mysqli_real_escape_string($conn, $row[0]);
                $paciente = mysqli_real_escape_string($conn, $row[1]);
                $fechaEnvio = date('Y-m-d');

                // Insertar datos en la tabla
                $insertSQL = "INSERT INTO envios_correos_fijos (namecampaña, cliente, correo, mensaje, fecha_envio, usuario) 
                              VALUES ('$campaña', '$paciente', '$to', '$mensaje', '$fechaEnvio', '$nombre_usuario')";

                if ($conn->query($insertSQL) === TRUE) {
                    echo "Registro insertado correctamente.";
                } else {
                    echo "Error al insertar el registro: " . $conn->error;
                }

                // Enviar correos
                $curl = curl_init();

                $data = array(
                    "to" => ["$to"],
                    "fromName" => "Informacion de Cita - FUNDACION IDEAL",
                    "fromEmail" => "confirmacioncitas@fundacionideal.org.co",
                    "body" => '<html><head><title>Informacion De Cita - FUNDACION IDEAL</title>
                    <style>
                        .boton {
                          display: inline-block;
                          padding: 10px 20px;
                          background-color: #0074e4;
                          color: #fff;
                          text-decoration: none;
                          border-radius: 5px;
                        }
                        .boton:hover {
                          background-color: #0058aa;
                        }
                      </style>
                    </head>
                    <body>
                    <img src="https://backend.360nrs.com/storage/multimedia/20512/imagenes/whatsapp-image-2023-11-10-at-100555-am.63d9824234.jpeg" alt="Descripción de la imagen">
                    <p>' . $mensaje . '</p>
                    </body>
                    </html>',
                    "replyTo" => "noreplyconfirmacioncitas@citas.fundacionideal.org.co",
                    "subject" => "Informacion de Cita - FUNDACION IDEAL"
                );

                $payload = json_encode($data);

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://dashboard.360nrs.com/api/rest/mailing',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Basic Vk9aSVBDT0xPTUJJQTpaVGdzMzMhJw=='
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                // Log the API response to a file
                $logFile = 'logs_mail.txt';
                file_put_contents($logFile, $response . PHP_EOL, FILE_APPEND);

                $apiResult = json_decode($response, true);

                if ($apiResult && isset($apiResult['sendingId']) && isset($apiResult['result'])) {
                    $sendingId = $apiResult['sendingId'];
                    foreach ($apiResult['result'] as $result) {
                        $to = mysqli_real_escape_string($conn, $result['to']);
                        $accepted = $result['accepted'];

                        if ($accepted) {
                            $resultado_envio = 0; // 0 represents "Accepted for delivery"
                        } else {
                            $resultado_envio = mysqli_real_escape_string($conn, $result['error']['code']);
                        }

                        // Update the row in the table with the send result and sending ID
                        $updateSQL = "UPDATE envios_correos_fijos SET resultado_envio = '$resultado_envio', sending_id = '$sendingId' WHERE correo = '$to'";

                        if ($conn->query($updateSQL) === TRUE) {
                            // echo "Send result updated successfully.";
                        } else {
                            // echo "Error updating send result: " . $conn->error;
                        }
                    }
                } else {
                    // echo "Error obtaining send result from API.";
                }
            }
            fclose($csv);
        }
    }
}

header('Location: Enviar-correos-simples.php');
exit();
?>
