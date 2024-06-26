<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Conectarse a la base de datos (reemplaza los valores con los de tu base de datos)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_center";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener las opciones del select
$sql = "SELECT sending_id, campana FROM envios_correos_fijos";
$result = $conn->query($sql);

$options = ""; // Variable para almacenar las opciones del select

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Modificar la construcción de las opciones del select
            $options .= "<option value='" . $row['sending_id'] . "'>" . $row['campana'] . "</option>";
        }
    } else {
        $options = "<option value=''>No hay opciones disponibles</option>";
    }
} else {
    // Manejar el error si la consulta falla
    $options = "<option value=''>Error al obtener las opciones: " . $conn->error . "</option>";
}



// Consulta para obtener el total de datos
$sql = "SELECT COUNT(*) AS total FROM reportes_voice where evento = 'delivered' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener el total de datos
    $row = $result->fetch_assoc();
    $total_enviados = $row["total"];
} else {
    $total_enviados = 0;
}

// Consulta para obtener el total de datos
$sql = "SELECT COUNT(*) AS total FROM reportes_voice where evento = 'rejected' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener el total de datos
    $row = $result->fetch_assoc();
    $total_rejected = $row["total"];
} else {
    $total_rejected = 0;
}

// Cerrar conexión
$conn->close();



?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/png" href="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Contact Center Vozip</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/css/adminlte.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="images/atlas2.png" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">


