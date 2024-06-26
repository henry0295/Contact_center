<?php

header('Content-Type: application/json');

// Set up the ORM library
require_once('setup.php');

if (isset($_GET['start']) AND isset($_GET['end'])) {
	
	$start = $_GET['start'];
	$end = $_GET['end'];
	$data = array();

	// Select the results with Idiorm
	$results = ORM::for_table('envios_fijos')
			->where_gte('fecha_envio', $start)
			->where_lte('fecha_envio', $end)
			->order_by_desc('fecha_envio')
			->find_array();


	// Build a new array with the data
	foreach ($results as $key => $value) {
		$data[$key]['label'] = $value['fecha_envio'];
		$data[$key]['value'] = $value['numero_telefono'];
	}

	echo json_encode($data);
}
