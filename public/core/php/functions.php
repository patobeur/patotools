<?php
	/**
	 * autouploader de class / stacking class
	 */
	function chargerClasse($classe) {
		if(file_exists(ROOTS['class'].$classe.ROOTS['extphp'])){
			$file = ROOTS['class'].$classe . ROOTS['extphp'];
			require_once $file;
			$_SESSION['cms']['autoload'][] = "New Class $classe"."() chargée.";
		} else {
			$_SESSION['cms']['errors'][] = "New Class $classe"."() n'est pas chargée correctement.";
			die('la classe ? '.$classe);
		}
	}
	spl_autoload_register('chargerClasse');
	// ---------------------------------------------------------------------------	
	// DATES TEXTS & OTHER VALUES TOOLS
	// ---------------------------------------------------------------
	
	function dateDuJour($format='classic'){
		switch($format){
			case 'arrivalDate':
				$date = date('d/m/Y', time());
			break;
			case 'arrivalHour':
				$date = date('H:i:s', time());
			break;
			case 'timestamp':
				$date = date('d/m/Y H:i:s', time());
			break;
			case 'hourfull':
				$date = date('H:i:s');
			break;
			case 'dayfull':
				$date = date('Y-m-d');
			break;
			case 'classic':
				$date = date('Y-m-d H:i:s');
			break;

		}
		return $date;
	}	

	function get_diffDate($debut,$fin){
		$dif = ceil(abs($fin - $debut) / 86400);
		return $dif;
	}
	function get_clean($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function str_aireplace($source,$remplacementIdx){
		if (!empty($source) AND gettype($source) === 'string' AND !empty($remplacementIdx) AND is_array($remplacementIdx)){
			foreach($remplacementIdx as $name => $content){
				$source = str_replace($name, $content, $source);
			}
			return $source;
		}
		return false;
	}
	// LOG TOOLS -----------------------------------------------------------------
	/**
	 * clean print_r function
	 * @param $paquet array give me something to print like a string
	 * @param $title string give me something to print like a string or integer
	 */
	function print_air($paquet,$title='',$top=false,$hr=false){
		if (ROOTS['debug']){
				$hr = (!empty($hr)) ? "<hr>" : "";
				$br = (!empty($top)) ? PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL : "";
				if (!headers_sent()) {
					header(WEBSITE['header']);
				}
				print($hr.$br.'<pre>');
				// echo "function(".__FUNCTION__.")<br>";
				$title ? print($title.': ') : '';//print('print_r: ');
				print_r($paquet);
				print('</pre>');
		}
	}
	function print_airZ($tab = array(), $bloc = "",$kkk='', $strong = false, $niveau = 0) {
		$bloc = !empty($bloc) ? $bloc : '';
		if(is_object($tab)) {$tab = get_object_vars($tab);}	
		if($niveau != 0) {$bloc .= "<br/>";}	
		foreach($tab as $key => $value) {
			if($strong === true) {
					$bloc .= str_repeat("&nbsp;", $niveau * 4)."<strong>".$key."</strong> => ";
			} else {
				$bloc .= str_repeat("&nbsp;", $niveau * 4).$key." => ";
			}
			if(is_array($value) || is_object($value)) {
				$kkk = $key;
				$bloc = print_airZ($value, $bloc,$kkk, $strong, $niveau + 1);
				continue;
			}
			$bloc .= ($kkk === 'errors' ? '<span style="color:red">' : '') . $value. ($kkk === 'errors' ? '</span>' : '') . "<br/>";
		}
		return $bloc;
	}
	// FILES TOOLS --------------------------------------------------------------
	function requireonce($fileandpath){
		if (file_exists($fileandpath)){
			$_SESSION['cms']['require'][] = '--requireonce:'.$fileandpath;
			return require_once($fileandpath);
		}else{$_SESSION['cms']['errors'][] = '--cant requireonce:'.$fileandpath;}
	}
	function includeonce($fileandpath){
		if (file_exists($fileandpath)){
			$_SESSION['cms']['include'][] = '--include_once:'.$fileandpath;
			include_once($fileandpath);
		}else{$_SESSION['cms']['errors'][] = '--cant include_once:'.$fileandpath;}
	}
	// function get_Ip(){
	// 		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	// 				//ip from share internet
	// 				$ip = $_SERVER['HTTP_CLIENT_IP'];
	// 		} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	// 				//ip pass from proxy
	// 				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	// 		} else{
	// 				$ip = $_SERVER['REMOTE_ADDR'];
	// 		}
	// 	return $ip;
	// }
    /**
    * Get current user IP Address.
    * @return string
    */
    function get_ip_address() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
            // Make sure we always only send through the first IP in the list which should always be the client IP.
            return (string) trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }
?>