<?php
	$template = [
		'#TITREACTIONS#'=> Page::$_actionDatas['form']['titre'],
		'#ACTIONFORM#'=> "?actions=".Page::$_actionDatas['actions'],
		'#ACTIONTYPE#'=> Page::$_actionDatas['actions'],
		'#INFOS#'=> '',
		'#CODEBARRE#'=> '',
		'#CSSALERT#'=> '',
		'#MESSAGE#'=> '',
	];

	if ($_POST){ // SI _POST
		if ( !empty($_POST['action']) AND !empty($_POST['codebarre'])){
			$actionPoste = Fun::get_clean($_POST['action']);
			$codebarre = Fun::get_clean($_POST['codebarre']);

			if (in_array($actionPoste , $actionsPossibles) AND $actionPoste === $actionDemande){
				
				$sql_Articles = get_articleByCodebarre($codebarre);

				if ($sql_Articles) { // si le codebarre existe (single ou multiple)

					put_rendComputer($codebarre,'put_rendComputer'); 
					$dernieresDatas = get_locationByCodeBarre($codebarre);
					$sqlresponse = $sql_Articles;
					
					$sql_Article = ( count($sql_Articles) > 0 ) ? $sql_Articles[0] : $sql_Articles[0];

					// ------------------
					// GLPI CHANTIER -----

					//$glpiTouch = !empty($_SESSION['user']['player']['glpi']) ? new RequestAPI() : false;
					//$glpiTouch = false;
					
					// GLPI CHANTIER -----
					// ------------------
				
					// on prend la 1ere si il y a plus d'une réponse
					$choix = 0; // le premier
					// if (count($sql_Articles) > 1) {
					// 	$choix = 0; // le premier sinon count($sql_Articles)-1; pour le dernier
					// }
					// [id]	[barrecode]	[nom_article]	[os]	[cpumhz]	[comment]	[valide]

					$template['#INFOS#'] = makeMessageEnregistrementRestitutionArticle($sql_Article);

					$template['#MESSAGE#'] = '<div class="message">'.
						Fun::sqlToTable($sql_Articles,"L'article en question !").
						'</div><div class="ligne messageretour" title="Message">'.
						Fun::sqlToTable($dernieresDatas,"Les derniers mouvements d'article").
					"</div>";

					$template['#CSSALERT#'] = ' good';
				}
				else {
					$template['#readyState#'] = '';
					$template['#CSSALERT#'] = ' alerte';
					$template['#INFOS#'] = '<i class="fas fa-exclamation-triangle"></i>Ce codebarre n\'existe pas.' ;
				}
			}
		}
	} // FIN SI _POST

	$currentVueBarrecode = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_actionDatas['vue'].".php");
	// $currentVueBarrecode = $this->getfilecontent( ROOTS['vues'].Page::$_pageCurrent."/".Page::$_actionDatas['vue'].".php");
	$currentVueBarrecode = str_replace("#CSSALERT#", $template['#CSSALERT#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#TITREACTIONS#", $template['#TITREACTIONS#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONFORM#", $template['#ACTIONFORM#'], $currentVueBarrecode);
	$currentVueBarrecode = str_replace("#ACTIONTYPE#", $template['#ACTIONTYPE#'], $currentVueBarrecode);
	// $currentVueBarrecode = str_replace("#readyState#", $template['#readyState#'], $currentVueBarrecode);		
	$currentVueBarrecode = str_replace("#CODEBARRE#", $template['#CODEBARRE#'], $currentVueBarrecode);
	$message = '';
	if (!empty($template['#MESSAGE#']) OR !empty($template['#INFOS#']))
	{
		$message = '<div class="form-page">
			<div class="message">
					'.$template['#INFOS#'].'
					'.$template['#MESSAGE#'].'
			</div>
			</div>';
	}
	$currentVueBarrecode = str_replace("#MESSAGE#", $message, $currentVueBarrecode);
	Page::$_actionDatas['htmlvue'] = $currentVueBarrecode;
