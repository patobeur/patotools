<?php
	$currentVueBarrecode = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_actionDatas['vue'].".php");
	$vueFormInput = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/form_input.php");
	$vueAideHelp = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/form_aidehelp.php");
	$vueCardReponse = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/form_cardreponse.php");

	$template = [
		'#TITREACTIONS#'=> Page::$_actionDatas['form']['titre'],
		'#ACTIONFORM#'=> "?actions=".Page::$_actionDatas['actions'],
		'#ACTIONTYPE#'=> Page::$_actionDatas['actions'],
		// '#INFOS#'=> '',
		// '#readyState#'=> '',
		'#INPUT_USER#'=> '',
		'#INPUT_ARTICLE#'=> '',
		'#CODEBARRE_USER#'=> '',
		'#CODEBARRE_ARTICLE#'=> '',
		'#CODEBARRE_USER#'=> '',
		'#CODEBARRE_ARTICLE#'=> '',
		'#CSSALERT#'=> '',
		'#MESSAGE#'=> '',
		'#FOCUS_USER#'=> ' autofocus',
		'#FOCUS_ARTICLE#'=> '',
		'#YES#'=> '',
		'#ENSTOCK#'=> '',
		'#AIDE-RETARDS#'=> '',
		'#AIDE-HELP#'=> '',
	];


	if ($_POST){ // SI _POST

		if (!isset($_SESSION['emprunt'])){
			$_SESSION['emprunt'] = [
				'user' => false,
				'article' => false
			];
		}
		if ( !empty($_POST['action'])){
			$actionPoste = Fun::get_clean($_POST['action']);

			if (in_array($actionPoste , $actionsPossibles) AND $actionPoste === $actionDemande){

				// si on a un codebarre
				if (!empty($_POST['codebarre_user'])){
					// (le check de la longueur du codebarre est inutile  ils n'ont pas la même length) ???
					$codebarre_user = Fun::get_clean($_POST['codebarre_user']);

					$sql_Membre = get_UserInfosByCodebarre($codebarre_user,self::$_pageCurrent.'(Emprunts'); // check si le membre existe

					if ($sql_Membre){
						$_SESSION[$actionDemande]['user'] = $sql_Membre; // on stock en session le membre trouvé
						
						$template['#FOCUS_USER#'] = ''; // en enleve le focus html de USER
						$template['#FOCUS_ARTICLE#'] = ' autofocus'; // en met le focus html sur ARTICLE

					
						// // affichage des derniers retards par user-barrecode ! 
						// $list_tousLesRetards = get_RetardsByBarrecode($_SESSION[$actionDemande]['user']['barrecode'],self::$_pageCurrent.'(Retards');
						
						// if (empty($list_tousLesRetards)){
						// 	$html_tousLesRetards = '<span>Aucun retard trouvé ! C\'est bien une première visite !</br>Bienvenu(e) parmis nous !</br>Veuillez faire scanner le codebarre de l\'article que vous souhaitez emprunter !</span>';
						// }
						// else {
						// 	$html_tousLesRetards = makelistehtml($list_tousLesRetards,'Derniers retards de '.$sql_Membre['nom'].' '.$sql_Membre['prenom'].' ('.$sql_Membre['section'].' / '.$sql_Membre['annee'].')');
						// }
					}

				}
				else {
					// n'existe pas ou vide
					$template['#CSSALERT#'] = ' alerte';
					if (isset($_SESSION['emprunt'])){
						unset($_SESSION['emprunt']);
					}
					unset($_POST['codebarre_user']);
				}

				//champ ARTICLE
				if (isset($_SESSION[$actionDemande]['user']['barrecode']) && !empty($_POST['codebarre_article'])){ // si article posted et user en session temporaire ok
					$codebarre_article = Fun::get_clean($_POST['codebarre_article']);
							
						$sql_Article = get_articleByCodebarre($codebarre_article); // check si article existe
						if ($sql_Article){
							//$sql_get_locations = get_locations($_SESSION[$actionDemande]['article']['barrecode'],1,'get_locations');
							//$isarticleIn = (sql_get_locations[0]['action']==='IN'); le dernier mouvement etait il une entree 'IN'
							// if ($isarticleIn){
								$_SESSION[$actionDemande]['article'] = $sql_Article[0]; // on stock le premier article trouvé
								$insert_emprunt = put_empruntComputer(
									$_SESSION[$actionDemande]['article']['barrecode'],
									$_SESSION[$actionDemande]['user']['barrecode'],
									"Emprunt"
								);
							// }



						}
						else {
							// n'existe pas ou vide
							$template['#CSSALERT#'] = ' alerte';
							if (isset($_SESSION['emprunt'])){
								unset($_SESSION['emprunt']);
							}
							$ErreurOrdre = Fun::sqlToTable([['Article'=>$codebarre_article,'Erreur'=>"n'existe pas !!!..."]]);
						}
				}


				if (empty($_POST['codebarre_user'])) {
					// n'existe pas ou vide
					// $template['#CSSALERT#'] = ' alerte';
					$user_alerte = " alerte";
					if (isset($_SESSION['emprunt'])){
						unset($_SESSION['emprunt']);
					}
					$ErreurOrdre = Fun::sqlToTable([['Utilisateur'=>"?????",'Erreur'=>"Scannez le barrecode de l'étudiant !!!"]]);
				}
				
			}
		}
	}
	// si emprunt ok alors on vide la session
	if (isset($insert_emprunt)){
		$template['#MESSAGE#'] .= str_replace("#NOM#",$_SESSION[$actionDemande]['user']['nom'],$vueCardReponse);
		unset($_SESSION['emprunt']);
	}


	if (!$_POST AND isset($_SESSION['emprunt'])){
		unset($_SESSION['emprunt']);
	}

	if (isset($ErreurOrdre)){
		$template['#MESSAGE#'] .= '
			<div class="form-page">
				<div class="message alerte">'.$ErreurOrdre.'</div>
			</div>';
	}

					
	
	if (isset($_SESSION[$actionDemande]['user']['barrecode']) && !isset($_SESSION[$actionDemande]['article']['barrecode']) ){

		$get_locationMembreByCodeBarre = get_locationMembreByCodeBarre($_SESSION[$actionDemande]['user']['barrecode'],5,'get_locationMembreByCodeBarre');
		if ($get_locationMembreByCodeBarre){
			$html_locationMembreByCodeBarre = Fun::sqlToTable($get_locationMembreByCodeBarre,"Les derniers transactions de ".$_SESSION[$actionDemande]['user']['nom']. " ".$_SESSION[$actionDemande]['user']['prenom'].' ('.$_SESSION[$actionDemande]['user']['section'].") [".$_SESSION[$actionDemande]['user']['annee'].']'  );
		}
		else {
			$html_locationMembreByCodeBarre = '<span>Aucunnes transactions trouvées ! Est-ce une première visite ?</span><span>Je me permet de chercher votre codebarre dans les retards !</span>';
		}

		$template['#MESSAGE#'] .= $html_locationMembreByCodeBarre;

		if (!$get_locationMembreByCodeBarre){
			// // affichage des derniers retards par user-barrecode ! 
			$list_tousLesRetards = get_RetardsByBarrecode($_SESSION[$actionDemande]['user']['barrecode'],self::$_pageCurrent.'(Retards');
			$html_tousLesRetards = $list_tousLesRetards
			? Fun::sqlToTable($list_tousLesRetards,'Derniers retards de '.$_SESSION[$actionDemande]['user']['nom'].' '.$_SESSION[$actionDemande]['user']['prenom'].' ('.$sql_Membre['section'].' / '.$sql_Membre['annee'].')')
			: '<span>Aucun retard trouvé ! C\'est bien une première visite !</br>Bienvenu(e) parmis nous !</br>Veuillez faire scanner le codebarre de l\'article que vous souhaitez emprunter !</span>';
			
			$template['#MESSAGE#'] .= $html_tousLesRetards;

		}
	}

	$html_dernieresLocations = get_locations(20,'page emprunts');
	if ($html_dernieresLocations){
		$template['#MESSAGE#'] .= Fun::sqlToTable($html_dernieresLocations,"Les derniers mouvements d'article");
	}


	// USER Member
	$input_user = str_replace("#ITEM#", 'CODEBARRE_USER', $vueFormInput);
	$input_user = str_replace("#INPUTNAME#", 'codebarre_user', $input_user);
	$input_user = str_replace("#AWESOMEINPUT#", 'user', $input_user);

	$input_user = str_replace("#PLACEHOLDER#", 'Codebarre de l utilisateur', $input_user);
	$input_user = str_replace("#TITLEINPUT#", 'Ici Renseignez le CodeBarre de l\'utilisateur!', $input_user);
	$input_user = str_replace("#AUTOFOCUS#", $template['#FOCUS_USER#'], $input_user);
	if (isset($_SESSION[$actionDemande]['user']['barrecode']) AND !empty($_SESSION[$actionDemande]['user']['barrecode'])){
		// je met le champ à jour
		$input_user = str_replace("#CODEBARRE_USER#", $_SESSION[$actionDemande]['user']['barrecode'], $input_user);
	}
	$template['#INPUT_USER#'] = $input_user;	

	// ARTICLE Computer
	$input_article = str_replace("#ITEM#", 'CODEBARRE_ARTICLE', $vueFormInput);	
	$input_article = str_replace("#INPUTNAME#", 'codebarre_article', $input_article);
	$input_article = str_replace("#AWESOMEINPUT#", 'laptop', $input_article);

	$input_article = str_replace("#PLACEHOLDER#", 'Codebarre de l article emprunté !', $input_article);
	$input_article = str_replace("#TITLEINPUT#", 'Ici Renseignez le CodeBarre de l\'article emprunté !', $input_article);
	$input_article = str_replace("#AUTOFOCUS#", $template['#FOCUS_ARTICLE#'], $input_article);

	if (isset($_SESSION[$actionDemande]['article']['barrecode']) AND !empty($_SESSION[$actionDemande]['article']['barrecode'])){
		// je met le champ à jour
		$input_article = str_replace("#CODEBARRE_ARTICLE#", $_SESSION[$actionDemande]['article']['barrecode'], $input_article);
	}
	$template['#INPUT_ARTICLE#'] = $input_article;

	$currentVueBarrecode = str_replace("#INPUT_USER#", $template['#INPUT_USER#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#INPUT_ARTICLE#", $template['#INPUT_ARTICLE#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#AIDE-HELP#", $template['#AIDE-HELP#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#MESSAGE#", $template['#MESSAGE#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#CSSALERT#", $template['#CSSALERT#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#TITREACTIONS#", $template['#TITREACTIONS#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONFORM#", $template['#ACTIONFORM#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONTYPE#", $template['#ACTIONTYPE#'], $currentVueBarrecode);
	// $currentVueBarrecode = str_replace("#INFOS#", $template['#INFOS#'], $currentVueBarrecode);		
	// $currentVueBarrecode = str_replace("#readyState#", $template['#readyState#'], $currentVueBarrecode);	
	$currentVueBarrecode = str_replace("#CODEBARRE_USER#", $template['#CODEBARRE_USER#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#CODEBARRE_ARTICLE#", $template['#CODEBARRE_ARTICLE#'], $currentVueBarrecode);


	Page::$_actionDatas['htmlvue'] = $currentVueBarrecode;


