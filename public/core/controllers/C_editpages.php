<?php 
	$currentVue = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent.".php");
	$currentVue = str_replace("#CONTENTS#", '<h2>La Page :'.self::$_pageCurrent.'</h2>'.makemetabable($this->_lesPagesSQL), $currentVue);
	$affichagevalues = [
		'id_page' => [
			'hidden' => true
			,'type' => 'id'
		],
		'type', 'accred', 'ismenu', 'name', 'auth', 'author', 'title', 'parent', 'active', 'content', 'url', 'urltitle', 'classsup'
	];

	function makemetabable($datasindex){
		if (is_array($datasindex)){
			if ($datasindex){
				$tableau = '
				<div class="" title="">
						<table>#LIGNES#</table>';
				$intitules = '';
				$lignes = '';
				$boucle = 0;
				//	Fun::print_air($datasindex);
				foreach($datasindex as $key => $value){
					if (is_array($value)){
						$colonnes = '';
						foreach($value as $key2 => $value2){
								$colonnes .= '<td>'.(!empty($value2) ? (is_array($value2) ? 'ARRAY': htmlentities($value2)) : 'null').'</td>';
								//	Fun::print_air($value2);
								if ($boucle === 0) {
									$intitules .= '<td>['.$key2.']</td>';
								}
						}
						$boucle++;
						$lignes .= '<tr>'.$colonnes.'</tr>';
					}
				}
				$tableau = str_replace('#LIGNES#', '<tr>'.$intitules.'</tr>'.$lignes, $tableau);
				
				$tableau .= "</div>";
				return $tableau;
			}
		}
		return 'vide';
	}

	//	Fun::print_air($this->_lesPagesSQL);
	// $tousLesRetards = Database::get_ToutesLesPages($codebarre);
	// makelisteHtml($this->_lesPagesSQL);
