<?php
	self::$_actionDatas = self::$_actionDatas;

	$template = [
		'#TITREACTIONS#'=> self::$_actionDatas['form']['titre'],	// le titre h2
		'#ACTIONFORM#'=> "?actions=".self::$_actionDatas['actions'], // Adresse Action du formulaire
		'#ACTIONTYPE#'=> self::$_actionDatas['actions'],
		'#INFOS#'=> '',
		'#CODEBARRE#'=> '201899990003540000', // contenu du champs de recherche
		'#CSSALERT#'=> ' retard',
		'#MESSAGE#'=> '',
		'formcommentaires'=> '',
		'toptenretards'=> '',
		'#CERETARD#'=> '',
		'#TITREH2#' => 'Un commentaire à ajouter ?'
	];


	if ($_GET){ // SI _POST
		if ( !empty($_GET['actions']) AND !empty($_GET['codebarre'])){
			$actionPoste = Fun::get_clean($_GET['actions']);
			$codebarre = Fun::get_clean($_GET['codebarre']);
			$membre = false;
			$template['#CODEBARRE#'] = $codebarre;

			if (in_array($actionPoste , $actionsPossibles) AND $actionPoste === $actionDemande){

				$sql_Membre = get_UserInfosByCodebarre($codebarre);
				

				if ($sql_Membre) {
					$tousLesRetards = get_RetardsByBarrecode($codebarre);
					$template['#MESSAGE#'] = makeHtml_listeTousLesRetards($tousLesRetards,$sql_Membre);
					$template['#CSSALERT#'] = ' good';
					$template['formcommentaires'] = ' good';
				}
				else {
					$template['#readyState#'] = '';
					$template['#CSSALERT#'] = ' alerte';
					$template['#INFOS#'] = 'Ce codebarre n\'existe pas.' ;
					$template['formcommentaires'] = ' good';
				}
			}
		}
	} // FIN SI _POST

	if ($_POST){ // SI _POST
		if ( !empty($_POST['action'])){
		
			if (!empty($_POST['codebarre'])){
				$actionPoste = Fun::get_clean($_POST['action']);
				$codebarre = Fun::get_clean($_POST['codebarre']);
				$membre = false;
	
				if (in_array($actionPoste , $actionsPossibles) AND $actionPoste === $actionDemande){
	
					$sql_Membre = Get_UserInfosByCodebarre($codebarre);
					if ($sql_Membre) {
					// 	// $template['#CERETARD#'] = put_EnregistrementDuRetardByMembre($sql_Membre); // retourn l'id de l'enregistrement
						//$template['#INFOS#'] = makeMessageEnregistrementRetard($sql_Membre);
	
						$tousLesRetards = get_RetardsByBarrecode($codebarre);
						$template['#MESSAGE#'] = makelisteTousLesRetards($tousLesRetards,$sql_Membre);
					// 	$template['#CSSALERT#'] = ' good';
					// 	$template['formcommentaires'] = $this->getfilecontent( ROOTS['vues']."actions/commentaires/commentaires.php");
					}
					else {
					// 	$template['#CSSALERT#'] = ' alerte';
					// 	$template['#INFOS#'] = 'Ce codebarre n\'existe pas.' ;
					}
				}
			}
			//commenteires
			if (!empty($_SESSION['user']['LastIdUsed']) AND !empty($_POST['commentaire'])){
				$postedcoment = Fun::get_clean($_POST['commentaire']);
				//	Fun::print_air($_SESSION['user']['LastIdUsed'],'posted LastIdUsed');
				//	Fun::print_air($postedcoment,'postedcoment');
				$resultcoment = put_MajCommentaireRetardById($_SESSION['user']['LastIdUsed'],$postedcoment);
			}
		}
	} // FIN SI _POST


	$currentVueBarrecode = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_actionDatas['vue'].".php");
	// $currentVueBarrecode = $this->getfilecontent( ROOTS['vues'].self::$_pageCurrent."/".self::$_actionDatas['vue'].".php");
	$currentVueBarrecode = str_replace("#CSSALERT#", $template['#CSSALERT#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#TITREACTIONS#", $template['#TITREACTIONS#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONFORM#", $template['#ACTIONFORM#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONTYPE#", $template['#ACTIONTYPE#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#CODEBARRE#", $template['#CODEBARRE#'], $currentVueBarrecode);

	$message = '';
	if (!empty($template['#MESSAGE#']) OR !empty($template['#INFOS#'])){
		$message = '
			<div class="form-page">
				<div class="message">
					'.$template['#INFOS#'].'
					<h2>Les dix derniers retards !</h2>
					'.$template['#MESSAGE#'].'
				</div>
			</div>';
	}

	$commentaireadditif = "";
	// if (!empty($template['formcommentaires'])){
	// 	$commentaireadditif = '
	// 		<div class="form-page">
	// 			<div class="commentaire">
	// 				'.$template['formcommentaires'].'
	// 			</div>
	// 		</div>';
	// 		$commentaireadditif = str_replace("#TITREH2#", "un commentaire sur le retard de ".$sql_Membre['nom']." ".$sql_Membre['prenom'].". ?", $commentaireadditif);
	// 		$commentaireadditif = str_replace("#CERETARD#", $template['#CERETARD#'], $commentaireadditif);
	// 		$commentaireadditif = str_replace("#ACTIONFORM#", $template['#ACTIONFORM#'], $commentaireadditif);
	// 		$commentaireadditif = str_replace("#ACTIONTYPE#", $template['#ACTIONTYPE#'], $commentaireadditif);
	// }
	$messageTopTen = "";
	// $toplastten = get_DerniersRetards(20);
	// $template['toptenretards'] = Page::makelisteHtmlDerniersRetards($toplastten);
	// $messageTopTen = '<div class="form-page">
	// 	<div class="message">
	// 		<div class="titre">Les 10 derniers retards enregistrés</div>
	// 			'.$template['toptenretards'].'
	// 	</div>
	// 	</div>';

	self::$_actionDatas['htmlvue'] = str_replace("#MESSAGE#", $message.$commentaireadditif.$messageTopTen, $currentVueBarrecode);
