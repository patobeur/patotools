<?php
trait t_Formulary {
	public function get_FormByGroupeName($table,$sqltype,$groupename,$contentidx=false){
		$groupevue = $this->getfilecontent( ROOTS['traits']."/vues/"."formulary.php");
		$groupeinputs = select_ChampsDeFormulairesByGroupname($groupename,'Formulary('.__FUNCTION__);
		// $groupeinputs = select_ChampsDeFormulairesByGroupname2($groupename,'Formulary('.__FUNCTION__);
		$templateReplacement = [
			'#GROUPENAME#' => $groupename
			,'#TITRE#' => "un commentaire sur le retard ?"// de ".$contentidx['nom']." ".$contentidx['prenom'].". ?"
			,'#HIDDENACTION#' => $_SESSION['user']['LastIdUsed']
			,'#HIDDENACTIONTYPE#' => Page::$_actionDatas['actions']
			,'#SUBMITCONTENT#' => DICO['200']
			,'#CSSCLASSNAME#' => "message"
			,'#ACTIONFORM#' => "?actions=".Page::$_actionDatas['actions']
			,'#INPUTSLIST#' => $this->HydrateForm($table,$sqltype,$groupename,$groupeinputs)
		];
		// REMPLISSAGE DU FORM
		$groupevue = Fun::str_aireplace($groupevue,$templateReplacement);
		return $groupevue;
	}
	// FORMS ---------------------------------------------------------------------
	// FORMS POSTED TOOLS --------------------------------------------------------
	public function get_CleanIntValueIfPosted($postname){
		if ( isset($_POST[$postname]) ){
				$cleanvaleur = intval(Fun::get_clean($_POST[$postname]));
			if ( !empty($cleanvaleur) OR $cleanvaleur===0){
				return $cleanvaleur;
			}
			else {
				return 0;
			}
		}
		return false;
	}
	public function get_CleanStringValueIfPosted($postname){
		if ( isset($_POST[$postname]) ){
			if ( !empty($_POST[$postname] ) ){
				$cleanvaleur = Fun::get_clean($_POST[$postname]);
				//	print_air($cleanvaleur,$postname.'');
				return $cleanvaleur;
			}
			else {
				return null;
			}
		}
		return null;
	}
	// FORMS DATAS TOOLS ---------------------------------------------------------
	public function HydrateForm($table,$sqltype,$groupename,$groupeinputs){
		// $groupsseinputs = select_ChampsDeFormulairesByGroupname('retardcommentaire');
		// ALTER TABLE `bc_retards` ADD `commentaire2` TEXT NULL DEFAULT NULL AFTER `commentaire`;
		$return = false;
		if (!empty($groupeinputs) AND is_array($groupeinputs)){
			$return = [];
			$requestSql = [];
			foreach($groupeinputs as $key => $values){
				switch($values['type']){
					case 'select':
						$bloc = $this->deal_FormSelect($values,false);
						$return[$values['ordre']] = $bloc['html'];
						$requestSql[] =  $bloc['sqldatas'];
					break;
					case 'textearea':
						$bloc = $this->deal_FormTextarea($values,false);
						$return[$values['ordre']] = $bloc['html'];
						$requestSql[] =  $bloc['sqldatas'];
					break;
					default:
					break;
				}
			}
			$return = count($return)>0 ? $this->make_InputsHtml($return,$groupeinputs) : false;
			// SQL CREATOR
			if (!empty($requestSql) AND count($requestSql)>0){
				$nb_for = 0;
				$nb_valide = 0;
				$reqSql = '';
				$reqSqlBinds = [];
				foreach($requestSql as $key => $value){
					$reqSql .= $nb_for > 0 ? ", " : '';
					$reqSql .= $value['col']."=?";
					$reqSqlBinds[] = $value['bind'] ? $value['bind'] : null;
					$value['valide'] ? $nb_valide++ : '';
					$nb_for++;
				}
				// si nombre de champs valide ==== nombre de champs
				if ($nb_valide === $nb_for) {
					switch($sqltype){
						case 'UPDATE':
							if (!empty($_SESSION['user']['LastIdUsed'])) {
								$reqSql = "UPDATE ".$table. " SET ". $reqSql." WHERE id = ?";
								$reqSqlBinds[] = $_SESSION['user']['LastIdUsed'];
							}
						break;
					}
					// INSERT
					if (!empty($_POST[$groupename])){
						$retour = Database::queryBindInsert(
							$reqSql,
							[$reqSqlBinds],
							'Formulary( '.__FUNCTION__
						);
					}
				}
			}
		}
		return $return;
	}
	// FORMS TOOLS HTMLizer -------------------------------------------------
	public function deal_FormSelect($values){
		$return= false;
		if (!empty($values) AND is_array($values)){
			$valeurPosted = $this->get_CleanIntValueIfPosted($values['inputname']); 
			$listeselect = json_decode($values['formdatascontent'], true);
			if (is_array($listeselect)){
				$return = intval($values['islabel']) === 1 ? '
				<label for="'.$values['inputname'].'">'.$values['inputtitle'].'</label>' :'' ;
				$return .= '
				<select id="'.$values['inputname'].'" name="'.$values['inputname'].'">';

				foreach($listeselect as $num => $content){
					$selected = (($valeurPosted AND $valeurPosted === $num) OR (!$valeurPosted AND intval($values['pardefaut']) === $num)) ? ' selected' : '';
					$return .= '
					<option value="'.$num.'"'.$selected.'>'.$num.'- '.$content.'</option>';
				}

				$return .= '
				</select>'.PHP_EOL;
				$return = [
					'html' => $return
					,'sqldatas' => [
						'col' => $values['inputname']
						,'bind' => $valeurPosted ? $valeurPosted : ''
						,'notnull' => $values['isnotnull']
						,'valide' => ( intval( $values['isnotnull'] ) === 0 ) OR ( intval( $values['isnotnull'] ) === 1 ) AND ( !empty($valeurPosted) ) 
					]
				];
			}
		}
		return $return;
	}
	public function deal_FormTextarea($values,$news=false){
		$return= false;
		if (!empty($values) AND is_array($values)){
			
			$valeurPosted = $this->get_CleanStringValueIfPosted($values['inputname']); 

			$return = intval($values['islabel']) === 1 ? '
			<label for="'.$values['inputname'].'">'.$values['inputtitle'].'</label>' :'' ;
			$return .= '
			<textarea id="'.$values['inputname'].'"';
				$return .= ' name="'.$values['inputname'].'"';
				$return .= ' class="comments shadows"';
				if(!empty($values['placeholder'])){
					$return .= ' placeholder="'.$values['placeholder'].'"';
					$return .= ' onfocus="this.placeholder = \'\'"';
					$return .= ' onblur="this.placeholder = \''.$values['placeholder'].'\'"';
				}
			$return .= '>';
			$return .= $valeurPosted ? $valeurPosted : '';
			$return .= '</textarea>';

			$valide = ( intval( $values['isnotnull'] ) === 0 ) OR ( intval( $values['isnotnull'] ) === 1 ) AND ( !empty($valeurPosted) );

			$return = [
				'html' => $return
				,'sqldatas' => [
					'col' => $values['inputname']
					,'bind' => $valeurPosted ? $valeurPosted : ''
					,'notnull' => $values['isnotnull']
					,'valide' => $valide
				]
			];
		}
		return $return;
	}
	// FORMS TOOLS HTML CONCATENER ------------------------------------------
	public function make_InputsHtml($array,$groupeinputs){
		$html = '';
		$compteur = 0;
		foreach($array as $htmlbloc){
			$html .= '<div id="inp_'.$compteur.'" >'.$htmlbloc."</div>";
			++$compteur;
		}
		return $html;
	}
}
