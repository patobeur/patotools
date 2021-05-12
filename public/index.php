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


	$pagehtml = file_get_contents( ROOTS['vues'].""."main".ROOTS['exthtml']);
	

	//testing print_air
	Fun::print_air('print_r',"test");

	//testing sqlToTable
	Fun::print_air('sqlToTable',"test");
	$sqlresult =  [0 => ["action" => "testing","date" => date('Y-m-d'),"ip" => $_SERVER['REMOTE_ADDR']]];
	$htmlcontent = Fun::sqlToTable($sqlresult,"");

	//affichage de la page html
	

	//testing sqlToTable
	$isLogWritten = DataLogs::writeToLogs("visites","test:writeToLogs()",[__FILE__,__FUNCTION__,__LINE__]);
	$htmlcontent .= '<a href="'.$isLogWritten['fullpathfilename'].'" target="logs">log</a>';



	
	$pagehtml = str_replace('#CONTENT#', $htmlcontent, $pagehtml);
	Fun::set_header();
	echo $pagehtml;

?>

</body>
</html>