<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="principal.php">
            <img src="images/atlas2.png" width="80%" alt="auto">
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ml-auto mr-0 mr-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $nombre; ?><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#">Configuración</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Salir</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="principal.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <?php if ($tipo_usuario == 1) { ?>
                            <div class="sb-sidenav-menu-heading">Modulos</div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class='far'>&#xf086; </i></div>
                                Campaña SMS
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="Envio-mensaje-masivo.php">Campaña SMS</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts3" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class='far'>&#xf2b6;</i></div>
                                Campaña CORREOS
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts3" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="Enviar-correos-simples.php">Campaña CORREOS</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts4" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class='far'>&#xf086; </i></div>
                                Campaña Audios Masivos
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts4" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <!-- <a class="nav-link" href="Envio-mensaje-simple.php">Envio de Mesaje simple</a> -->
                                    <a class="nav-link" href="envia-audios-fijo.php">Campaña de Audios</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts2" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-bar mr-1"></i></div>
                                Reportes
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts2" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">

                                    <a class="nav-link" href="repo-SMS-fijo.php">Reporte de Campaña Audios</a>

                                </nav>
                            </div>

                            </header>

                        <?php } ?>


                        <div class="sb-sidenav-footer">
                            <div class="small">Logged in as:</div>
                            Contact Center Vozip
                        </div>
            </nav>




            <?php

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }


            // Inicializar los valores como 0
            $totalDelivered = 0;
            $totalRejected = 0;
            // Asegurarse de que $resultEvents esté inicializado
            $resultEvents = null;

            // Manejo de la solicitud POST cuando se envía el formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Datos para la autenticación
                $username = 'AtlasSeguridad';
                $password = 'KNhs32#%';

                // ID del envío y fecha del formulario HTML
                $sendingId = $_POST['sendingId'];
                $fecha = $_POST['fecha'];

                // Verificar si ya existen resultados en la base de datos para este sendingId
                $sqlCheck = "SELECT COUNT(*) AS count FROM reportes_voice WHERE sendingId = '$sendingId'";
                $resultCheck = $conn->query($sqlCheck);

                if ($resultCheck && $resultCheck->num_rows > 0) {
                    $rowCheck = $resultCheck->fetch_assoc();
                    $count = $rowCheck['count'];

                    if ($count > 0) {
                        // Ya existen resultados en la base de datos para este sendingId, recuperar los datos
                        $sqlEvents = "SELECT namecapañana, evento, phone, duracion FROM reportes_voice WHERE sendingId = '$sendingId'";
                        $resultEvents = $conn->query($sqlEvents);

                        echo "<script>";
                        echo "document.getElementById('resultContainer').innerHTML = 'Resultados recuperados de la base de datos';";
                        echo "</script>";
                    }
                }

                if (!$resultEvents) {
                    // No se encontraron resultados en la base de datos, realizar la consulta a la API
                    // Formatear la fecha para incluirla en la URL de la API
                    $formattedDate = date('Y-m-d\TH:i:s', strtotime($fecha));
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
                        // Procesar el CSV recibido y guardar en la base de datos
                        // Procesar el CSV recibido
                        $csvData = str_getcsv($response, "\n"); // Convertir CSV a matriz de filas


                        if (!empty($csvData)) {
                            $firstRowSkipped = false;
                            foreach ($csvData as $row) {
                                if (!$firstRowSkipped) {
                                    $firstRowSkipped = true; // Saltar la primera fila (encabezado)
                                    continue;
                                }

                                $rowData = str_getcsv($row); // Obtener datos de cada fila
                                // Aquí puedes procesar los datos de cada fila según tus necesidades
                                // Por ejemplo, supongamos que queremos almacenar solo ciertos campos en la base de datos
                                if (count($rowData) >= 3) { // Supongamos que el CSV tiene al menos 3 columnas
                                    $campo1 = $rowData[0];
                                    $campo2 = $rowData[4];
                                    $campo3 = $rowData[5];
                                    $campo4 = $rowData[9];

                                    // Insertar los datos en la base de datos
                                    $sql = "INSERT INTO reportes_voice (namecapañana, evento, phone, duracion, sendingId) VALUES ( '$campo1', '$campo2', '$campo3', '$campo4', '$sendingId')";

                                    if ($conn->query($sql) !== TRUE) {
                                        echo "<script>";
                                        echo "document.getElementById('resultContainer').innerHTML = 'Error al guardar el reporte en la base de datos: " . $conn->error . "';";
                                        echo "</script>";
                                        break; // Detener el bucle si hay un error
                                    }
                                }
                            }
                        } else {
                            echo "<script>";
                            echo "document.getElementById('resultContainer').innerHTML = 'Error al consultar el reporte. Código HTTP: $httpCode';";
                            echo "</script>";
                        }

                        // Cerrar la sesión cURL
                        curl_close($curl);

                        // Recuperar los resultados de la base de datos después de insertar nuevos datos (si es necesario)
                        $sqlEvents = "SELECT namecapañana, evento, phone, duracion FROM reportes_voice WHERE sendingId = '$sendingId'";
                        $resultEvents = $conn->query($sqlEvents);

                        // Recuperar los resultados de la base de datos después de insertar nuevos datos (si es necesario)
                        $sqlEvents = "SELECT mensaje, usuario, phone, duracion FROM envio_audios2 WHERE sendingId = '$sendingId'";
                        $resultEvents = $conn->query($sqlEvents);

                        echo "<script>";
                        echo "document.getElementById('resultContainer').innerHTML = 'Reporte obtenido correctamente y guardado en la base de datos';";
                        echo "</script>";
                    }
                }
            }


            // Realizar una consulta para obtener el recuento de eventos "delivered" y "rejected" para este sendingId
            $sqlDelivered = "SELECT COUNT(*) AS total_delivered FROM reportes_voice WHERE sendingId = '$sendingId' AND evento = 'delivered'";
            $resultDelivered = $conn->query($sqlDelivered);
            $totalDelivered = ($resultDelivered->num_rows > 0) ? $resultDelivered->fetch_assoc()['total_delivered'] : 0;

            $sqlRejected = "SELECT COUNT(*) AS total_rejected FROM reportes_voice WHERE sendingId = '$sendingId' AND evento = 'rejected'";
            $resultRejected = $conn->query($sqlRejected);
            $totalRejected = ($resultRejected->num_rows > 0) ? $resultRejected->fetch_assoc()['total_rejected'] : 0;

            // Consulta adicional para obtener detalles de los eventos (opcional)
            $sqlEvents = "SELECT namecapañana, evento, phone, duracion FROM reportes_voice WHERE sendingId = '$sendingId'";
            $resultEvents = $conn->query($sqlEvents);




            // Cerrar la conexión a la base de datos
            $conn->close();

            echo "Consulta SQL: " . $sql;




            ?>



        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Reportes SMS</li>
                    </ol>
                    <div class="container">
                        <h3 class="mt-5">Resultado SMS</h3>
                        <hr>

                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Llamadas enviadas: <?php echo $total_enviados; ?></div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">Llamadas rechazadas: <?php echo $total_rejected; ?></div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <form id="reportForm" action="" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sendingId">ID del Envío:</label>
                                            <select class="form-control" id="sendingId" name="sendingId" required>
                                                <option value=''>Selecciona Una Campaña</option>
                                                <?php echo $options; ?>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha">Fecha y Hora:</label>
                                            <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Consultar Reporte</button>
                                </div>
                            </form>
                            <table id="tuTabla" class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Nombre de Campaña</th>
                                        <th>Evento</th>
                                        <th>Teléfono</th>
                                        <th>Duración</th>
                                        <th>Mensaje</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php

                                    $mysqli = new mysqli('localhost', 'root', '', 'contact_center');

                                    // Verifica la conexión
                                    if ($mysqli->connect_errno) {
                                        echo "Falló la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                                    }


                                    // Verificar si la consulta fue exitosa y $resultEvents tiene un valor válido
                                    if ($resultEvents && $resultEvents->num_rows > 0) {
                                        // Aquí puedes utilizar $resultEvents para mostrar los detalles de los eventos
                                        while ($row = $resultEvents->fetch_assoc()) {
                                            // Procesar los datos de cada evento
                                            echo "<tr>";
                                            echo "<td>{$row['namecapañana']}</td>";
                                            echo "<td>{$row['evento']}</td>";
                                            echo "<td>{$row['phone']}</td>";
                                            echo "<td>{$row['duracion']}</td>";

                                            // Consulta adicional para obtener el mensaje y el usuario
                                            $queryMessages = "SELECT mensaje, usuario FROM envio_audios2 ";
                                            $resultMessages = $mysqli->query($queryMessages);

                                            if ($resultMessages && $resultMessages->num_rows > 0) {
                                                $messageRow = $resultMessages->fetch_assoc();
                                                echo "<td>{$messageRow['mensaje']}</td>";
                                                echo "<td>{$messageRow['usuario']}</td>";
                                            } else {
                                                echo "<td>No disponible</td>";
                                                echo "<td>No disponible</td>";
                                            }

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No se consultaron resultados.</td></tr>";
                                    }

                                    ?>



                                </tbody>
                            </table>

                        </div>

                    </div>


            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; <a href="https://vozipcolombia.net.co/">Vozip Bussines Of Techonology</a> </div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    </footer>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <!-- Incluir scripts de AdminLTE3 (jQuery y Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.4.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluir script de AdminLTE3 -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <!-- Incluir los estilos de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <!-- Incluir jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- Incluir DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <!-- Incluir las bibliotecas de botones de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>




    <script type="text/javascript">
        $(document).ready(function() {
            $('#tuTabla').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'pdf', 'csv', 'excel'
                ]
            });
        });
    </script>




</body>

</html>