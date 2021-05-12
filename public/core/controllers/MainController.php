<?php
	!ROOTS['distant'] ? ini_set('display_errors',1) : '';
	$timer = microtime(true);
	require_once(ROOTS['functions'].'functions.php');
	require_once(ROOTS['sessions'].'langs.php');
	require_once(ROOTS['sessions'].'session.php');
	require_once(ROOTS['traits'].'Fun.php');
	require_once(ROOTS['traits'].'DataLOG.php');
	require_once(ROOTS['traits'].'Formulary.php');
	require_once(ROOTS['functions'].'cookies.php');
	//WEBACTIONS['cssLess'] === true ? requireonce(ROOTS['controllers'].'CssController.php') : ''; //! LESS -> CSS Maker

	new DataLogs(); //! Mounting DataLogs Class
	new Database(); //! Mounting Database Class
	new User(); //! Mounting User Class
	new Page(); //! Mounting Page Class
	