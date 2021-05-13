<?php
// CORE INFO
define('SEEDS', [
	'root' => ''
	,'core' => 'core/'
	,'assets' => 'assets/'
	,'redirecturl' => "https://github.com/patobeur" // redirect url
]);
// Directories
define('ROOTS', [
	'htmlheader'=> 'Content-type: text/html; charset=UTF-8'
	,'distant'=> false
	,'vues' => SEEDS['root'].SEEDS['core'].'vues/'
	,'functions' => SEEDS['root'].SEEDS['core'].'functions/'
	,'class' => SEEDS['root'].SEEDS['core'].'class/'
	,'controllers' => SEEDS['root'].SEEDS['core'].'controllers/'
	,'traits' => SEEDS['root'].SEEDS['core'].'traits/'
	,'less' => SEEDS['root'].SEEDS['assets'].'less/'
	,'css' => SEEDS['root'].SEEDS['assets'].'css/'
	,'js' => SEEDS['root'].SEEDS['assets'].'js/'
	,'img' => SEEDS['root'].SEEDS['assets'].'img/'
	,'extphp' => '.php'
	,'exthtml' => '.html'
	,'extlog' => '.log'
]);
define('SITEURL', ['http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])]);

define('ACTIONS', [
	'print_r'=> true
]);
