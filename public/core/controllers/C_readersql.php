<?php

// use function PHPSTORM_META\type;
new ImportDatabase(); //! Mounting ImportDatabase Class

	// a deplacer dans les definitions
	$CdT = [ 
		'choix'=> 'articles',// ChoixDesTables
		'articles'=> [
			'compare' => ['glpi'],
			'from'=> [
				"numbdd" => 2,
				"numtab" => 1
			],
			'to'=> [
				"numbdd" => 0,
				"numtab" => 1
			]
		],
		'membres'=> [
			'compare' => ['barrecode'],
			'from'=> [
				"numbdd" => 1,
				"numtab" => 5
			],
			'to'=> [
				"numbdd" => 0,
				"numtab" => 10
			]
		]
	];

	//	Fun::print_airImportDatabase::getWhere());
	$whereSqlString = "";
	if($where = ImportDatabase::getWhere()){
		$texteee = "";
		foreach($where as $key => $value){
			foreach($value as $key2 => $value2){
				switch($key2){
					case '<>':
					case '=':
						$minibind = "";
						foreach($value2 as $value3){
							$minibind .= ($minibind != "" ? "AND " : "") . "".$key." ".$key2 . " ". $value3 ." ";
						}
						$texteee .= ($texteee != "" ? "AND " : "") . $minibind;
					break;
					case "IN":
						$texteee .= ($texteee != "" ? "AND " : "")."".$key." ".$key2." (";
						$minibind = "";
						foreach($value2 as $value3){
							$virgule = $minibind != "" ? "," : "";
							$minibind .= $virgule . $value3;
						}
						$texteee .= $minibind.")";
					break;
					default:
					// nada
				};
			}
		}
		$whereSqlString = " WHERE ".$texteee;
	}
	// $table_from = ImportDatabase::get_SourceDatas(// requete source
	// 	$CdT[$CdT['choix']]['from']['numbdd'],
	// 	$CdT[$CdT['choix']]['from']['numtab'],
	// 	$whereSqlString
	// );	
	$table_from = ImportDatabase::get_SourceDatas(// requete source
		$CdT[$CdT['choix']]['from']['numbdd'],
		$CdT[$CdT['choix']]['from']['numtab'],
		$whereSqlString
	);	
	$table_to = ImportDatabase::get_DestinationDatas(// requete destination
		$CdT[$CdT['choix']]['to']['numbdd'],
		$CdT[$CdT['choix']]['to']['numtab'],
		false
	);

	// si reponses et resultats on compte sinon on met a zero
	$table_from_count = (isset($table_from[1]) && is_array($table_from[1])) ? count($table_from[1]) : 0;
	$table_to_count = (isset($table_to[1]) && is_array($table_to[1])) ? count($table_to[1]) : 0;


	// on nomme juste pour un affichage temporaire 
	$table_from_name = DB['ImportSql'][$CdT[$CdT['choix']]['from']['numbdd']]['DBtabs'][$CdT[$CdT['choix']]['from']['numtab']][0];
	$table_to_name = DB['ImportSql'][$CdT[$CdT['choix']]['to']['numbdd']]['DBtabs'][$CdT[$CdT['choix']]['to']['numtab']][0];
	$table_from_dbname = DB['ImportSql'][$CdT[$CdT['choix']]['from']['numbdd']]['DBname'];
	$table_to_dbname = DB['ImportSql'][$CdT[$CdT['choix']]['to']['numbdd']]['DBname'];

	// vues utiles à l'affichage
	$currentVue = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent.".php");
	$currentVue_navitems = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent."_navitems.php");
	$currentVue_navitem = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent."_navitem.php");
	
	
	// si _importation est active = true
	if (ImportDatabase::get_importation()) {
		// SOURCE
		$currentVue = str_replace("#SDBName#", $table_from_dbname, $currentVue);
		$currentVue = str_replace("#STBName#", $table_from_name, $currentVue);
		if( isset($table_from) AND isset($table_from[1]) AND is_array($table_from[1]) AND count($table_from[1])>0){
			$colstypes_S = ImportDatabase::get_colstypes($CdT[$CdT['choix']]['from']['numbdd'],$CdT[$CdT['choix']]['from']['numtab'],"source");
			$inputselect_S = inputselect($colstypes_S,'source',false,'sourcebind');

			$currentVue = str_replace("#SDBNbLines#",$inputselect_S,$currentVue);
			$currentVue = str_replace("#SDBNbChamps#", $table_from_count, $currentVue);			
			$currentVue = str_replace("#SDBCONTENTS#", bootstrapmethis($table_from,'sourcesql',$colstypes_S,'ddd',$table_to_dbname.".".$table_to_name), $currentVue);
		} else {
			$currentVue = str_replace("#SDBCONTENTS#", bootstrapmethis([1,[['Message'=>'La table est vide ou ... un bug !']]]), $currentVue);
		}

		// DESTINATION
		$currentVue = str_replace("#DDBName#", $table_to_dbname, $currentVue);
		$currentVue = str_replace("#DTBName#", $table_to_name, $currentVue);
		if( isset($table_to) AND isset($table_to[1]) AND is_array($table_to[1]) AND count($table_to[1])>0){
			$colstypes_D = ImportDatabase::get_colstypes($CdT[$CdT['choix']]['to']['numbdd'],$CdT[$CdT['choix']]['to']['numtab'],"destination");
			$inputselect_D = inputselect($colstypes_D,'destination',false,'destinationbind');

			$currentVue = str_replace("#DDBNbLines#", $inputselect_D, $currentVue);
			$currentVue = str_replace("#DDBNbChamps#", $table_to_count, $currentVue);			
			$currentVue = str_replace("#DDBCONTENTS#", bootstrapmethis($table_to,'destsql',$colstypes_D,'test_to',$table_to_name), $currentVue,);
		} else {
			$currentVue = str_replace("#DDBCONTENTS#", bootstrapmethis([1,[['Message'=>'La table est vide ou ... un bug !']]]), $currentVue);
		}










		$content = "";
		$currentVue = str_replace("#CONTENTS#",$content,$currentVue
		);
	} else {
		$content = bootstrapmethis([1,[['Message'=>'Import des tables desactivée !']]]);
	}
