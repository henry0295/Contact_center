<?php 
// Database connection info 
$dbDetails = array( 
'host' => 'vozipcolombia.net.co', 
'user' => 'vozipco1_sms', 
'pass' => 'fMW_p~aUnewk', 
'db'   => 'vozipco1_recepcionsms'
); 
// mysql db table to use 
$table = 'respuestasms'; 
// Table's primary key 
$primaryKey = 'id'; 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
array( 'db' => 'mensaje', 'dt' => 0 ), 
array( 'db' => 'celular',  'dt' => 1 ), 
array( 'db' => 'fecha',      'dt' => 2 ), 
); 
// Include SQL query processing class 
require 'ssp.class.php'; 
// Output data as json format 
echo json_encode( 
SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
);