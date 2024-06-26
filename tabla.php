<?php
// Conexión a la base de datos (ajusta estos valores según tu configuración)
$servername = "localhost";
$username = "root";
$password = "";
$database = "contact_center";

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Realizar una consulta para obtener las campañas
$query = "SELECT DISTINCT campana FROM envios_sms"; // Ajusta la consulta según tu esquema de base de datos
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contact Center Vozip</title>

    <!-- Incluye DataTables CSS -->
    <link href="/contact-center/DataTables/datatables.min.css" rel="stylesheet">

    <!-- Incluye DataTables Buttons CSS -->
    <link href="/contact-center/DataTables/Buttons-2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

    <!-- Incluye jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluye DataTables JavaScript -->
    <script src="/contact-center/DataTables/datatables.min.js"></script>

    <script src="/contact-center/DataTables/Buttons-2.4.2/js/buttons.html5.min.js"></script>


    <!-- Incluye DataTables Buttons JavaScript -->
    <script src="/contact-center/DataTables/Buttons-2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="/contact-center/DataTables/JSZip-2.5.0/jszip.min.js"></script>
    <script src="/contact-center/DataTables/pdfmake-0.1.28/pdfmake.min.js"></script>
    <script src="/contact-center/DataTables/pdfmake-0.1.28/vfs_fonts.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>




    <!-- Estilos personalizados -->
    <style type="text/css">
        .bs-example {
            margin: 40px;
        }
    </style>

    <!-- Incluye jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluye DataTables CSS y JavaScript -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Incluye DataTables Buttons CSS y JavaScript -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>

    <!-- Estilos personalizados -->
    <style type="text/css">
        .bs-example {
            margin: 20px;
        }
    </style>
</head>

<body>
    <div class="bs-example">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Reporte Envios De Recordatorios</h2>
                    </div>

                    <div class="text-left">
                        <button class="btn btn-primary" onclick="location.href='valida.php'">Actualizar Tabla</button>
                    </div>

                    <div class="filter-container">
                        <label for="campaña-filter">Filtrar por Campaña:</label>
                        <select id="campaña-filter">
                            <option value="">Todas las campañas</option>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['campana'] . "'>" . $row['campana'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table id="listaUsuarios" class="table table-sm table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Campaña</th>
                                    <th>Paciente</th>
                                    <th>Celular</th>
                                    <th>Mensaje</th>
                                    <th>Estado Envío</th>
                                    <th>Fecha Envío</th>
                                    <th>Usuario</th>
                                    <th>Resultado Respuesta</th>
                                    <th>Respuesta del Cliente</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Campaña</th>
                                    <th>Paciente</th>
                                    <th>Celular</th>
                                    <th>Mensaje</th>
                                    <th>Estado Envío</th>
                                    <th>Fecha Envío</th>
                                    <th>Usuario</th>
                                    <th>Resultado Respuesta</th>
                                    <th>Respuesta del Cliente</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ...

        $(document).ready(function() {
            var table = $('#listaUsuarios').DataTable({
                "processing": true,
                "serverSide": false, // Cambiar a false para cargar todos los datos en la tabla
                "lengthChange": false, // Desactiva la opción "Show entries"
                "ajax": "extraer.php",
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Descargar Excel',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Descargar PDF',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['15%', '15%', '10%', '10%', '10%', '10%', '10%', '10%', '10%']; // Establece el ancho de las columnas
                            doc.pageOrientation = 'landscape'; // Cambia la orientación a horizontal
                        }
                    },
                    'print'
                ]
            });

            // Agrega un evento de cambio al filtro de campaña
            $('#campaña-filter').on('change', function() {
                var selectedCampaña = $(this).val();
                table.column(0).search(selectedCampaña).draw(); // 0 es el índice de la columna "Campaña"
            });
        });
    </script>
</body>

</html>