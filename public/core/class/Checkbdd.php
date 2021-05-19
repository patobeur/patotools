<?php
	class Checkbdd {	
		private static $_checkBdd = false;
		private static $_checkBddName = false;

		public function __construct(){
			if (!isset($_SESSION['checkBdd'])) {
				// creation de la session checkBdd = false par défaut
				// passera a true quand toutes les tables sont validées
				$_SESSION['checkBdd'] = false;
			}
			if($_SESSION['checkBdd']===false){
				self::$_checkBdd = Database::get__CheckfirstContact();
				if(!$this->checkBdd()===false){
					if(!self::checkDbname()===false){
						if(!self::checkTables()===false){
							$_SESSION['checkBdd'] = true;
							Fun::stop("destroy","__construct check ok");
							Database::set_checkBdd($_SESSION['checkBdd']);
							Database::set_bddContact(self::$_checkBddName);
						}
					}
					else {
						if (INSTALLATION['active']) {
							if(self::create_ThisDatabase()){
								self::hydrate_Tables();
							}
						}
						else {
							Fun::stop("(), Erreur: Connexion bonne mais La DB ".DB['name']." n'existe pas ! [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
							Fun::stop("(), Erreur: AUTO-INSTALLATION DESACTIVEE [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
						}

					}
				}
			}
			elseif ($_SESSION['checkBdd']===true){
				Database::set_checkBdd($_SESSION['checkBdd']);
			}
		}
		

		// CheckBdd
		private static function checkBdd(){
			$result = false;
			if (!((!self::$_checkBdd===false) && is_object(self::$_checkBdd))){
				Fun::print_air(__FUNCTION__."ERREUR -> de connexion ! ");
				Datalogs::writeToLogs('errors', "ERREUR -> de connexion.",[__FILE__,__FUNCTION__,__LINE__-3]);
				Fun::stop("ERREUR -> de connexion.".__FILE__."|".__CLASS__."|".__FUNCTION__."|".(__LINE__-3),__FUNCTION__);
			}
			else {
				$result = true;
			}
			return $result;
		}
		// checkDbname
		private static function checkDbname(){
			$result = false;
			try{
				self::$_checkBddName = new PDO("mysql:host=".DB['host'].";"."dbname=".DB['name'].";charset=".DB['charset'],DB['user'],DB['pass']);
				return true;
			}
			catch(PDOException $e){	
				Fun::print_air(__FUNCTION__."ERREUR -> de connexion ! ");
				Datalogs::writeToLogs('errors', "ERREUR;La DB ".DB['name']." n'existe pas !",[__FILE__,__FUNCTION__,__LINE__-6]);
				//Fun::stop("ERREUR -> La DB ".DB['name']." n'existe pas ! ".__FILE__."|".__CLASS__."|".__FUNCTION__."|".(__LINE__-6),__FUNCTION__);
				self::$_checkBddName = false;
			}
			return $result;
		}
		
		private static function checkTables(){
			$result = false;
			// Fun::print_air(__FUNCTION__,"Option Check des Tables Actives");
			Datalogs::writeToLogs('errors', "Option Check des Tables Actives",[__FILE__,__FUNCTION__,__LINE__-6]);
			if(self::$_checkBddName){
				Datalogs::writeToLogs('errors', "Connexion '_checkBddName'=".gettype(self::$_checkBdd),[__FILE__,__FUNCTION__,__LINE__-6]);
				// Fun::print_air(gettype(self::$_checkBdd),"---------------------------gettype");
				$errorcount = 0;
				foreach (TABLES as $key){
					try{
						$checkDbTables = self::$_checkBddName->prepare("SELECT 1 FROM " . $key . " LIMIT 1");
						$checkDbTables->execute();
						// Fun::print_air("la table [".$key."] existe !!! ");
						Datalogs::writeToLogs('errors', "la table [".$key."] existe !!! ",[__FILE__,__FUNCTION__,__LINE__]);
					}
					catch(PDOException $e)
					{
						$errorcount++;
						Fun::print_air("la table [".$key."] est absente !!! ");
						Datalogs::writeToLogs('errors', "la table [".$key."] est absente !!! ",[__FILE__,__FUNCTION__,__LINE__]);
						Fun::stop("(".$e->getMessage()."), Erreur:la table [".$key."] est absente !!!  [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
					}
				}




				if($errorcount>0){
					Datalogs::writeToLogs('errors', "AUTOINSTALL:" . (INSTALLATION['active'] ? 'true' : 'false'),[__FILE__,__FUNCTION__,__LINE__]);
					Datalogs::writeToLogs('errors', "AUTOINSTALL:" . (INSTALLATION['delete'] ? 'true' : 'false'),[__FILE__,__FUNCTION__,__LINE__]);

					Fun::print_air('________________________________________');
					Fun::print_air("AUTOINSTALL:" . (INSTALLATION['active'] ? 'true' : 'false'));
					Fun::print_air("DELETEDB:" . (INSTALLATION['delete'] ? 'true' : 'false'));

					if (INSTALLATION['active'] ){
						if (INSTALLATION['delete']){
							// SUPPRESSION BDD
							self::drop_Database([__FILE__,__FUNCTION__,__LINE__]);
							Fun::print_air("La Base de données [".DB['name']."] a été suprimée !!!");

							if (INSTALLATION['redirect']){
								Fun::stop("destroy",__FUNCTION__);
								Fun::print_air("Refresh dans :".INSTALLATION['delay']);
								Datalogs::writeToLogs('errors',"Redirection et relancement de la procédure d'installation...",["","",""]);
								Fun::refresh(INSTALLATION['delay'],WEBSITE['siteurl'], true, 404 , true);
							}
							else {
								Fun::print_air($errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'redirect' est désactivé [false], Vous devez rafraichir la page manuellement (appuyer sur F5)");
								Datalogs::writeToLogs('errors', $errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'redirect' est désactivé [false], Vous devez rafraichir la page manuellement (appuyer sur F5)",[__FILE__,__FUNCTION__,__LINE__]);
								die();
							}
						}
						else {
							Fun::print_air($errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'delete' est désactivé [false], Vous devez supprimer la base de donnée ".DB['name']." manuellement et relancer l'installation");
							Datalogs::writeToLogs('errors', $errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'delete' est désactivé [false], Vous devez supprimer la base de données ".DB['name']." et relancer l'installation",[__FILE__,__FUNCTION__,__LINE__]);
							die();
						}
					}
					else {
						Fun::print_air($errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'AUTOINSTALL' est désactivé [false], Vous devez supprimer la base de donnée ".DB['name']." manuellement et relancer l'installation");
						Datalogs::writeToLogs('errors', $errorcount . " erreur".($errorcount>0 ? "s" : ""). " L'option 'AUTOINSTALL' est désactivé [false], Vous devez supprimer la base de données ".DB['name']." et relancer l'installation",[__FILE__,__FUNCTION__,__LINE__]);
						die();
					}

					if (INSTALLATION['active']){						
						// PENSEZ à mettre un compteur de passage pour eviter la boucle sans fin
						Fun::stop("onerror",__FUNCTION__);
					}
				}
				if($errorcount===0){
					$result = true;
				}
			}
			return $result;
		}
		// Check
		static function create_ThisDatabase(){
			Fun::stop("clean",__FUNCTION__);
			$result = false; 
			if(self::$_checkBdd && !self::$_checkBddName){
				try{
					//création de la base de donnée DB['name']
					$_checkDbConnCreation = self::$_checkBdd->prepare("CREATE DATABASE IF NOT EXISTS ".DB['name']." DEFAULT CHARACTER SET ".DB['charset']." COLLATE ".DB['collate']."; USE `".DB['name']."`;");
					$_checkDbConnCreation->execute();
					try{
						Datalogs::writeToLogs('errors', "----OK -> Création DB [".DB['name']."]. -> réussie !",[__FILE__,__FUNCTION__,__LINE__]);
						Fun::print_air("Création de la base de données [".DB['name']."]. -> réussie !" );

						self::$_checkBddName = new PDO("mysql:host=".DB['host'].";"."dbname=".DB['name'].";charset=".DB['charset'],DB['user'],DB['pass']);
						Fun::print_air("Connexion à la base de données [".DB['name']."] fraichement crée réussie !");
						Datalogs::writeToLogs('errors', "----OK -> Connexion à la DB [".DB['name']."] fraichement crée réussie. ",[__FILE__,__FUNCTION__,__LINE__]);
						$result = true;
					}
					catch(PDOException $e){
						self::$_checkBddName = false;
						Datalogs::writeToLogs('errors', "ERREUR -> Connexion à DB [".DB['name']."] fraichement crée impossible. ".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
						Fun::stop("(".$e->getMessage()."), Erreur:Connexion à DB [".DB['name']."] fraichement crée impossible! [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
					}
				}
				catch(PDOException $e){
					Datalogs::writeToLogs('errors', "ERREUR -> Création DB [".DB['name']."] impossible. ".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
					Fun::stop("(".$e->getMessage()."), Erreur:Création DB [".DB['name']."] impossible ! [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
				}				
			}
			else {
				Fun::print_air("Pas de connexion, la DB [".DB['name']."]. -> n'est pas crée." );
				Datalogs::writeToLogs('errors', "ERREUR -> Pas de connexion, la DB ".DB['name']." n'est pas crée. ",[__FILE__,__FUNCTION__,__LINE__]);
				Fun::stop("(), Erreur:Pas de connexion, la DB ".DB['name']." n'est pas crée ! [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
			}
			unset($_checkDbConnCreation);
			Fun::stop("onerror","jjj".__FUNCTION__);
			return $result;
		}

		
		// INSTALL
		static function hydrate_Tables(){
			Fun::stop("clean",__FUNCTION__);
			$result = false;
			$tableCheck2 = [];
			try
			{
				// $errortexte = 'probleme avec la table '.'pages';
				$datasTables = self::get_Datas();
				Fun::print_air("----- création des tables ----------");
				if (!( count($datasTables) === count(TABLES) )){
					Fun::print_air(count($datasTables).' table(s) proposée(s)');
					Fun::print_air(count(TABLES).' table(s) définie(s)');
					// drop table
					self::drop_Database([__FILE__,__FUNCTION__,__LINE__]);
					// die('les tables ne sont pas toutes définies');
					Datalogs::writeToLogs('errors', "ERREUR -> les tables ne sont pas toutes définies dans definitions.php. ",[__FILE__,__FUNCTION__,__LINE__]);
					Fun::stop("(), Erreur: les tables ne sont pas toutes définies dans definitions.php. ! [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
			
				}

				$nbReq = 0;
				$nbReqBad = 0;
				$counttable = 0;
				foreach($datasTables as $key => $value){
					$b = 0;
					Datalogs::writeToLogs('errors', "SQL ( ".(($counttable++)+1)." / ".count(TABLES)." ) -----------> ".TABLES[$key],[__FILE__,__FUNCTION__,__LINE__]);
					foreach($value as $sqlreq){
						// replacement du nom de la table
						$sqlreq = str_replace("#TABLENAME#", TABLES[$key], $sqlreq);

						try {
							$creation = self::$_checkBddName->prepare($sqlreq);
							$creation->execute();
							Datalogs::writeToLogs('errors', "INJECTION -> ".($b+1)." ".TABLES[$key]." n'a pas donnée d'erreurs. ! ".($nbReq+1),[__FILE__,__FUNCTION__,__LINE__]);
						}
						catch(PDOException $e){
							Datalogs::writeToLogs('errors', "INJECTION -> ".($nbReq+1)."/".($b+1)." la table ".TABLES[$key]." n'a pas été crée. ! ",[__FILE__,__FUNCTION__,__LINE__]);
							Fun::stop("(".$e->getMessage()."), INJECTION: ".($nbReq+1)."/".($b+1)." la table ".TABLES[$key]." n'a pas été crée. [".__FILE__."|".__FUNCTION__."|".__LINE__."]",__FUNCTION__);
						}




						if (!isset($tableCheck2[$creation ? 1 : 0])){
							$tableCheck2[$creation ? 1 : 0] = [];
						}
						if (!isset($tableCheck2[$creation ? 1 : 0][TABLES[$key]])){
							$tableCheck2[$creation ? 1 : 0][TABLES[$key]] = [];
						}
						$tableCheck2[$creation ? 1 : 0][TABLES[$key]][$b] = $creation ? 'Requete '.($b+1).' ok!' : '<span style="color:red">Erreur avec la Requete '.($b+1).'</span>'; // succes
						$b++;
						$nbReq++;
						!$creation ? $nbReqBad++ : '';
					}



					
					if ( self::is_Table(TABLES[$key]) ) {
						Fun::print_air('création de: '.TABLES[$key].' réussie...');
						if ( !isset($tableCheck2[0][TABLES[$key]])){
							
							Datalogs::writeToLogs('errors', "INJECTION -> Création et hydratation de la table ".TABLES[$key]." réussie. ! ",[__FILE__,__FUNCTION__,__LINE__]);
							Fun::print_air('Création et hydratation de la table '.TABLES[$key].' réussie...');
							$tableCheck[1][] = TABLES[$key]; // succes
						}
						else {
							Fun::print_air('<span style="color:red">Création Ok mais erreur d\'hydratation de la table :'.TABLES[$key].'...</span>');
							$_SESSION['cms']['bdd'][TABLES[$key]] = '<span style="color:red">Création Ok mais erreur d\'hydratation de la table!</span>';
							Datalogs::writeToLogs('errors', "INJECTION -> Création Ok mais erreur d'hydratation de la table ".TABLES[$key]."!",[__FILE__,__FUNCTION__,__LINE__]);
							$tableCheck[0][] = TABLES[$key]; // erreurs
						}
					}
					else {
						Fun::print_air('création de: '.TABLES[$key].' raté ...');
						$_SESSION['cms']['bdd'][TABLES[$key]] = 'Erreur';
						$tableCheck[0][] = TABLES[$key]; // erreurs
					}
				}

				$bad = isset($tableCheck2[0]) ? count($tableCheck2[0]) : 0;
				$good = isset($tableCheck2[1]) ? count($tableCheck2[1]) : 0;
				Fun::print_air("------------------------------------------------");
				Fun::print_air("".$good." table".($good>1?'s':'')." crée".($good>1?'s':'')." sur ".count($datasTables)." ! (".($nbReq-$nbReqBad)."/".$nbReq." requete". ($nbReq>1?'s':'') ." réussie". ($nbReq>1?'s':'') .")");
				
				
				Datalogs::writeToLogs('errors', "INJECTION ->".$good." table".($good>1?'s':'')." crée".($good>1?'s':'')." sur ".count($datasTables)." ! (".($nbReq-$nbReqBad)."/".$nbReq." requete". ($nbReq>1?'s':'') ." réussie". ($nbReq>1?'s':'') .")",[__FILE__,__FUNCTION__,__LINE__]);
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				if ($bad>0) {
					// drop table
					Fun::print_air("------------------------------------------------");
					Fun::print_air($bad." Erreur".($bad>0 ? "s" : "")." dans la configuration sql ");
					Fun::print_air('Suppression de la base de données...');
					if (INSTALLATION['delete']){
						self::drop_Database([__FILE__,__FUNCTION__,__LINE__]);
						Fun::print_air('Base supprimée...');
						Fun::Stop("onerror",__FUNCTION__);
					} 
					else {
						Fun::print_air('Option [delete] non actif, désolé ! Base non supprimée...');
						Fun::print_air('pas de redirection....');
						Fun::Stop(false,__FUNCTION__);
					}
				}
				else {
					if (INSTALLATION['redirect']){
						Fun::stop("onerror",__FUNCTION__);
						Fun::print_air("Check Option de Redirection = ".(INSTALLATION['redirect'] ? "true" : "false"));
						Fun::print_air("Redirection dans ".INSTALLATION['delay']." seconde".(INSTALLATION['delay']>1 ? 's' : '')."... ");
						Datalogs::writeToLogs('errors', "Check Option de Redirection = ".(INSTALLATION['redirect'] ? "true" : "false"),[__FILE__,__FUNCTION__,__LINE__]);
						Datalogs::writeToLogs('errors', "REDIRECT EN COURS",[__FILE__,__FUNCTION__,__LINE__]);
						Fun::refresh(INSTALLATION['delay'],WEBSITE['siteurl'], true, 404 , true);
					}
					else {
						Datalogs::writeToLogs('errors', "Redirection Bloquée redirect=false",[__FILE__,__FUNCTION__,__LINE__]);
						Fun::print_air("Redirection Bloquée redirect=false");
						Fun::Stop(false,__FUNCTION__);
					}

				}
				
			}
			catch(PDOException $e){
				Datalogs::writeToLogs('errors', $e->getMessage()." ?????",[__FILE__,__FUNCTION__,__LINE__]);
				Fun::stop($e->getMessage(),__FUNCTION__.":".__LINE__);
				ROOTS['debug'] ? die($e->getMessage()) : die();
			}
		}
		public static function is_Table($tablename){
			// pas dans les log
			$test = self::$_checkBddName->query("SELECT 1 FROM " . $tablename . " LIMIT 1"); //$db needs to be PDO instance
			return $test ? 1 : 0;
		}
		static function drop_Database($datas=["?","?","?"]){
			// pas dans les log
			try{ 
				$drop_Database = self::$_checkBdd->query("DROP DATABASE `".DB['name']."`"); //$db needs to be PDO instance
				Datalogs::writeToLogs('errors', "SUPPRESSION DE LA BDD",$datas);
			}
			catch(PDOException $e){
				Datalogs::writeToLogs('errors', "ERREUR SUPPRESSION DE LA BDD IMPOSSIBLE !",$datas);
				Fun::stop("ERREUR SUPPRESSION DE LA BDD IMPOSSIBLE !".$datas[0].$datas[1].$datas[2],__FUNCTION__);
			}
			return ((!$drop_Database===false) && is_object($drop_Database));
		}
		//datas
		static function get_Datas(){
			require_once(ROOTS['class'].'checkbdd_datas.php');
			return $Checkbdd_datas;
		}
	}
