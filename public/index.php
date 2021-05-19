<?php
	session_start();
	define('CHRONO',microtime(true)); 		// demarrage du chrono dans STARTT
	require_once('../private/definitions.php');				// chargement des constantes du serveur
	require_once('core/controllers/MainController.php');	// chargement du Controller Principal
?>