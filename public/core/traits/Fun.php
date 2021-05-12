<?php
trait Fun {
	// Some'Tools
	static function get_clean($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
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
	static function TagMeThis($content='""', $class='""',$tag='span'){
		switch($tag){
			case 'span';
			case 'div';
			case 'td';
			case 'tr';
			case 'li';
			case 'lu';
			case 'ol';
			case 'i';
			case 'span';
				return '<' . $tag . ' class="' . $class . '">' . $content . '</' . $tag . '>';
			break;
		}
		return false;
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
		if (ACTIONS['print_r']){
				$hr = (!empty($hr)) ? "<hr>" : "";
				$br = ($top) ? '<br/><br/><br/>' : "";
				if (!headers_sent()) {
					header(ROOTS['htmlheader']);
				}
				print($hr.$br.'<pre>');
				$title ? print($title.': ') : '';
				print_r($paquet);
				print('</pre>');
		}
	}
	static function sqlToTable($datasindex,$tablenom="Tableau de données"){
		$intituleSiLigneZero = 0;
		$intitules = '';
		$lignes = '';
		
		$tableau = file_get_contents( ROOTS['traits']."/vues/"."Fun".ROOTS['exthtml']);

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

		$tableau = str_replace('#TABLETITLE#', $tablenom, $tableau);
		$tableau = str_replace('#THEAD#', $intitules, $tableau);
		$tableau = str_replace('#ROWS#', $lignes, $tableau);
		$tableau = str_replace('#TFOOTER#', $intitules, $tableau);

		return $tableau;
	}
}