<?php
	!ROOTS['distant'] && ROOTS['debug'] ? ini_set('display_errors',1) : '';
	$timer = microtime(true);
	require_once(ROOTS['sessions'].'langs.php');
	require_once(ROOTS['functions'].'functions.php');
	require_once(ROOTS['traits'].'Fun.php');
	
	require_once(ROOTS['sessions'].'session.php');

	new DataLogs(); //! Mounting DataLogs Class
	new Database(); //! Mounting Database Class
	new Checkbdd(); //! Mounting Checkbdd Class
	new User(); //! Mounting User Class
	new Page(); //! Mounting Page Class
	