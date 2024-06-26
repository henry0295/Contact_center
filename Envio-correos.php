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
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS envios_correos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campana VARCHAR(255) NOT NULL,
    paciente VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL, 
    mensaje TEXT NOT NULL,
    fecha_envio DATE NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    resultado_envio TEXT
)";

// Ejecutar la consulta SQL
if ($conn->query($tableCreateSQL) === TRUE) {
  echo "Tabla 'envios_correos' creada o ya existente.";
} else {
  echo "Error al crear la tabla: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $campaña = $_POST["campaña"];
  if (isset($_FILES["csv"]) && $_FILES["csv"]["error"] == 0) {
    $file = $_FILES["csv"]["tmp_name"];
    $csv = fopen($file, 'r');
    if ($csv) {
      while (($row = fgetcsv($csv)) !== FALSE) {
        $to = mysqli_real_escape_string($conn, $row[0]);
        $paciente = mysqli_real_escape_string($conn, $row[1]); // Agregar la columna 'paciente' si es necesario.
        $especialidad = mysqli_real_escape_string($conn, $row[2]);
        $diacita = mysqli_real_escape_string($conn, $row[3]);
        $horacita = mysqli_real_escape_string($conn, $row[4]);
        $sede = mysqli_real_escape_string($conn, $row[5]);
        $fechaEnvio = date('Y-m-d');

        // Insertar datos en la tabla
        $insertSQL = "INSERT INTO envios_correos (campana, paciente, correo, mensaje, fecha_envio, usuario) VALUES ('$campaña', '$paciente', '$to', 'Fundacion IDEAL confirma su cita de ' . $especialidad . ' el dia ' . $diacita . ', a las ' . $horacita . ', en la ' . $sede . '.', '$fechaEnvio', '$nombre_usuario')";

        if ($conn->query($insertSQL) === TRUE) {
          echo "Registro insertado correctamente.";
        } else {
          echo "Error al insertar el registro: " . $conn->error;
        }

        // Tu código para enviar correos aquí

        $curl = curl_init();

        $data = array(
          "to" => ["$to"],
          "fromName" => "Informacion de Cita - FUNDACION IDEAL",
          "fromEmail" => "confirmacioncitas@fundacionideal.org.co",
          "body" => '<html><head><title>Informacion De Cita - FUNDACION IDEAL</title>
          <style>
              /* Estilos del botón */
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
          <img src="https://backend.360nrs.com/storage/multimedia/20482/imagenes/screenshot-2.782c4d1f04.png" alt="Descripción de la imagen">
          <center>
              <p>Fundacion IDEAL confirma su cita de "' . $especialidad . '" el dia "' . $diacita . '", a las "' . $horacita . '", en la "' . $sede . '".</p>
              
              <p>Desea Confirmar (Envia Si). Cancelar (Envia No)</p>
          </center>
          <center>
          <p style="font-weight: bold;">TEN PRESENTE QUE DURANTE TU ATENCIÓN EN FUNDACIÓN IDEAL ES IMPORTANTE QUE CONOZCAS LO SIGUIENTE</p>
          </center>
          <p style="font-weight: bold;">¿Qué debes tener en cuenta al asistir a la cita?</p> 
          <p>Conoce los documentos a presentar y las medidas de higiene, protección y seguridad para garantizar tu cuidado y bienestar.</p>
          <center>
          <a href=\www.fundacionideal.org.co/informaci%C3%B3n-de-citas>Citas</a>
          </center>
          <br>
          <p style="font-weight: bold;">Asistir a tu cita es un compromiso contigo y el sistema de salud</p>
          <p>Si debes cancelarla es importante que nos informes a tiempo, así le darás la oportunidad a otro usuario de ser atendido</p>
          <center>
          <a href=\forms.office.com/Pages/DesignPageV2.aspx?subpage=design&FormId=UcQz5GaPA0SHmMXm0GVd6nqJyNvLFkFMlIUYZ2GZ8yFUNTRQUlQ0TlMwM0FVNUhXRkVEV09BTUQ5SiQlQCN0PWcu&Token=2a437e9f29934c05b8fa4c228fb13113>Cancelar Citas</a>
          </center>
          <p style="font-weight: bold;">Comprometidos con el sistema de salud damos a conocer DERECHOS Y DEBERES</p>
          <center>
          <a href=\www.fundacionideal.org.co/derechos-y-deberes-ips>Derecehos y Deberes</a>
          </center>
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

        $apiResult = json_decode($response, true);

        if ($apiResult && isset($apiResult['result'])) {
          foreach ($apiResult['result'] as $result) {
            $to = mysqli_real_escape_string($conn, $result['to']);
            $accepted = $result['accepted'];

            if ($accepted) {
              $resultado_envio = "Correo enviado"; // 0 representa "Accepted for delivery"
            } else {
              $resultado_envio = mysqli_real_escape_string($conn, $result['error']['code']);
            }

            // Actualizar la fila en la tabla con el resultado del envío
            $updateSQL = "UPDATE envios_correos SET resultado_envio = '$resultado_envio' WHERE correo = '$to'";

            if ($conn->query($updateSQL) === TRUE) {
              echo "Resultado del envío actualizado correctamente.";
            } else {
              echo "Error al actualizar el resultado del envío: " . $conn->error;
            }
          }
        } else {
          echo "Error al obtener el resultado del envío desde la API.";
        }
      }
    }
  }
}

header('Location: Envio-correo-masivo.php'); // Debes usar 'Location' en lugar de 'location'.
exit();
