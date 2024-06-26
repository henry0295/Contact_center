<?php
// Conexión a la base de datos (asegúrate de configurar tu conexión correctamente)
$servername = "vozipcolombia.net.co";
$username = "vozipco1_sms";
$password = "fMW_p~aUnewk";
$dbname = "vozipco1_atlas_sms";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Preparar la consulta SQL para seleccionar los datos
$sql = "SELECT mensaje, celular FROM respuestasms";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Mensaje</th><th>Celular</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["mensaje"] . "</td><td>" . $row["celular"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
