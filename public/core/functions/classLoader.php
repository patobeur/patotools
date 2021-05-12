<?php
	/**
	 * chargeur de class / class loader
	 * @param  mixed $classe string file name only
	 * @return void die if error
	 */
	function classLoader($classe) { // merci JérômeB
		$filepath = ROOTS['class'].$classe.ROOTS['extphp'];
		file_exists($filepath) ? require_once($filepath) : die("Can't mount ? ".__FUNCTION__."(".$classe.")");
	}
	spl_autoload_register('classLoader');
