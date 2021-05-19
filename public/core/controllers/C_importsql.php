<?php

// use function PHPSTORM_META\type;
new ImportDatabase(); //! Mounting ImportDatabase Class

	// $CdT = [ // ChoixDesTables
	// 	'choix'=> 'membres',
	// 	'articles'=> [ // ChoixDesTables
	// 		'compare' => ['barrecode'],
	// 		'from'=> [
	// 			"numbdd" => 2,
	// 			"numtab" => 1
	// 		],
	// 		'to'=> [
	// 			"numbdd" => 0,
	// 			"numtab" => 1
	// 		]
	// 	],
	// 	'membres'=> [
	// 		'compare' => ['barrecode','nom','prenom'],
	// 		'from'=> [
	// 			"numbdd" => 0,
	// 			"numtab" => 10
	// 		],
	// 		'to'=> [
	// 			"numbdd" => 1,
	// 			"numtab" => 5
	// 		]
	// 	]
	// ];
	// // choix importation 
	if (isset($_GET['x']) && isset($_GET['y'])){
		// mérite un peu plus de filtre --> sécurité (même en local ??)
		$numbdd = intval($_GET['x']);
		$numtab = intval($_GET['y']);
		$numbdd = ($numbdd!= '' && $numbdd >= 0 && $numbdd < count(DB['ImportSql'])) ? $numbdd : 0;
		$numtab = ($numtab!= '' && $numtab >= 0 && $numtab < count(DB['ImportSql'][$numbdd]['DBtabs'])) ? $numtab : 1;
	}

	$currentVue = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent.".php");
	$currentVue_navitems = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent."_navitems.php");
	$currentVue_navitem = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent."_navitem.php");
	
	// creation des menus
	function menuchoix($currentVue,$currentVue_navitems,$currentVue_navitem){
		$tempo = "";
		foreach(DB['ImportSql'] as $key => $values){
			if ($values){
				$tempo_vue = str_replace("#DBNAME#", $values['DBhost'].'['.$values['DBname'].'@'.$values['DBuser'].']', $currentVue_navitems);
				$navtables = "";
				$i = 0;
				foreach($values['DBtabs'] as $tabname){
					if (isset($tabname[0][0]) && $tabname[0][0]){
						$table = $tabname[0];
						$blok = str_replace("#ITEM#", $table, $currentVue_navitem);
						$blok = str_replace("#ITEMURL#", '?'.Page::$_pageCurrent.'&x='.$key.'&y='.$i, $blok);
						$blok = str_replace("#ITEMTITLE#", $table, $blok);
						$navtables .= $blok;
					}
					$i++;
				}
				$tempo .= str_replace("#ITEMS#", $navtables, $tempo_vue);
			}
		}
		return str_replace("#NAVITEMS#", $tempo, $currentVue);
	}
	
	function menusselects($datasindex){
		$MENUSELECTS = [];
		$textetext = "";
		if (isset($datasindex[0])){
			if (is_array($datasindex[1])){
				$COLS = [];
				foreach($datasindex[1] as $key => $value){ // pour chaque ligne
					$i = 0;
					if (is_array($value)){ // lignes avec un array
						foreach($value as $key2 => $value2){ 	// pour chaque colonnes  
							if(! isset($COLS[$key2])){			// si on ne l'a deja pas dans notre tableau
								$COLS[$key2] = []; 				//on ajoute un champs portant le nom de la colonne et on y met un tablo vide
							}
							if (isset($value2) AND !is_array($value2) AND !empty($value2) ){
								$cleanvalue = htmlentities($value2);
								if(!in_array($cleanvalue,$COLS[$key2])
								){ // si on a PAS deja cette valeur dans la Colonne
									$COLS[$key2][] = $cleanvalue;
									$i++;
									// on la rajoute
								} // sinon on ne fait rien
							}
						}
					}
				}
				foreach($COLS as $key => $value){ // pour chaque ligne
					$MENUSELECTS[$key] = '<label for="'.$key.'">'.$key.'</label><select name="'.$key.'" id="'.$key.'"><option value="">--Liste '.$key.'--</option>';
					if (is_array($value) AND count($value)>0 AND count($value)<50) {
						foreach($value as $key2 => $value2){
							$MENUSELECTS[$key] .= '<option value="'.$value2.'">'.$value2.'</option>';
						}
						$MENUSELECTS[$key] .= '</select>';
						$textetext .= $MENUSELECTS[$key];
					}
					else { $MENUSELECTS[$key] = '';}
				}
			}
		}
		return count($MENUSELECTS) > 0 ? '<div id="">'.$textetext.'</div>' : false;
	}

	
	function bootstrapmethis($datasindex){
		if (is_array($datasindex[1])){
			if (isset($datasindex[0])){
				$tableau = '
				<div class="table-responsive" style="margin: 1rem;">
						<table id="fernandotable" 
							data-toggle="table"
							data-height="460"
							data-pagination="true"
							data-pagination="true"
							class="table table-light table-striped table-hover table-borderless table-sm"
						>#LIGNES#</table>';
				$intitules = '';
				$lignes = '';
				$intituleSiLigneZero = 0;


				//	Fun::print_air($datasindex);
				foreach($datasindex[1] as $key => $value){
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
				$tableau = str_replace('#LIGNES#', '<thead class="table-dark"><tr class="">'.$intitules.'</tr></thead><tbody>'.$lignes.'<tbody>', $tableau);
				
				// $tableau .= "<script>
				// $(document).ready(function () {
				//   $('#fernandotable').DataTable({
				// 	\"paging\": false
				//   });
				//   $('.dataTables_length').addClass('bs-select');
				// });
				// </script>";

				$tableau .= "</div>";

				return $tableau;
			}
		}
		return $datasindex[1];
	}
	// creation de la liste des tables lisibles !
	$currentVue = menuchoix($currentVue,$currentVue_navitems,$currentVue_navitem);

	if (ImportDatabase::get_lecture()) {

		if(isset($numbdd) && isset($numtab) ) {
		// si lecture est activee = true
			$contenudelatable = ImportDatabase::get_SourceDatas($numbdd,$numtab,false);
			if (isset($contenudelatable[0]) && $contenudelatable[0]) {
				$currentVue = str_replace("#FILTERFORM#", menusselects($contenudelatable), $currentVue);
				$content = bootstrapmethis($contenudelatable);
			} else {
				$currentVue = str_replace("#FILTERFORM#", '', $currentVue);
				$content = bootstrapmethis([1,[['Message'=>'La table est vide ou ... un bug !']]]);
			}
			// $content = '<h2>'.DB['ImportSql'][$numbdd]['DBhost'].'|'.DB['ImportSql'][$numbdd]['DBname'].'|'.DB['ImportSql'][$numbdd]['DBtabs'][$numtab][0].'</h2>'.$content;
		} else {
			$currentVue = str_replace("#FILTERFORM#", '', $currentVue);
			$content = '';
		}



		$currentVue = str_replace(
			"#CONTENTS#",
			$content,
			$currentVue
		);
	} else {
		$content = bootstrapmethis([1,[['Message'=>'La lecture des tables est desactivée !']]]);
	}
	//	Fun::print_air($this->_lesPagesSQL);
	// $tousLesRetards = Database::get_ToutesLesPages($codebarre);
	// makelisteHtml($this->_lesPagesSQL);
