<?php 
	Fun::print_air("effacement cookie");
	DataLogs::writeToLogs("visites",'Deconnexion de: userID:'.$_SESSION['session']['userID'] . ' userIP:'.$_SERVER['REMOTE_ADDR'] . ' email:'.$_SESSION['session']['userEmail'] . ' last Token:'.$_SESSION['session']['tokenCode'],[__FILE__,__LINE__]);
	session_destroy();
	
	if (trait_exists('t_Cookies',false)) {t_Cookies::_unsetCookie();}
	header("Location: ".'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	// die();