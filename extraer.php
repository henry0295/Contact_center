<?php 
// Database connection info 
$dbDetails = array( 
'host' => 'localhost', 
'user' => 'root', 
'pass' => '', 
'db'   => 'contact_center'
); 
// mysql db table to use 
$table = 'envios_sms'; 
// Table's primary key 
$primaryKey = 'id'; 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
array( 'db' => 'campana', 'dt' => 0 ), 
array( 'db' => 'cliente',  'dt' => 1 ), 
array( 'db' => 'numero_telefono',      'dt' => 2 ),
array( 'db' => 'mensaje',      'dt' => 3 ),
array( 'db' => 'resultado_envio',      'dt' => 4 ),
array( 'db' => 'fecha_envio',      'dt' => 5 ),
array( 'db' => 'usuario',      'dt' => 6 ),
array( 'db' => 'resultado',      'dt' => 7 ),
array( 'db' => 'respuesta_cliente',      'dt' => 8 ),
); 
// Include SQL query processing class 
require 'ssp.class.php'; 
// Output data as json format 
echo json_encode( 
SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
);