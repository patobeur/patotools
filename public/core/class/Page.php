<?php
	class Page extends User {
		use t_PageTracer;
		use t_Formulary;
		public $pageMain = 'main';
		public static $_pageCurrent = '';
		public $pagesPossibles = [];
		public $_getDatas;
		static $_actionDatas;
		private $_lesPages = [];
		private $_lesPagesSQL = [];
		private $pageHtml = "";
		/**
		 * Construct de la class 'Page'
		 *  
		 */
		public function __construct(){
			if(Database::get_checkBdd()){
				self::$_actionDatas = false;
				$this->pageHtml = $this->getfilecontent( ROOTS['vues'].$this->pageMain.".php");
	
				// definition des pages
				$this->_lesPagesSQL = Database::get_PagesList('page',0,__CLASS__.'('.__FUNCTION__);
	
				//check conditions d'affichage
				//generation du menu
				if (!empty($this->_lesPagesSQL)){ // $pageList
					$nouvelleliste = [];
					foreach($this->_lesPagesSQL as $key => $value){
						//test acrreditation
						$isokpage = true;
						if (
							!empty($value['accred']) AND
							isset($_SESSION['user']['session']) AND
							!empty($_SESSION['user']['session']['accred']) AND 
							strlen($_SESSION['user']['session']['accred']) >= strlen($value['accred'])
						){
							$useraccred = strlen($_SESSION['user']['session']['accred']);
							$pageaccred = strlen($value['accred']);
							for ($i=0; $i<$pageaccred and $i<$useraccred ; $i++){
								if ($_SESSION['user']['session']['accred'][$i] < $value['accred'][$i]){
									$isokpage = false;
								}
							}
						}
						if ($isokpage){
							$nouvelleliste[$value['name']] = [
								'type' => $value['type']
								,'id_page' => $value['id_page']
							];
							//	Fun::print_air($nouvelleliste);
							$condition = false;
							if (!empty($value['author'])){
								$toto = json_decode($value['author'], true);
								foreach($toto as $key2 => $value2){
									$boolA = !empty($_SESSION['user']['session']); // la session existe t'elle ?
									if ($boolA === false AND $boolA === $value2) {
										$condition = true;
									}
									elseif ($boolA === true) {
										$boolB = !empty($_SESSION['user']['session']) AND !empty($_SESSION['user']['session'][$key2]);
										$boolC = ($boolB === true);
										$condition = $value2 === $boolC ? true : false;
									}
								}
							}
							else { 
								$condition = true;
							}
							if ($condition){ // add to menu
								$nouvelleliste[$value['name']]['auth'] = ($condition ? true : false);
								if ($this->_lesPagesSQL[$key]['ismenu']){ // add to menu
									$nouvelleliste[$value['name']]['menu'] = [
										'name' => $this->_lesPagesSQL[$key]['name']
										,'content' => $this->_lesPagesSQL[$key]['content']
										,'url' => !empty($this->_lesPagesSQL[$key]['name']) ? "?".$this->_lesPagesSQL[$key]['name'] : '?'.$this->_lesPagesSQL[$key]['url']
										,'urltitle' => !empty($this->_lesPagesSQL[$key]['urltitle']) ? DICO[100]." (".$this->_lesPagesSQL[$key]['urltitle'].')' : DICO[100]." (".$this->_lesPagesSQL[$key]['name'].')'
										,'class' => (!empty($value['classsup']) ? $value['classsup'] : ''.$value['name'] )
									];
								}
							}
						}
					}
					$this->_lesPages = $nouvelleliste;
					// $this->_lesPagesSQL = $nouvelleliste;
				
				}
				// nettoyage des pages par accès
				foreach($this->_lesPages as $key => $value){
					if(
						$value['type'] === 'page' AND 
						!empty($value['auth']) AND 
						$value['auth'] === true
					){
						$this->pagesPossibles[] = $key;
					}
				};
				//PAGE DETECT
				parse_str($_SERVER['QUERY_STRING'],$this->_getDatas);
				$this->_detectpagename();
	
				$this->_set_MenuTop();
				$this->get_Page();
				
				$this->pageHtml = str_replace('#MAINTITLE#', WEBSITE['head_title'], $this->pageHtml);
				$this->pageHtml = str_replace('#JSPART#', '', $this->pageHtml);
				$this->pageHtml = str_replace('#JSSESSION#', (ROOTS['debug'] ? '<script src="theme/js/scripts.js"></script>' : ''), $this->pageHtml);
				// DISPLAY SESSION
				$this->pageHtml = str_replace('#SESSIONS#', $this->print_log(), $this->pageHtml);
				// Display  time
				$this->pageHtml = str_replace('#ENDT#', "Traitement: " . (microtime(true) - CHRONO) . ' sec', $this->pageHtml);
				
				//	Fun::print_air($this->_detectGet('emprunt'),'test');

			}
			// else {
			// 	Datalogs::writeToLogs('errors', "ARRET DU CODE:(cause: Connexion DB innexistante ) ",[__FILE__,__FUNCTION__,__LINE__]);
			// 	Fun::stop("ARRET DU CODE (cause: Connexion DB innexistante ) ".__FILE__."|".__CLASS__."|".__FUNCTION__."|".(__LINE__),__FUNCTION__);
			// }







			// --------------------
			// AFFICHAGE DE LA PAGE
			// SI STOP MUET
			// --------------------
			if (trait_exists('t_PageTracer')) {
				t_PageTracer::tryClickLog(self::$_pageCurrent);
			}
			else {
				DataLogs::writeToLogs("errors","le trait t_PageTracer n'existe pas!!!",[__FILE__,__FUNCTION__,__LINE__]);
			}
			
			$this->get_header();
			echo $this->pageHtml;
			// --------------------
			// --------------------
			
		}
		static function get_header(){
			if (!headers_sent()) {
				header(WEBSITE['header']);
			}
		}
		static function setActionDatas($actionDatas,$vue=false){
				if (!empty($actionDatas)){
					
					if ($vue){
						$actionDatas['htmlvue'] = $vue;
					}
					else{
						// $actionDatas['htmlvue'] = self::getfilecontent(  ROOTS['controller'].'actions/vues/'.$actionDatas['vue'].".php");
						$actionDatas['htmlvue'] = self::getfilecontent(  ROOTS['controller'].'actions/vues/'.$actionDatas['vue'].".php");
					}
					self::$_actionDatas = $actionDatas;
					return true;
				}
				return false;
		}
		private function print_log(){
			return ROOTS['debug'] ? '<div id="divsession" class=""><div class="sessionprint"><div id="fermerdivsession"><i focusable="true" class="fas fa-window-close"></i></div>'.Fun::print_airZ($_SESSION,'Session[]:<br/>').'</div></div>' : '';
		}
		public function get_Page(){ // NEED RENAME
			$currentController = $this->requireonce(ROOTS['controllers'] . 'C_' . self::$_pageCurrent . ".php");
			$this->pageHtml = str_replace('#HTML#', $currentController, $this->pageHtml);
		}
		private function _set_MenuTop(){
			$currentController = $this->requireonce(ROOTS['controllers'] . "C_menutop.php");
			$this->pageHtml = str_replace('#MENUTOP#', $currentController, $this->pageHtml);
		}
		private function _detectpagename(){
			$rep = explode('/', $_SERVER['PHP_SELF']);
			$rep = $rep[count($rep)-1];
			parse_str($_SERVER['QUERY_STRING'], $array_arg);
			if (!empty($_SESSION['user']['session']['tokenCode'])) {
				$page_cible = WEBSITE['indexloged'];
			}
			else {
				$page_cible = WEBSITE['index'];
			}
			if ($array_arg AND count($array_arg)>0) {
				foreach($array_arg as $key => $value){
					$page_cible = (in_array ($key, $this->pagesPossibles, true)) ? $key : $page_cible;
					break; // take the first matching QUERY_STRING then break
				}
			}
			$_SESSION['user']['current_page'] = $page_cible;
			self::$_pageCurrent = $page_cible;
		}
		// Get files
		static function getfilecontent($fileandpath){
			if (file_exists($fileandpath)){
				$_SESSION['cms']['get_contents'][] = 'File_get_contents:'.$fileandpath;
				return file_get_contents($fileandpath);
			} 
			else {
				$_SESSION['cms']['errors'][] = 'Cant file_get_contents:'.$fileandpath;
				return false;
			}
		}
		private function requireonce($fileandpath){
			if (file_exists($fileandpath)){
				$_SESSION['cms']['require'][] = 'require_once:'.$fileandpath;
				require_once($fileandpath);
				// Fun::print_air($fileandpath);
				return $currentVue;
			} else {$_SESSION['cms']['errors'][] = 'cant require_once:'.$fileandpath;}
		}
		// static function get_CurrentId_Page(){
		//	Fun::print_air($this->_lesPages,'_lesPages');
		//	Fun::print_airself::$_pageCurrent,'_pageCurrent');
		// }
		public function get_lesPages(){
			// return self::$_lesPages;
			return $this->_lesPages;
		}
	}
