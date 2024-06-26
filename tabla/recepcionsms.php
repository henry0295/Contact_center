<?php
    // Establecer la conexi��n a la base de datos (modifica los valores seg��n tu configuraci��n)
    $servername = "149.202.31.220";
    $username = "vozipco1_sms";
    $password = "!iHF2(b7WJ0[";
    $dbname = "vozipco1_recepcionsms";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexi��n a la base de datos
    if ($conn->connect_error) {
        die("Error de conexi��n a la base de datos: " . $conn->connect_error);
    }

// Verifica si se ha recibido una solicitud GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los par��metros de la URL
    $mensaje = $_POST['mensaje'] ?? '';
    $celular = $_POST['celular'] ?? '';

    // Realiza las operaciones necesarias con los par��metros recibidos
    // Por ejemplo, puedes guardarlos en una base de datos o procesarlos de alguna otra manera.


    // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO respuestasms (mensaje, celular) VALUES ('$mensaje', '$celular')";

    if ($conn->query($sql) === TRUE) {
        echo "Datos insertados en la base de datos correctamente.";
    } else {
        echo "Error al insertar datos en la base de datos: " . $conn->error;
    }

    // Cerrar la conexi��n a la base de datos
    $conn->close();
} else {
    // Si la solicitud no es GET, puedes manejarla seg��n tus necesidades.
    echo "Solicitud no v��lida.";
    
    // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO respuestasms (mensaje, celular) VALUES ('no numero', '31213123')";

    if ($conn->query($sql) === TRUE) {
        echo "Datos insertados en la base de datos correctamente.";
    } else {
        echo "Error al insertar datos en la base de datos: " . $conn->error;
    }

    // Cerrar la conexi��n a la base de datos
    $conn->close();
}
?>
