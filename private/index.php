<?php
	session_start();
	session_destroy();
	unset($_COOKIE['barrecode']);
	header("Location: ".'http://'.$_SERVER['HTTP_HOST']);
?>