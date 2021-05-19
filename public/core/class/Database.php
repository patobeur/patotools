<?php
	class Database {	
		private static $_cont = null;
		private static $_firstContact = false;
		private static $_bddContact = false;
		private static $_dietexte = 'Jetez un oeil dans les logs !!!';
		
		private static $_checkBdd = false; // Linked to CheckBdd Class

		public function __construct(){
			if($this->get_firstContact()) {
				if($this->get_bddContact()) {
					self::set_checkBdd(true);
				}
			}
		}
		
		public static function set_checkBdd($isok=false){
			self::$_checkBdd = $isok ? true : false;
		}
		public static function set_bddContact($checkBddName){
			self::$_bddContact = $checkBddName;
		}
		public static function get_checkBdd(){
			return self::$_checkBdd;
		}
		public static function get__CheckfirstContact(){
			return self::$_firstContact;
		}
		public function get_firstContact(){
			// Fun::print_air('_firstContact.........................................');
			// savoir si s'il on peut se connecter à la base de donnée 
			try{
				self::$_firstContact = new PDO("mysql:host=".DB['host'],DB['user'],DB['pass']);
				// Fun::print_air(self::$_firstContact,'_firstContact ok');
				return true;
			}
			catch(PDOException $e){
				// on ne peut pas se connecter à la base de donnée 
				Fun::print_air('BDD ERROR - LEVEL 1 get_firstContact raté ');
				DataLogs::writeToLogs("errors","BDD ERROR - LEVEL 1 - get_firstContact raté - ".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
				Fun::stop("BDD ERROR - LEVEL 1 - get_firstContact raté - ".$e->getMessage(),__FUNCTION__);
				Fun::stop(false,__FUNCTION__);
			}
			return false;
		}
		public function get_bddContact(){
			// Fun::print_air('_dbContact.........................................');
			// savoir si s'il on peut se connecter à la base de donnée 
			try{
				self::$_bddContact = new PDO("mysql:host=".DB['host'].";"."dbname=".DB['name'].";charset=".DB['charset'],DB['user'],DB['pass']);
				//Fun::print_air(self::$_bddContact,'_bddContact ok');
				return true;
			}
			catch(PDOException $e){
				// on ne peut pas se connecter à la base de donnée 
				Fun::print_air('BDD ERROR - LEVEL 2 get_bddContact raté ');
				DataLogs::writeToLogs("errors","BDD ERROR - LEVEL 2 - get_bddContact raté - ".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
				Fun::stop("BDD ERROR - LEVEL 2 - get_bddContact raté - ".$e->getMessage(),__FUNCTION__);
			}
			return false;
		}
		
		// public static function cosdfsdfnnect($caller){ // used in User Class Only
			// 	Fun::print_air(__FUNCTION__);
			// 	// Fun::print_air($caller."(".__FUNCTION__.")");
			// 	if ( null == self::$_cont ){
			// 		$bonjour = false;
			// 		//test de connexion
			// 		try{
			// 			$bonjour = new PDO("mysql:host=".DB['host'],DB['user'],DB['pass']);
			// 			// Datalogs::writeToLogs('dbaccess', "OK;La connexion DB est bonne !",[__FILE__,__FUNCTION__,__LINE__]);
			// 		}
			// 		catch(PDOException $e){
			// 			Datalogs::writeToLogs('errors', "ERREUR;La connexion DB est refusée !".$e->getCode(),[__FILE__,__FUNCTION__,__LINE__]);
			// 			Fun::stop(self::$_dietexte);
			// 			// Fun::stop("end");
			// 		}

			// 		// connexion ok ! 
			// 		if (!$bonjour===false AND gettype($bonjour)==='object'){
			// 			try{
			// 				self::$_cont = new PDO("mysql:host=".DB['host'].";"."dbname=".DB['name'].";charset=".DB['charset'],DB['user'],DB['pass']);
			// 				// Datalogs::writeToLogs('dbaccess', "OK;La DB ".DB['name']." existe et la connexion est bonne !",[__FILE__,__FUNCTION__,__LINE__]);



			// 			}
			// 			catch(PDOException $e){	
			// 				Datalogs::writeToLogs('errors', "ERREUR;La DB ".DB['name']." n'existe pas !",[__FILE__,__FUNCTION__,__LINE__]);
			// 				Fun::stop("(".$e->getMessage()."), Jetez un oeil dans les definitions ![".__FILE__."|".__FUNCTION__."|".__LINE__."]");
			// 			}

						



			// 			//	Fun::print_air('la connection à la bdd est : '. (!empty(self::$_cont) ? "faite" : "refusée"));
			// 				//	Fun::print_air("test des ".count(TABLES).' tables attendues...');
			// 				// $tablesNONvides = 0;
			// 				// foreach(TABLES as $key => $value){
			// 				// 	if($value){
			// 				// 		try {
			// 				// 			$sql = "SELECT * FROM ".TABLES[$key];
			// 				// 			$stmt = self::$_cont->prepare($sql);
			// 				// 			$stmt->execute();
			// 				// 			$testdatas = $stmt->fetchall(PDO::FETCH_ASSOC);
			// 				// 			$tablesNONvides = count($testdatas) > 0 ? $tablesNONvides + 1 : $tablesNONvides;
			// 				// 		}
			// 				// 		catch(PDOException $e){
			// 				// 			//Fun::print_air("Erreur [".$e->getMessage()."] avec la fonction ".__FUNCTION__."(table:".TABLES[$key].") ligne:".__LINE__);
			// 				// 			Datalogs::writeToLogs('errors', "ERREUR2;".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
			// 				// 			Fun::stop("Erreur2 [".$e->getMessage()."] avec la fonction ".__FILE__."|".__FUNCTION__."|".__LINE__);
										
			// 				// 		}
			// 				// 	}
			// 				// }
			// 				// if($tablesNONvides != count(TABLES)) {
			// 				// 	//	Fun::print_air('[lancement du nettoyage...]');
			// 				// 	self::$_cont = false;
			// 				// 	// Fun::stop("[il manque ".(count(TABLES)-$tablesNONvides)."]");
			// 				// 	Fun::stop($tablesNONvides." tables trouvées sur ".count(TABLES).".".__FILE__."|".__FUNCTION__."|".__LINE__);
			// 				// 	// Fun::stop("end");
			// 				// }
			// 				// else {
			// 				// 	//	Fun::print_air('['.(count(TABLES)."/".$tablesNONvides).' tables]');
			// 			// }
			// 			unset($sql);
			// 			unset($testdatas);
			// 		}
			// 	}
			// 	return self::$_cont;
		// }

		static function queryBindSelect($sql,$bind=[],$callingParent="indéfinie",$conne=false) {
			
			// print_air($sql,'sql');
			// print_air($bind,'bind');
			// print_air($callingParent,'callingParent');
			// print_air('--------------------');
			// print_air(gettype(self::$_firstContact),'_firstContact gettype');
			// print_air(gettype(self::$_cont),'_cont gettype');
			// print_air('--------------------');
			if(self::$_firstContact && self::$_bddContact){
				self::$_cont = self::$_bddContact;
				if (!empty($sql) OR !empty($bind) OR !count($bind) ){
					try
					{
						$stmt = self::$_bddContact->prepare($sql);
						foreach($bind as $param => $value) {
								$c = 1;
								for ($i=0; $i<count($value); $i++) {
									switch(gettype($value[$i])){
										case 'integer':
											$stmt->bindParam($i+1, $value[$i], PDO::PARAM_INT);
										break;
										case 'string':
											$stmt->bindParam($i+1, $value[$i], PDO::PARAM_STR);
										break;
										case 'array':
										case 'bool':
										case 'double':
										case 'object':
										case 'resource':
										case 'NULL':
										case 'unknown type':
										default:
											die();
										break;
									}
								}
								$stmt->execute();
						}
						$results = $stmt->fetchall(PDO::FETCH_ASSOC);
	
						if (count($results)>0){
							// au moins une réponses !!! PAS D'ERREUR
							$_SESSION['cms']['sql'][] = '♡ '.$callingParent.'('.self::get_SqlToString($sql,$bind).'))';
							return $results;
						}
						if (count($results)===0){
							// pas de réponse ?? PAS D'ERREUR
							$_SESSION['cms']['sql'][] = '♡ ♤ '.$callingParent.'( '.self::get_SqlToString($sql,$bind).' ['. DICO[601].'] ))';
							return $results;
						}
						$_SESSION['cms']['errors'][] = '♡ '.$callingParent.'('. (ROOTS['debug'] ? DICO[900] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
						DataLogs::writeToLogs("errors",'♡ '.$callingParent.'('. (ROOTS['debug'] ? DICO[900] : false) .' '. self::get_SqlToString($sql,$bind) . '))',[__FILE__,__FUNCTION__,__LINE__]);
						Fun::stop('♡ '.$callingParent.'('. (ROOTS['debug'] ? DICO[900] : false),__FUNCTION__);
					}
					catch(PDOException $e){
						$_SESSION['cms']['errors'][] = '♡ '.$callingParent.'('. (ROOTS['debug'] ? DICO[600] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
						DataLogs::writeToLogs("errors","ici--->".$e->getMessage(),[__FILE__,__FUNCTION__,__LINE__]);
						Fun::stop("ici--->".$e->getMessage(),__FUNCTION__);
						// ROOTS['debug'] ? die('ici--->'.$e->getMessage()) : die();
					}
					return false;
				}
			}
			else {
				Fun::stop("Erreur pas de connexion ! ".__FILE__."|".__FUNCTION__."|".__LINE__. "----",__FUNCTION__);
			}
		}
		static function queryBindInsert($sql,$bind=false,$callingParent="indéfinie") {
			if (!empty($sql) OR !$bind OR !is_array($bind)){
				try
				{
					// Fun::print_air($callingParent,"-----------------------------------------------------");
					// Fun::print_air($sql,"sql");
					// Fun::print_air($bind,"bind");
					// Fun::print_air($bind,"bind");
					// Fun::print_air(self::$_cont,"_cont");
					Fun::stop("onerror",__FUNCTION__);

					$stmt = self::$_cont->prepare($sql);

					if (is_array($bind)){
						foreach($bind as $param => $value) {
							$c = 1;
							for ($i=0; $i<count($value); $i++) {
									$stmt->bindValue($c++, $value[$i]);
							}
						}
					}
					if ($stmt->execute()){
						$_SESSION['cms']['sql'][] = '+ '.$callingParent.'( '.self::get_SqlToString($sql,$bind).' ))';
						if(!empty($_SESSION['user']['LastIdUsed']) AND !CONF['lastUsedId']){
							unset($_SESSION['user']['LastIdUsed']);
						}
						return true;
					}
					$_SESSION['cms']['errors'][] = '+ '.$callingParent.'('. (ROOTS['debug'] ? DICO[601] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
					Fun::stop(" ".self::get_SqlToString($sql,$bind). " ".__FILE__."|".__FUNCTION__."|".__LINE__,__FUNCTION__);
				}
				catch(PDOException $e){
					$_SESSION['cms']['errors'][] = 'E+ '.$callingParent.'('. (ROOTS['debug'] ? DICO[600] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
					// ROOTS['debug'] ? die($e->getMessage()) : die();
					Fun::stop($e->getMessage()." ".self::get_SqlToString($sql,$bind). " ".__FILE__."|".__FUNCTION__."|".__LINE__,__FUNCTION__);
				}
				return false;
			}
		}		
		static function queryBindUpdate($sql,$bind=false,$callingParent="indéfinie") {
			//	Fun::print_air($bind);
			if (!empty($sql) OR !empty($bind) OR !count($bind)){
				try
				{
					$stmt = self::$_cont->prepare($sql);
					if (is_array($bind)){
						foreach($bind as $param => $value) {
							$c = 1;
							for ($i=0; $i<count($value); $i++) {
									$stmt->bindValue($c++, $value[$i]);
							}
						}
					}
					try
					{
						if ($stmt->execute()){
							$_SESSION['cms']['sql'][] = '♣ '.$callingParent.'( '.self::get_SqlToString($sql,$bind).' ))';
							if(!empty($_SESSION['user']['LastIdUsed']) AND !CONF['lastUsedId']){
								unset($_SESSION['user']['LastIdUsed']);
							}
							return true;
						}
					}
					catch(PDOException $e){
						$_SESSION['cms']['errors'][] = '♣ '.$callingParent.'('. (ROOTS['debug'] ? DICO[600] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
						ROOTS['debug'] ? die($e->getMessage()) : die();
					}
					$_SESSION['cms']['sql'][] = '♣ '.$callingParent.'( '.self::get_SqlToString($sql,$bind).' ))';
					return true;
				}
				catch(PDOException $e){
					$_SESSION['cms']['errors'][] = '♣ '.$callingParent.'('. (ROOTS['debug'] ? DICO[600] : false) .' '. self::get_SqlToString($sql,$bind) . '))';
					ROOTS['debug'] ? die($e->getMessage()) : die();
				}
				return false;
			}
		}

		static function get_SqlToString($sql,$bind){
			if($bind!=false){
				$sql = str_replace('= ?', "='%s'", $sql);
				$sql=vsprintf($sql,$bind[0]);
			}
			// else {
			//	Fun::print_air(['bind'=>$bind, 'sql'=>$sql]);
			// }
			return $sql;
		}

		static function get_LastIdUsed(){
			$_SESSION['user']['LastIdUsed'] = self::$_cont->lastInsertId() === 0 ? false : self::$_cont->lastInsertId();
			return $_SESSION['user']['LastIdUsed'];
		}

		// NEED CLEAN
		static function get_PagesList($type,$parent=0,$callingParent="indéfinie",$active=1) {
			$retour = false;
			if (!empty($type)){
					$retour = self::queryBindSelect(
						"SELECT * FROM ".TABLES['pages']." WHERE ".TABLES['pages'].".parent = ? AND ".TABLES['pages'].".active = ? AND ".TABLES['pages'].".type = ?",
						[[$parent,$active,$type]],
						$callingParent.'( '.__CLASS__.'('.__FUNCTION__
					);
			}
			return $retour;
		}
		static function get_Import($type,$parent=0,$callingParent="indéfinie",$active=1) {
			$retour = false;
			if (!empty($type)){
					$retour = self::queryBindSelect(
						"SELECT * FROM ".TABLES['pages']." WHERE ".TABLES['pages'].".parent = ? AND ".TABLES['pages'].".active = ? AND ".TABLES['pages'].".type = ?",
						[[$parent,$active,$type]],
						$callingParent.'( '.__CLASS__.'('.__FUNCTION__
					);
			}
			return $retour;
		}
	}