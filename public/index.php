<?php
	session_start();
	// Chrono Starter
	define('CHRONOS',$_SERVER['REQUEST_TIME_FLOAT']);
	// Constants
	require_once('../private/definitions.php');
	// class Loader
	require_once(ROOTS['functions']."classLoader".ROOTS['extphp']);

	// chargement du Controller Principal
	// require_once(ROOTS['controllers']."MainController.php");

	// mytools
	require_once(ROOTS['traits'].'Fun'.ROOTS['extphp']);


	new DataLogs(); //! Mounting DataLogs Class

	
?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Patotools</title>
	<link href="theme/css/table.css" rel="stylesheet" media="screen">
</head>
<body>
<?php

	//testing print_air
	Fun::print_air('print_r',"test");

	//testing sqlToTable
	$sqlresult =  [0 => ["action" => "testing","date" => date('Y-m-d'),"ip" => $_SERVER['REMOTE_ADDR']]];
	$htmltable = Fun::sqlToTable($sqlresult,"");
	Fun::print_air('sqlToTable',"test");
	echo $htmltable;

	//testing sqlToTable
	DataLogs::writeToLogs("visites","test:writeToLogs()",[__FILE__,__FUNCTION__,__LINE__])
?>
<a href="logs/datalogs.visites.log" target="logs">log</a>
</body>
</html>