function getTypeOf($paquet){
	return gettype($paquet);
}

function bootstrapmethis($datasindex,$identity=false,$bindtype=false,$attribs=false,$dbname=false){
	//	Fun::print_air'gg-'.$dbname);
	$rules = [
		"","null","-"
	];
	if (is_array($datasindex[1])){
		if (isset($datasindex[0])){
			$att = $attribs ? ' idcol="'.$attribs.'"' : "";
			$att2 = $dbname ? ' db="'.$dbname.'"' : "";
			$identity = $identity ? ' id="'.$identity.'"' : "";
			$style = ' style="cellspacing=0"';
			$tableau = '
			<div class="">
					<table style="cellspacing=0"'.$att.$att2.$identity.'
						data-toggle="table"
						data-height="460"
						data-pagination="true"
						data-pagination="true"
						class="table"
					>#LIGNES#</table>';
			$intitules = '';
			$lignes = '';
			$intituleSiLigneZero = 0;


			foreach($datasindex[1] as $key => $value){
				if (is_array($value)){
					$intituleSiColZero = 0;
					$colonnes = '';
					foreach($value as $key2 => $value2){

						$eventclass = (random_int(1, 100)<=20) ? ' class="event"' : '';

						$type = (isset($bindtype[$key2][1]) && $bindtype[$key2][1] === 'int') ? ' d="1"' : "";

						if ($intituleSiColZero === 0 && $intituleSiLigneZero === 0) {
							$intitules .= '<td>[Val]</td>';
						}
						if ($intituleSiColZero === 0) {
							$colonnes .= '<td><input type="checkbox" id="row_'.$intituleSiLigneZero.'" name="scales" checked></td>';
						}
						if ($intituleSiLigneZero === 0) {
							$intitules .= '<td'.$type.'>['.trim($key2).']</td>';
						}
						$colonnes .= '<td'.$eventclass.' title="testcss">'.(!empty($value2) ? (is_array($value2) ? 'ARRAY': htmlentities(trim($value2))) : 'null').'</td>';
						$intituleSiColZero++;
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


/**
 * inputselect
 *
 * @param  mixed $datasarray need array [['one','int'],[two','varchar']]
 * @param  mixed $label
 * @return void
 */
function inputselect($datasarray=false,$target,$label=false,$idselect=false){
	if ($datasarray && is_array($datasarray) AND count($datasarray)>0) {
		$options = '<option value="">--Liste --</option>';
		foreach($datasarray as $key => $value){
			$options .='<option value="'.$value[0].'">'.$value[0].'('.$value[1].')</option>';
		}
	}
	if ($label){
		$label = '<label for="">'.$label.'</label>';
	}
	if ($idselect){
		$idselect = ' id="'.$idselect.'"';
	}
	return isset($options) ? ($label??"").'<select name=""'.($idselect??"").'>'.$options.'</select>' : false;
}