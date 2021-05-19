<?php
	/**
	 * autouploader de class / stacking class
	 */
	function chargerClasse($classe) {
		switch (substr($classe,0,2)){
			case 't_':
				$path = 'traits';
				$type = 'Trait';
			break;
			default : 
				$path = 'class';
				$type = 'Class';
			break;
		}
		if(file_exists(ROOTS[$path].$classe.ROOTS['extphp'])){
			$file = ROOTS[$path].$classe . ROOTS['extphp'];
			require_once $file;
			$_SESSION['cms']['autoload'][] = "New ".$type." $classe"."() chargée.";
		} else {
			$_SESSION['cms']['errors'][] = "New ".$type." $classe"."() n'est pas chargée correctement.";
			die('la classe ? '.$classe);
		}
	}
	spl_autoload_register('chargerClasse');

	// // FILES TOOLS --------------------------------------------------------------
	// function requireonce($fileandpath){
	// 	if (file_exists($fileandpath)){
	// 		$_SESSION['cms']['require'][] = '--requireonce:'.$fileandpath;
	// 		return require_once($fileandpath);
	// 	}else{$_SESSION['cms']['errors'][] = '--cant requireonce:'.$fileandpath;}
	// }
	// function includeonce($fileandpath){
	// 	if (file_exists($fileandpath)){
	// 		$_SESSION['cms']['include'][] = '--include_once:'.$fileandpath;
	// 		include_once($fileandpath);
	// 	}else{$_SESSION['cms']['errors'][] = '--cant include_once:'.$fileandpath;}
	// }
	