 <?php
 
 class DataLogs{
	private static $_repertoiredeslogs = ROOTS['logs'];// ../private/logs/
	// private static $_prefixfichier = "gestionaccueil.";
	private static $_prefixfichier = ROOTS['prefixfilelogs']; // gestionaccueil.
	private static $_extensionfichier = ".txt";	
	private static $_retourchariot = "\n";
	private static $_separator = ";";
	private static $_maxtexte = 115; // defaut is 130 if equal 0
	private static $_space = " ";
	private static $_currentFileKey = false;
	private static $_files = ["errors" => [],"visites" => [],"dbaccess" => []];

	public function __construct(){
		// self::writeToLogs("errors", "Test du fichier.",[__FILE__,__FUNCTION__,__LINE__]);
		$this->checkLogFiles();
	}

	static private function checkLogFiles(){
		foreach (self::$_files as $key => $value){
			// si la mise a jour FileAndKey est true 
			// alors on test l'existence du fichier
			if (self::set_currentFileAndKey($key)) {
				// si le fichier n'existe pas on le crée
				if(!file_exists(self::$_currentFileKey['fullpathfilename'])){
					self::createLogFiles();	
				}
			}
			else {
				Fun::print_air($key . " la clé ".$key." n'existe pas ! [".__FUNCTION__.":".__LINE__."]");
			}
		}
	}
	
	static private function set_currentFileAndKey($key){
		if(array_key_exists($key,self::$_files)){
			self::$_currentFileKey = [	
				"key" => $key,
				"fullpathfilename" => self::$_repertoiredeslogs . self::$_prefixfichier . $key . self::$_extensionfichier
			];
			return true;
		}
		Fun::print_air($key . " n'existe pas ! [".__FUNCTION__.":".__LINE__."]");
		return false;
	}
	
	static private function unset_currentFileAndKey(){
		self::$_currentFileKey = false;
	}
	


	static private function spaceMaker($texte){
		$spaces = "";
		$nbspaces = self::$_maxtexte - strlen($texte);
		for($i = 0 ; $i < $nbspaces; $i++){
			$spaces .= self::$_space;
		}
		return $spaces;
	}
	static private function parentToString($texte,$parentCaller=["fichier?","fonction?","ligne?"]){
		// if (!$parentCaller[0]==="fichier?"){
			// si on connait la fonction appelante on met des espaces
			$textString = self::$_separator.self::spaceMaker($texte);
			if($parentCaller!=false){
				$textString .= (isset($parentCaller[0]) ? self::$_separator.mb_substr($parentCaller[0],-23).";" : "").
				(isset($parentCaller[1]) ? $parentCaller[1].";" : "").
				(isset($parentCaller[2]) ? $parentCaller[2].";" : "");
			}
		// }
		return $textString;
	}




	/**
	 * writeToLogs
	 *
	 * @param  mixed $key
	 * @param  mixed $texte string with Tabulation spaces
	 * @return void
	 */	
	static public function writeToLogs($key, $texte ,$parentCaller){
		if(self::set_currentFileAndKey($key)){
			$parentCaller = self::parentToString($texte,$parentCaller);
			$fullpathfilename = self::$_currentFileKey['fullpathfilename'];
			// si le fichier existe
			if(file_exists($fullpathfilename)){
				// on l'ouvre en ecriture
				// $openedFile = fopen($fullpathfilename, 'a+');
				
				try {
					$openedFile = self::openFile($fullpathfilename);
				}
				catch(Exception $e){
					Fun::print_air($e->getMessage());
					die();
				}
				
				if($openedFile){
					// je met du texte
					fputs($openedFile, self::get_promptLog() . $texte . $parentCaller . self::$_retourchariot);
					// je ferme le fichier txt !
					fclose($openedFile);
				}
				else {
					Fun::print_air($fullpathfilename . " n'a pas été mis à jour ! ERREUR !!! [".__FUNCTION__.":".__LINE__."]");
				}
			}
			self::unset_currentFileAndKey();
		}
	}
	static private function openFile($fullpathfilename){
		try {
			$openedFile = fopen($fullpathfilename, 'a+');
			return $openedFile;
		}
		catch(Exception $e){
			Fun::print_air($fullpathfilename . " n'a pas été crée ! ERREUR !!! (".$e->getMessage().")");
			return false;
		}



		
        // if(!file_exists($fullpathfilename)){
        //         throw new Exception (sprintf('Le fichier « %s » n\'existe pas .', $fullpathfilename));
        // }
		// if($openedFile == false){
		// 	throw new Exception (sprintf('Erreur de chargement du fichier « %s » .', $fullpathfilename));
		// }
	}
	/**
	 * createLogFiles
	 *
	 * @return void
	 */
	static private function createLogFiles(){
		$fullpathfilename = self::$_currentFileKey['fullpathfilename'];
		$key = self::$_currentFileKey['key'];

		if(!file_exists($fullpathfilename)) {
			
			try {
				if ($openedFile = self::openFile($fullpathfilename)){
					fclose($openedFile);
				}
			}
			catch(Exception $e){
				Fun::print_air($e->getMessage());
			}


			if(file_exists($fullpathfilename)){
				self::writeToLogs($key, "Création du fichier.",[__FILE__,__FUNCTION__,__LINE__]);
			}
			else {
				Fun::print_air($fullpathfilename . " n'a pas été crée ! ERREUR !!! [".__FILE__.":".__FUNCTION__.":".__LINE__."]");
			}
		}
	}

	static private function get_promptLog(){
		return date('Y-m-d H:i:s').self::$_separator;
	}
}
