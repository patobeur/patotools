<?php
trait Fun {
	// Some'Tools
	// ---------------------------------------------------------------
	static function stop($data=false,$source=false) {
		if (!isset($_SESSION['die'])){
			$_SESSION['die'] = [];
			$_SESSION['nbstop'] = 0;
		}

		if ($data==="onerror") {
			$message = "";
			if(count($_SESSION['die'])>0){
				$s = $_SESSION['nbstop']>1 ? "s" : "";
				$message = $_SESSION['nbstop']." erreur".$s." trouvée".$s." !";
			}
			for($i=0; $i < count($_SESSION['die']); $i++){
				Fun::print_air($_SESSION['die'][$i]);
				Datalogs::writeToLogs('errors', $_SESSION['die'][$i],[__FILE__,__FUNCTION__,__LINE__]);
			}
			if(count($_SESSION['die'])>0){
				unset($_SESSION['die']);
				unset($_SESSION['nbstop']);
				Datalogs::writeToLogs('errors', "unset(die et nbstop)",[__FILE__,__FUNCTION__,__LINE__]);
				die($message . "Arret forcée onerror!!"); // pour de vrai !
			}
			else {
				if (ROOTS['debug']){
					Datalogs::writeToLogs('errors', "stop('onerror') demandé par (".$source.") mais aucunne erreur detectée",[__FILE__,__FUNCTION__,__LINE__]);
					Datalogs::writeToLogs('errors', "stop('onerror') refusé à (".$source.") !!!",[__FILE__,__FUNCTION__,__LINE__]);
				}
			}
		}
		elseif ($data===false) {
			for($i=0; $i < count($_SESSION['die']); $i++){
				Fun::print_air($_SESSION['die'][$i]);
			}
			
			if(count($_SESSION['die'])>0){
				$s = $_SESSION['nbstop']>1 ? "s" : "";
				$message = $_SESSION['nbstop']." erreur".$s." trouvée".$s." !";
				Datalogs::writeToLogs('errors', $message,[__FILE__,__FUNCTION__,__LINE__]);
				Datalogs::writeToLogs('errors', "stop() accordé à (".$source.")",[__FILE__,__FUNCTION__,__LINE__]);
				unset($_SESSION['die']);
				unset($_SESSION['nbstop']);
				Datalogs::writeToLogs('errors', "unset(die et nbstop)",[__FILE__,__FUNCTION__,__LINE__]);
				die($message); // pour de vrai !
			}
			else {
				if (ROOTS['debug']){
					Datalogs::writeToLogs('errors', "stop() demandé par (".$source.") mais aucunne erreur detectée",[__FILE__,__FUNCTION__,__LINE__]);
					Datalogs::writeToLogs('errors', "stop() refusé à (".$source.") !!!",[__FILE__,__FUNCTION__,__LINE__]);
				}
			}
		}
		elseif ($data==="clean") {
			$_SESSION['die'] = [];
			$_SESSION['nbstop'] = 0;
			Datalogs::writeToLogs('errors', "Réinitialisation(die et nbstop) ".$source,[__FILE__,__FUNCTION__,__LINE__]);
		}
		elseif ($data==="destroy") {
			unset($_SESSION['die']);
			unset($_SESSION['nbstop']);
			Datalogs::writeToLogs('errors', "Destroy(die et nbstop) ".$source,[__FILE__,__FUNCTION__,__LINE__]);
		}
		else {
			$_SESSION['die'][] = "#". (count($_SESSION['die'])+1) .":".$data;
			$_SESSION['nbstop'] = $_SESSION['nbstop'] + 1;
		}
	}
	// ---------------------------------------------------------------
	static function refresh($delay,$url,$joker=true,$joker2="404",$detroySession) {
		if ($detroySession) {session_destroy();}
		header( "Refresh:".$delay."; url=".$url, $joker, $joker2);
		die();
	}
	// ---------------------------------------------------------------
	static function get_clean($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	// ---------------------------------------------------------------
		// DATES TEXTS & OTHER VALUES TOOLS
		// ---------------------------------------------------------------
	static function get_diffDate($debut,$fin){
		$dif = ceil(abs($fin - $debut) / 86400);
		return $dif;
	}
	// ---------------------------------------------------------------
	static function dateDuJour($format='classic'){
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
	/**
	 * TagMeThis encapsulater html
	 *
	 * @param  mixed $tag html tag (default=span)
	 * @param  mixed $html html content
	 * @param  mixed $class css class name
	 * @return void
	 */
	static function TagMeThis($tag='span', $html='""', $class='""'){
		if(($html AND $class AND $tag)){
			switch($tag){
				case 'span';
				case 'div';
				case 'td';
				case 'tr';
				case 'li';
				case 'ol';
				case 'i';
				case 'span';
					$html = '<' . $tag . ' class="fun ' . $class . '">' . $html . '</' . $tag . '>';
				break;
			}
		}
		return $html ?? false;
	}	
	/**
	 * clean print_r function
	 * @param $paquet array give me something to print like a string
	 * @param $title string give me something to print like a string or integer
	 * @param mixed $top true make display without br before content
	 * @param mixed $hr true display line before content
	 * @return void
	 */
	static function print_air($paquet,$title='',$top=false,$hr=false){
		if (ROOTS['debug']){
				$hr = (!empty($hr)) ? "<hr>" : "";
				$br = (!empty($top)) ? '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>' : "";
				if (!headers_sent()) {
					header(WEBSITE['header']);
				}
				print($hr.$br.'<pre>');
				// echo "function(".__FUNCTION__.")<br>";
				$title ? print(CONF['F'].$title.': ') : '';//print('print_r: ');
				print_r($paquet);
				print('</pre>');
		}
	}
	static function sqlToTable($datasindex,$tablenom="Tableau de données"){
		if($datasindex && is_array($datasindex)){
			$tableau = '
			<div class="form-page">
				<div class="message">
					<div class="titre">'.$tablenom.'</div>
					<div class="table-responsive">
						<table>#LIGNES#</table>';
			$intitules = '';
			$lignes = '';
			$intituleSiLigneZero = 0;

			foreach($datasindex as $key => $value){
				if (is_array($value)){
					$colonnes = '';
					foreach($value as $key2 => $value2){
							$colonnes .= '<td>'.(!empty($value2) ? (is_array($value2) ? 'ARRAY': htmlentities($value2)) : 'null').'</td>';
							if ($intituleSiLigneZero === 0) {
								$intitules .= '<td>['.$key2.']</td>';
							}
					}
					$intituleSiLigneZero++;
					$lignes .= '<tr>'.$colonnes.'</tr>';
				}
			}


			$tableau = str_replace('#LIGNES#', '<thead><tr>'.$intitules.'</tr></thead><tbody>'.$lignes.'</tbody><tfoot><tr>'.$intitules.'</tr></tfoot>', $tableau);
			$tableau .= "</div></div></div>";
			return $tableau;
		}
		return false;
	}
    /**
	 * Get current user IP Address.
	 * @return string
     */
    static function get_ip_address() {
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
	static function generateToken(){
			return md5(rand(1, 10) . microtime());
	}
	static function str_aireplace($source,$remplacementIdx){
		if (!empty($source) AND gettype($source) === 'string' AND !empty($remplacementIdx) AND is_array($remplacementIdx)){
			foreach($remplacementIdx as $name => $content){
				$source = str_replace($name, $content, $source);
			}
			return $source;
		}
		return false;
	}
	static function print_airZ($tab = array(), $bloc = "",$kkk='', $strong = false, $niveau = 0) {
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
				$bloc = Fun::print_airZ($value, $bloc,$kkk, $strong, $niveau + 1);
				continue;
			}
			$bloc .= ($kkk === 'errors' ? '<span style="color:red">' : '') . $value. ($kkk === 'errors' ? '</span>' : '') . "<br/>";
		}
		return $bloc;
	}
	/**
	 * get_SqlErreurTexte
	 *
	 * @param  mixed $e
	 * @param  mixed $server
	 * @return void
	 */
	static function get_SqlErreurTexte($e,$server="???",$caller){
		$string = "???";
		if ($e = $e->getCode()){
			switch ($e) {
				case "1045":
					$string = $server.' est refusée, droits insuffisants (28000) !';
				break;
				case "1049":
					$string = $server.' donne un nom de base inexistante (42000): Importation impossible !';
				break;
				case "2002":
					$string = $server.' ne répond pas à l\'adresse indiquée.';
				break;
				default:
					$string = $server.' donne une erreur :'.$e;
				break;
			}
		}
		DataLogs::writeToLogs("errors",$string,[__FILE__,__FUNCTION__,__LINE__]);
		return $string;
	}
}
