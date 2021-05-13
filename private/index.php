<?php
	session_start();
	session_destroy();
	header("Location: http".(empty($_SERVER['HTTPS']) ? '': 's').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
?>