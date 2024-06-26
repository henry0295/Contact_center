<?php
// Configuración de la base de datos remota
$server_remoto = 'vozipcolombia.net.co';
$username_remoto = 'vozipco1_vozipsms';
$password_remoto = 'rNU*cM6Ddde@';
$database_remota = 'vozipco1_atlas_sms';


// Configuración de la base de datos local
$server_local = 'localhost';
$username_local = 'root';
$password_local = '';
$database_local = 'contact_center';

// Conectar a la base de datos remota
$conexion_remota = new mysqli($server_remoto, $username_remoto, $password_remoto, $database_remota);

// Verificar la conexión a la base de datos remota
if ($conexion_remota->connect_error) {
    die("Error de conexión a la base de datos remota: " . $conexion_remota->connect_error);
}

// Consulta SQL en la base de datos remota
$sql_remota = "SELECT * FROM respuestasms";

// Ejecutar la consulta en la base de datos remota
$resultado_remota = $conexion_remota->query($sql_remota);

// Conectar a la base de datos local
$conexion_local = new mysqli($server_local, $username_local, $password_local, $database_local);

// Verificar la conexión a la base de datos local
if ($conexion_local->connect_error) {
    die("Error de conexión a la base de datos local: " . $conexion_local->connect_error);
}

// Consulta SQL en la base de datos local
$sql_local = "SELECT * FROM envios_sms";

// Ejecutar la consulta en la base de datos local
$resultado_local = $conexion_local->query($sql_local);


// Inicializar una variable para almacenar los resultados
$resultado_coincidencia = '';

// Crear una tabla temporal para almacenar los resultados
$sql_create_temp_table = "CREATE TEMPORARY TABLE temp_coincidencias (
    celular_remoto VARCHAR(20),
    mensaje_respuesta TEXT,
    resultado VARCHAR(2)
)";
if ($conexion_local->query($sql_create_temp_table) !== true) {
    die("Error al crear la tabla temporal: " . $conexion_local->error);
}


while ($fila_local = $resultado_local->fetch_assoc()) {
    $mensaje_respuesta = ''; // Inicializar el mensaje de respuesta
    $resultado = 'No Respondió'; // Valor por defecto si no se encuentra coincidencia

    $resultado_remota->data_seek(0); // Reiniciar el puntero del resultado remoto
    $encontrado = false; // Bandera para indicar si se encontró una coincidencia

    while ($fila_remota = $resultado_remota->fetch_assoc()) {
        // Eliminar el prefijo "57" de la columna remota para comparar
        $celular_remoto_sin_prefijo = substr($fila_remota["celular"], 2);

        if ($celular_remoto_sin_prefijo == $fila_local["numero_telefono"]) {
            // Convertir la fecha de respuesta de respuestasms al formato de envios_fijos
            $fecha_respuesta_formato_envio = date('Y-m-d', strtotime($fila_remota["fecha"]));

            // Comparar la fecha de envío en la base de datos local con la fecha de respuesta en el formato correcto
            if ($fila_local["fecha_envio"] == $fecha_respuesta_formato_envio) {
                // Asignar el mensaje de respuesta
                $mensaje_respuesta = $fila_remota["mensaje"];
                $encontrado = true;
                $resultado = 'Sí'; // Actualizar el resultado si se encuentra coincidencia
            }
        }
    }

    // Actualizar el registro en la tabla envios_fijos con el mensaje de respuesta y resultado
    $sql_update_envios_fijos = "UPDATE envios_sms SET resultado = '$resultado', respuesta_cliente = '$mensaje_respuesta' WHERE numero_telefono = '" . $fila_local["numero_telefono"] . "' AND fecha_envio = '" . $fila_local["fecha_envio"] . "'";
    if ($conexion_local->query($sql_update_envios_fijos) !== true) {
        // Manejar errores si es necesario
    }
}



// Cerrar las conexiones a ambas bases de datos
$conexion_remota->close();
$conexion_local->close();

header('location: repo-SMS.php');
exit();
