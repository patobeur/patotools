<?php
	$timer = microtime(true);
	// mytools
	require_once(ROOTS['traits'].'Fun'.ROOTS['extphp']);
	// classLoader Traits
	require_once(ROOTS['functions']."classLoader".ROOTS['extphp']);

	// main html vue
	$pagehtml = file_get_contents( ROOTS['vues'].""."main".ROOTS['exthtml']);

	function renderHtmlPage($pagehtml){
		$pagehtml = str_replace('#CHRONOS#', "Traitement: " . (microtime(true) - CHRONOS) . ' sec', $pagehtml);
		//affichage de la page html
		Fun::set_header();
		echo $pagehtml; // echo or print ??? what else ?
	}