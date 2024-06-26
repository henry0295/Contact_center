<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: principal.php");
}

$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];


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
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>


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
                        <!-- <div class="sb-sidenav-menu-heading">Core</div> -->
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
                                    <!-- <a class="nav-link" href="Envio-mensaje-simple.php">Envio de Mesaje simple</a> -->
                                    <a class="nav-link" href="Envio-mensaje.php">Recordatorios</a>
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
                                    <a class="nav-link" href="Envio-correo-masivo.php">Campaña Recordatorios</a>
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
                                    <a class="nav-link" href="Envios-Audios.php">Recordatorios</a>
                                    <a class="nav-link" href="envia-audios-fijo.php">Campaña de Audios</a>
                                </nav>
                            </div>
                            <!-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts4" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class='far'>&#xf2b6;</i></div>
                                Campaña SMS - CORREOS
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts4" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="CampañaBlend.php">Campaña SMS Y CORREOS</a>
                                </nav>
                            </div> -->
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts2" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-bar mr-1"></i></div>
                                Reportes
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts2" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">

                                    <a class="nav-link" href="repo-SMS.php">Reporte de Envio Mensajes Recordatorios</a>
                                    <a class="nav-link" href="repo-SMS-fijo.php">Reporte de Envio Mensajes</a>
                                    <a class="nav-link" href="repo-audios.php">Reporte de Audios Recordatorios</a>
                                    <a class="nav-link" href="repo-audios-fijos.php">Reporte de Audios</a>
                                    <a class="nav-link" href="repo-CORREO.php">Reporte de Correos Recordatorios</a>
                                    <a class="nav-link" href="repo-CORREO-fijo.php">Reporte de Correos</a>

                                </nav>
                            </div>



                        <?php } ?>
                        <div class="sb-sidenav-menu-heading">Sistema</div>
                        <a class="nav-link" href="crear_usuario.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            Crear Usuario
                        </a><a class="nav-link" href="tabla.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Usuarios
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Contact Center Vozip
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Envio de Mensaje</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="principal.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Envio de Mensaje</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">

                            <div class="container">
                                <h3 class="mt-5">Formulario de envio</h3>

                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-12">
                                        <!-- Contenido -->
                                        <div class="signup-form-container">
                                            <!-- form start -->
                                            <!-- <form onsubmit="return validacion()" action="?c=sms&a=Enviar" method="POST" autocomplete="on"> -->
                                            <div class="form-header">
                                                <h3 class="form-title">
                                                    <!-- <i class="fa fa-user"></i> -->
                                                    <i class="material-icons">&#xe0c9;</i>
                                                    </span>SMS
                                                </h3>
                                            </div>

                                            </p>
                                        </div>
                                        <div class="col-md-5">
                                        </div>
                                    </div>
                                </div>

                                </p>
                            </div>
                            <!---FORMULARIO-->
                            <div>
                                <form action="sms-fijo.php" method="post" enctype="multipart/form-data">

                                    <div class="row">
                                        <div class="col-md-7">

                                            <form>
                                                <label for="csv">Cargar archivo CSV:</label>
                                                <input type="file" id="csv" name="csv" accept=".csv" required><br><br>

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Nombre Campaña</label>
                                                    <input type="text" class="form-control" id="campaña" name="campaña" placeholder="Nombre Campaña">

                                                </div>


                                                <button type="submit"> Enviar Mensaje</button>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

</body>

</html>
<!-- <div style="height: 100vh;"></div>
                        <div class="card mb-4"><div class="card-body">When scrolling, the navigation stays at the top of the page. This is the end of the static navigation demo.</div></div>
                    </div>
                </main> -->
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
</footer>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
</body>

</html>