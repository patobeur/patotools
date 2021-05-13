<?php
	session_start();
	define('CHRONOS',microtime(true));// Chrono Starter
	require_once('../private/definitions.php');// defined constants
	require_once(ROOTS['controllers']."MainController.php");// Mounting Main Controller

	//testing sqlToTable function
	Fun::print_air('sqlToTable',"test");
	$sqlRowsResult =  [0 => ["action" => "testing","date" => date('Y-m-d'),"ip" => $_SERVER['REMOTE_ADDR']]];
	$pagehtml = str_replace('#CONTENT1#', Fun::sqlToTable($sqlRowsResult,""), $pagehtml);
	
	//testing sqlToTable Class
	new DataLogs(); //! Mounting DataLogs Class
	
	if ($isLogWritten = DataLogs::writeToLogs("visites","test:writeToLogs()",[__FILE__,__FUNCTION__,__LINE__])){
		// add to the vue
		$pagehtml = str_replace('#CONTENT2#', '<br/>Testing writeToLogs(): <a href="'.$isLogWritten['fullpathfilename'].'" target="logs">log</a>', $pagehtml);
	}




	renderHtmlPage($pagehtml);
?>