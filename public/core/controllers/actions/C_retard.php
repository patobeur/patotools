<?php
	$listeDesActions = self::$_actionDatas;
	$commentaireActif = true; // a deplacer dans les definitions ??
	$template = [
		'#TITREACTIONS#'=> self::$_actionDatas['form']['titre'],	// le titre h2
		'#ACTIONFORM#'=> "?actions=".self::$_actionDatas['actions'], // Adresse Action du formulaire
		'#ACTIONTYPE#'=> self::$_actionDatas['actions'],

		'#ACTIONRETARD#'=> '',
		'#INFOS#'=> '', // affichage des resultats de l'action
		'#CODEBARRE#'=> '', // contenu du champs de recherche
		'#CSSALERT#'=> ' ',
		'#MESSAGE#'=> '',
		'toptenretards'=> '',
		//--
		'formcommentaires2'=> '',
		'#HIDDENACTIONTYPE#'=> self::$_actionDatas['actions'],
		'#HIDDENACTIONRETARD#'=> '',
		'#TITREH2#' => 'Un commentaire à ajouter ?',
		'#SUBMITCONTENT#' => DICO['200'],
		'select1' => ['panne reveil',]
	];
	$commentaireadditif2 = '';
	$sql_Membre = false;
	if ($_POST){ // SI _POST
		if ( !empty($_POST['action'])){
			$actionPoste = Fun::get_clean($_POST['action']);

			if (!empty($_POST['codebarre'])){
				$barrecode = Fun::get_clean($_POST['codebarre']);
				$membre = false;
	
				if (in_array($actionPoste , $actionsPossibles) AND $actionPoste === $actionDemande){
	
					$sql_Membre = get_UserInfosByCodebarre($barrecode,self::$_pageCurrent.'(Retards');
					if ($sql_Membre) {
						$sql_Membre['arrival'] = [
							'arrivalDate' => Fun::dateDuJour('arrivalDate'),// date('d/m/Y', time()),
							'arrivalHour' => Fun::dateDuJour('arrivalHour'),// date('H:i:s', time()),
							'timestamp' => Fun::dateDuJour('timestamp'),// date('d/m/Y H:i:s', time()),
						];

						$template['#ACTIONRETARD#'] = insert_EnregistrementDuRetardByMembre($sql_Membre,self::$_pageCurrent.'(Retards'); // retourne l'id de l'enregistrement
						$template['#INFOS#'] = makeHtml_MessageEnregistrementRetard($sql_Membre);
						
						$tousLesRetards = get_RetardsByBarrecode($barrecode,self::$_pageCurrent.'(Retards');
						$template['#MESSAGE#'] = makeHtml_listeTousLesRetards($tousLesRetards);
						$template['#CSSALERT#'] = ' good';

						// ajout d'un formulaire de commentaires après un post réussi !
						$template['formcommentaires'] = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/commentaires.php");
					}
					else {
						$template['#CSSALERT#'] = ' alerte';
						$template['#INFOS#'] = 'Ce codebarre n\'existe pas.' ;
					}
				}
			}
			
			// groupe vue
			// Affichage d'un formulaire
			// gestion par groupename
			if ($commentaireActif && !empty($_SESSION['user']['LastIdUsed']) AND $actionPoste === $actionDemande){
				//	Fun::print_air($sql_Membre);
				$extraFORM = $this->get_FormByGroupeName(
					TABLES['retards'] 							// nom de la table -> récupéré tels quel
					,'UPDATE' 									// nom du type de l'action SQL -> récupéré tels quel
					,'retardcommentaire' 						// nom de groupe du formulaire -> récupéré tels quel
					// ,[											// données utiles -> un peu en fonction des besoins pour l'instant
					// 	'nom' => $sql_Membre['nom']
					// 	,'prenom' => $sql_Membre['prenom']
					// ]
				);
			}





		}
	} // FIN SI _POST

	$currentVueBarrecode = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_actionDatas['vue'].".php");

	$currentVueBarrecode = str_replace("#CSSALERT#", $template['#CSSALERT#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#TITREACTIONS#", $template['#TITREACTIONS#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONFORM#", $template['#ACTIONFORM#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONTYPE#", $template['#ACTIONTYPE#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#CODEBARRE#", $template['#CODEBARRE#'], $currentVueBarrecode);

	$messageValidation = '';
	if (!empty($template['#MESSAGE#']) OR !empty($template['#INFOS#'])){
		$messageValidation = '
			<div class="form-page">
				<div class="message">
					'.$template['#INFOS#'].'
					'.$template['#MESSAGE#'].'
				</div>
			</div>';
	}
	$toplastten = get_DerniersRetards(18,self::$_pageCurrent.'(Retards');
	// Les 10 derniers retards enregistrés

	
	// $template['toptenretards'] = makeHtml_listeDerniersRetardsTableVersion($toplastten);
	$template['toptenretards'] = Fun::sqlToTable($toplastten,"Les 10 derniers retards enregistrés");
	// $messageTopTen = '
	// <div class="form-page">
	// 	<div class="message">
	// 		<div class="titre">Les 10 derniers retards enregistrés</div>
	// 		'.$template['toptenretards'].'
	// 	</div>
	// </div>';
	$messageTopTen = $template['toptenretards'];
	self::$_actionDatas['htmlvue'] = str_replace("#MESSAGE#", $messageValidation.($extraFORM ?? '').$messageTopTen, $currentVueBarrecode);
