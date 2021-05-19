<?php 
	$modelVue = $this->getfilecontent( ROOTS['controllers'].'actions/vues/'.self::$_pageCurrent.".php");

	$actionDemande = WEBSITE['actionsParDefaut'];	
	$actionsPossibles = [];
	$titreActuel = 'Titre vide !';
	$nav_menu_buttons = '';
	
	// a intégrer dans install ou pas ou autres 
	$listeDesActions = [
		'emprunt' => [
			'actions' => 'emprunt',
			'vue' => 'barrecode_formulaire_emprunt',
			'form' => [
				'active' => true,
				'inputs' => ['barrecode'],
				'titre' => 'Empruntez du Matériel !',
				'defautcodebarre' => '201899990003540000'
			],
			'menu' => [
				'active' => true,
				'class' => 'emprunt',
				'href' => '?actions=',
				'content' => 'Emprunts',
				'atarget' => '_self',
				'atitle' => 'Emprunts de matériel !',
				'aico' => '<i class="fas fa-upload"></i>'
			],
		],
		'restitution' => [
			'actions' => 'restitution',
			'vue' => 'barrecode_formulaire_restitution',
			'form' => [
				'active' => true,
				'titre' => 'Restitution de Matériel !',
				'defautcodebarre' => '201899990003540000'
			],
			'menu' => [
				'active' => true,
				'class' => 'restitution',
				'href' => '?actions=',
				'content' => 'Restitution',
				'atarget' => '_self',
				'atitle' => 'Restitution de matériel !',
				'aico' => '<i class="fas fa-download"></i>']
		],
		'retard' => [
			'actions' => 'retard',
			'vue' => 'barrecode_formulaire_retard',
			'form' => [
				'active' => true,
				'titre' => 'Un Retard !',
				'defautcodebarre' => '201899990003540000'
			],
			'menu' => [
				'active' => true,
				'class' => 'presence',
				'href' => '?actions=',
				'content' => 'Retards !',
				'atarget' => '_self',
				'atitle' => 'Retard',
				'aico' => '<i class="fas fa-clock"></i>']
		]
		// 'membre' => [
		// 	'actions' => 'membre',
		// 	'vue' => 'barrecode_formulaire_membre',
		// 	'form' => [
		// 		'active' => true,
		// 		'titre' => 'Info Membre !'
		// 	],
		// 	'menu' => [
		// 		'active' => false,
		// 		'class' => 'membres',
		// 		'href' => '?actions=',
		// 		'content' => 'M',
		// 		'atarget' => '_self',
		// 		'atitle' => 'Membres',
		// 		'aico' => '<i class="fas fa-user"></i>']
		// ]
	];
	
	// SQL 
	// RETARDS ;)
	function maj_MajCommentaireRetardById($id,$commentaire,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($id)){
			if (!empty($_SESSION['user']['LastIdUsed']) AND $_SESSION['user']['LastIdUsed']===$id){
					$retour = Database::queryBindInsert(
						"UPDATE ".TABLES['retards']." SET ".TABLES['retards'].".commentaire = ? WHERE ".TABLES['retards'].".id = ?",
						[[$commentaire,$_SESSION['user']['LastIdUsed']]],
						$callingParent.'( '.__FUNCTION__
					);
			}
		}
		if (isset($_SESSION['user']['LastIdUsed'])) {
			unset($_SESSION['user']['LastIdUsed']);
		}
		return $retour;
	}
	function insert_EnregistrementDuRetardByMembre($membreRow,$callingParent="indéfinie"){
		$retour = false;
		if (isset($membreRow) AND !empty($membreRow)){
			$retour = Database::queryBindInsert(
				"INSERT INTO ".TABLES['retards']." (`id_membres`, `action`, `barrecode`) VALUES (?, 1, ?)",
				[ [$membreRow['membre_id'],$membreRow['barrecode']] ],
				$callingParent.'( '.__FUNCTION__
			);
			$retour = $retour ? Database::get_LastIdUsed(): false;
		}
		return $retour;
	}
	// IN
	function put_rendComputer($barrecode=false,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($barrecode)){
			$retour = Database::queryBindInsert(
				"INSERT INTO ".TABLES['locations']." (`barrecode`, `dateheure`, `date`, `action`, `qui`, `commentaires`)".
					" VALUES (?, '".Fun::dateDuJour('classic')."', '".Fun::dateDuJour('dayfull')."', 'IN', '000000000000000000', 'beta demo')",
				[[$barrecode]],
				$callingParent.'( '.__FUNCTION__
			);
			$retour = $retour ? Database::get_LastIdUsed(): false;
		}
		return $retour;
	}
	// OUT
	function put_empruntComputer($barrecode=false,$qui=false,$callingParent="indéfinie"){
		//	Fun::print_air($qui,'qui:');
		if (!empty($barrecode)){
			$retour = Database::queryBindInsert(
				"INSERT INTO ".TABLES['locations']."(`id`, `barrecode`, `dateheure`, `date`, `action`, `qui`, `commentaires`) VALUES".
				" (NULL, ?, '".Fun::dateDuJour('classic')."', '".Fun::dateDuJour('dayfull')."', 'OUT', ?, 'beta demo')",
				[[$barrecode,$qui]],
				$callingParent.'( '.__FUNCTION__
			);
			$retour = $retour ? Database::get_LastIdUsed(): false;
			return $retour;
		}
		return false;
	}
	// Get datas
	function get_UserInfosByCodebarre($barrecode,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($barrecode)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['membres']." WHERE ".TABLES['membres'].".barrecode = ? LIMIT 1",
				[[$barrecode]],
				$callingParent.'( '.__FUNCTION__
			);
			if(count($retour) > 1){
				$_SESSION['cms']['errors'][] = 'il existe plusieurs comptes utilisateur avec le barrecode '.$barrecode.' ??? dans '.__FUNCTION__."";
			}
			if(count($retour) >= 1){
				// pas plus d'une réponse normalement 
				// log ?!?
				$retour = $retour[0];
			}
		
		}
			return $retour;
	}
	function get_locationByCodeBarre($barrecode=false,$callingParent="indéfinie"){
		$limite = 18;
		$retour = false;
		$action = "OUT";
		if (!empty($barrecode)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations'].
				" WHERE ".TABLES['locations'].".barrecode = ?". // AND ".TABLES['locations'].'.action = ?".
				" ORDER BY ".TABLES['locations'].".dateheure".
				" DESC".
				" LIMIT ?",
				[[$barrecode,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_locations($limite=false,$callingParent="indéfinie"){
		$limite = $limite ?? 3;
		$return = Database::queryBindSelect(
			"SELECT * FROM ".TABLES['locations']." ".
			" ORDER BY ".TABLES['locations'].".dateheure".
			" DESC".
			" LIMIT ?",
			[[$limite]],
			$callingParent.'( '.__FUNCTION__
		);
		return (is_array($return) && count($return)>=1) ? $return : false;
	}
	function get_locationByUserId($id=false,$limite=false,$callingParent="indéfinie"){
		$limite = $limite ?? 3;
		$retour = false;
		$action = "OUT";
		if (!empty($id)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations']." WHERE ".TABLES['locations'].".qui = ?".
				" ORDER BY ".TABLES['locations'].".dateheure".
				" DESC".
				" LIMIT ?", // AND ".TABLES['articles'].".valide = :valide";
				[[$id,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_derniereslocationsByCodeBarre($barrecode=false,$limite=false,$callingParent="indéfinie"){
		$limite = $limite ?? 100;
		$retour = false;
		if ($barrecode){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations'].
				" WHERE ".TABLES['locations'].".barrecode = ?".
				" ORDER BY ".TABLES['locations'].".dateheure".
				" DESC".
				" LIMIT ?",
				[[$barrecode,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return (is_array($retour) && count($retour)>=1) ? $retour : false;
	}
	function get_locationMembreByCodeBarre($barrecode=false,$limite=false,$callingParent="indéfinie"){
		$limite = $limite ?? 100;
		$retour = false;
		$action = "OUT";
		if (!empty($barrecode)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations'].
				" WHERE ".TABLES['locations'].".qui = ?". // AND ".TABLES['locations'].'.action = ?".
				" ORDER BY ".TABLES['locations'].".dateheure".
				" DESC".
				" LIMIT ?",
				[[$barrecode,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return (is_array($retour) && count($retour)>=1) ? $retour : false;
	}
	function get_locationsByCodeBarreAndAction($barrecode=false,$action="IN",$limite=3,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($barrecode)){

			//version ou il faut d'abord entrer (IN) les articles avant de pouvoir les sortir (OUT)
			// $retour = Database::queryBindSelect(
			// 	"SELECT * FROM ".TABLES['locations'].
			// 	" WHERE ".TABLES['locations'].".barrecode = ? AND ".TABLES['locations'].".action = ? ORDER BY ".TABLES['locations'].".dateheure".
			// 	" DESC".
			// 	" LIMIT ?",
			// 	[[$barrecode,$action,$limite]],
			// 	$callingParent.'( '.__FUNCTION__
			// );
			
			//version sans contraintes
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations'].
				" WHERE ".TABLES['locations'].".barrecode = ? ORDER BY ".TABLES['locations'].".dateheure".
				" DESC".
				" LIMIT ?",
				[[$barrecode,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_locationById($id=false,$callingParent="indéfinie"){
		$limite = 18;
		$retour = false;
		$action = "OUT";
		if (!empty($id)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations']." WHERE ".TABLES['locations'].".id = ? ORDER BY ".TABLES['locations'].".dateheure LIMIT ?", // AND ".TABLES['articles'].".valide = :valide";
				[[$id,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_DerniereslocationsById($id=false,$callingParent="indéfinie"){
		$limite = 18;
		$retour = false;
		if (!empty($id)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['locations']." WHERE ".TABLES['locations'].".id = ? ORDER BY ".TABLES['locations'].".dateheure LIMIT ?", // AND ".TABLES['articles'].".valide = :valide";
				[[$id,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_articleByCodebarre($barrecode,$callingParent="indéfinie"){
		$limite = 18;
		$retour = false;
		if (!empty($barrecode)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['articles']." WHERE ".TABLES['articles'].".barrecode = ? ORDER BY ".TABLES['articles'].".id DESC", // AND ".TABLES['articles'].".valide = :valide"
				[[$barrecode]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return (is_array($retour) && count($retour)>=1) ? $retour : false;
	}

	function get_Cootent($table='formdatas_pivot',$inputname='raisonretard',$limit=false,$callingParent="indéfinie"){
		$limit = 1;
		$retour = false;
		$requete = "SELECT ".
			TABLES[$table].".formdatascontent".
			" FROM ".TABLES[$table].
			" WHERE ".TABLES[$table].".parentcolname = ?".
			// " ORDER BY ".TABLES[$table].".datasID DESC".
			" LIMIT ?";
		$binds= [[$inputname,$limit]];
		if (!empty($limit) AND !empty($table) AND !empty($inputname)){
			$retour = Database::queryBindSelect(
				$requete,
				$binds,
				$callingParent.'( '.__FUNCTION__
			);
			if (!empty($retour) AND $retour[0]['formdatascontent']) {
					$retour = json_decode($retour[0]['formdatascontent'], true);
			}
		}
		return $retour;
	}

	function get_DerniersRetards($limit=false,$callingParent="indéfinie"){
		$limit = $limit ? $limit : 5;
		$retour = false;
		if (!empty($limit)){
			$retour = Database::queryBindSelect(
				"SELECT ".
					// TABLES['retards'].".id_membres, ".
					// TABLES['retards'].".id, ".
					TABLES['retards'].".date, ".
					TABLES['membres'].".nom, ".
					TABLES['membres'].".prenom, ".
					// TABLES['membres'].".barrecode as membrebarrecode, ".
					TABLES['membres'].".section, ".
					TABLES['membres'].".annee,".
					TABLES['retards'].".commentaire, ".
					TABLES['retards'].".commentaire2, ".
					TABLES['retards'].".raisonretard, ".
					'TIMEDIFF("'.date("Y-m-d H-i-s").'", '.TABLES['retards'].'.date) as TdV'.
					" FROM ".TABLES['retards'].
					" LEFT JOIN ".TABLES['membres']." ON ".TABLES['retards'].".id_membres = ".TABLES['membres'].".membre_id".
					" ORDER BY ".TABLES['retards'].".date DESC LIMIT ?",
				[[$limit]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function get_RetardsByBarrecode($barrecode,$callingParent="indéfinie"){
		$retour = false;
		$limite = 18;
		if (!empty($barrecode)){
			$retour = Database::queryBindSelect(
				"SELECT date,raisonretard,action,commentaire,commentaire2 FROM ".TABLES['retards']." WHERE ".TABLES['retards'].".barrecode = ? ORDER BY ".TABLES['retards'].".date DESC LIMIT ?",
				[[$barrecode,$limite]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return (is_array($retour) && count($retour)>=1) ? $retour : false;
	}
	function get_ChampsDeFormulairesById($id_page,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($id_page)){
			$retour = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['formdatas']." WHERE ".TABLES['formdatas'].".pageId = ? ORDER BY ".TABLES['formdatas'].".ordre ASC",
				[[$id_page]],
				$callingParent.'( '.__FUNCTION__
			);
		}
		return $retour;
	}
	function select_ChampsDeFormulairesByGroupname($groupename,$callingParent="indéfinie"){
		$retour = false;
		if (!empty($groupename)){
			$retour = Database::queryBindSelect(
				"SELECT *"
				." FROM ".TABLES['formdatas']
				." LEFT JOIN ".TABLES['formdatas_pivot']." ON ".TABLES['formdatas_pivot'].".formdatasid = ".TABLES['formdatas'].".datasid"
				." WHERE ".TABLES['formdatas'].".groupename = ?"
				." ORDER BY ".TABLES['formdatas'].".ordre ASC",
				[[$groupename]],
				$callingParent.'('.__FUNCTION__
			);
		}
		return $retour;
	}

	// HTML BUILDERS FUNCTIONS
	function makeHtml_MessageEnregistrementRetard($sql_User){
		$retour = '';
		$retour = '<div class="ligne enregistrement">';
		$retour .= 'Enregistrement: ' . $sql_User['nom'] . ' ' . $sql_User['prenom'] . ' ['.$sql_User['section'].' / '.$sql_User['annee'].'] Arrivé le ' . $sql_User['arrival']['arrivalDate'] . ' à ' . $sql_User['arrival']['arrivalHour']."<br/>";
		$retour .= "</div>";
		return $retour;
	}
	function makeHtml_listeDerniersRetardsTableVersion($datasindex){ // Les 10 derniers retards enregistrés
		if (is_array($datasindex)){
			//.'(Retards(SelectType';
			if ($datasindex){
				$tableau = '
				<div class="ligne messageretour" title="Message">
					<table>
						<thead>
							<tr>#INTITULES#</tr>
						</thead>
						<tbody>
							#LIGNES#
						</tbody>
						<tfoot>
							<tr>#INTITULESFOOT#</tr>
						</tfoot>
					</table>';
				$intitules = '';
				$lignes = '';
				$boucle = 0;
				$utile=[];
				foreach($datasindex as $key => $value){
					if (is_array($value)){
						$colonnes = '';
						foreach($value as $key2 => $value2)
						{
							if ($key2 === 'raisonretard' AND !isset($utile[$key2]) ){
								$utile[$key2] = get_Cootent('formdatas_pivot',$key2,false,'makeHtml_listeDerniersRetardsTableVersion(Retards(SelectType') ;
							}
							// if ($key2 === 'membrebarrecode' AND !empty($value2)){
							// 	$value2 = '<a href="?actions=membre&codebarre='.$value['membrebarrecode'].'">'.$value2.'</a>';
							// }
							// if ($key2 === 'commentaire' AND !empty($value2)){
							// 	$tmp = '<a href="?actions=retard&retard='.$value['id'].'">'.$value2.'</a>';
							// 	$value2 = Fun::TagMeThis('span', $tmp, 'rgpd');
								
							// }
							// if ($key2 === 'raisonretard' AND !empty($utile[$key2][$value2])){
							// 	$tmp = '<a href="?actions=retard&retard='.$value['id'].'">';
							// 	$tmp .= $utile[$key2][$value2].'</a>';
							// 	$value2 = Fun::TagMeThis('span', $tmp, 'rgpd');
							// }
							$colonnes .= '<td>'.(!empty($value2) ? $value2 : 'null').'</td>';

							if ($boucle === 0) {
								$intitules .= '<td>['.$key2.']</td>';
							}
						}
						$boucle++;
						$class_last = $boucle === 1 ? ' exergue' : '';
						$lignes .= '<tr class="'.$class_last.'">'.$colonnes.'</tr>';
					}
				}
				
				$tableau = str_replace('#INTITULES#', $intitules, $tableau);
				$tableau = str_replace('#INTITULESFOOT#', $intitules, $tableau);
				$tableau = str_replace('#LIGNES#', $lignes, $tableau);
				
				$tableau .= "</div>";
				return $tableau;
			}
		}
		return DICO[602];
	}
	function makeHtml_listeTousLesRetards($datasindex){
		if (is_array($datasindex)){
			$tableau = '';
			// $tableau = '<div class="message">';
			$tableau .= '<div class="ligne messageretour" title="Message">';
			$tableau .= '<div class="actreduce"><i class="fas fa-plus-circle"></i></div>';
			$aujourdhui = Fun::dateDuJour('dayfull');
			$debut = strtotime($aujourdhui);
			$compteur = count($datasindex);
			foreach($datasindex as $key => $value){
				$ladate = strtotime($value['date']);
				$difference = Fun::get_diffDate($debut,$ladate);
				$heuredarrivee = date("H\hi",$ladate);
				$infodate = Fun::dateDuJour('dayfull') === date("Y-m-d",$ladate) ? "Aujourd'hui : " : "Il y'a ".$difference.' jour'. ($difference>0 ? "s" : "").' le ' .date("d-m-Y",$ladate). ' ';
				$tableau .= '<div>'.$infodate. " à ".$heuredarrivee.' ('.$compteur. ($compteur === 1 ? "er" : "ème") . ' retard)</div>';
				$compteur--;
			}
			// $tableau .= "</div>";
			$tableau .= "</div>";
			return $tableau;
		}
		return false;
	}
	// CREATION DE TABLEAU HTML COMMUNS
	function makelistehtml($datasindex,$tablenom="Tableau de données"){
		if (is_array($datasindex)){
			// if ($datasindex){
				$tableau = '
				<div class="ligne messageretour" title="Message">
					<caption>'.$tablenom.'</caption>
					<table class="table">
						<thead class="">
							<tr>#INTITULES#</tr>
						</thead>
						<tbody class="stripped">
							#LIGNES#
						</tbody>
						<tfoot>
							<tr>#INTITULESFOOT#</tr>
						</tfoot>
					</table>';
				$intitules = '';
				$lignes = '';
				$boucle = 0;
				foreach($datasindex as $key => $value){
					if (is_array($value)){
						$colonnes = '';
						foreach($value as $key2 => $value2){
								$colonnes .= '<td>'.(!empty($value2) ? $value2 : 'null').'</td>';
								if ($boucle === 0) {
									$intitules .= '<td>['.$key2.']</td>';
								}
						}
						$boucle++;
						$class_last = $boucle === 1 ? ' exergue' : '';
						$lignes .= '<tr class="'.$class_last.'">'.$colonnes.'</tr>';
					}
				}

				$tableau = str_replace('#INTITULES#', $intitules, $tableau);
				$tableau = str_replace('#INTITULESFOOT#', $intitules, $tableau);
				$tableau = str_replace('#LIGNES#', $lignes, $tableau);
				
				$tableau .= "</div>";
				return $tableau;
			// }
		}
		return 'makelistehtml(vide)';
	}
	function makeMessageEnregistrementRestitutionArticle($sql_Article){
		$retour = '';
		$retour .= '<div class="ligne enregistrement">';			
		$retour .= 'Enregistrement: Nom Article : ' . $sql_Article['nom_article'] . ' #id:' . $sql_Article['id'] . '<br/>';
		$retour .= 'Os : '.$sql_Article['os'].' / Cpumhz'.$sql_Article['cpumhz'].'<br/>';
		$retour .= 'Comment: ' . $sql_Article['comment'] . '' ;
		$retour .= 'Barrecode: ' . $sql_Article['barrecode'] . ' / Valide:' . $sql_Article['valide'] . '' ;
		$retour .= "</div>";
		return $retour;
	}


	// TRAITEMENTS PAGE
	if (self::$_pageCurrent === 'actions'){
		foreach($listeDesActions as $key => $value){
			// actions menu
			if ($value['menu']['active']) {
				$nav_menu_buttons .= '<div class="navaction '.$value['menu']['class'].'">';
				$nav_menu_buttons .= '<a href="'.$value['menu']['href'].$key.'" title="'.$value['menu']['atitle'].'" target="'.$value['menu']['atarget'].'">';
				$nav_menu_buttons .= $value['menu']['content'].' ';
				$nav_menu_buttons .= $value['menu']['aico'].' ';
				$nav_menu_buttons .= '</a>';
				$nav_menu_buttons .= '</div>';
			}
			// actions possibles menu
			if ($value['form']['active']) {
				$actionsPossibles[] = $key;
			}
		}		
		$modelVue = str_replace("#CONTENTS#", $nav_menu_buttons, $modelVue);
		
		if (isset($_GET['actions'])){// SI _GET['actions']
			if (!empty($_GET['actions'])){
				if (in_array(Fun::get_clean($_GET['actions']) , $actionsPossibles)){
					$actionDemande = Fun::get_clean($_GET['actions']);
				}
			}
		} // FiN SI _GET['actions']

		Page::$_actionDatas = $listeDesActions[$actionDemande];

		$fileandpath = ROOTS['controllers'].'actions/C_'.$actionDemande.'.php';
		if (file_exists($fileandpath)){
			$_SESSION['cms']['include'][] = 'include_once:'.$fileandpath;
			include_once($fileandpath);
		}else{$_SESSION['cms']['errors'][] = 'cant include_once:'.$fileandpath;}


	} // FiN SI _pageCurrent === 'actions'
	
	$currentVue = str_replace("#ACTIONS#", Page::$_actionDatas['htmlvue'], $modelVue);
?>