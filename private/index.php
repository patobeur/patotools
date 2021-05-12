<?php
	session_start();
	session_destroy();
	header("Location: ".'https://imc.randstad.fr');
?>