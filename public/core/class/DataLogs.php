 <?php
 
 class DataLogs{
	// private static $_repertoiredeslogs = "../private/logs/";
	private static $_repertoiredeslogs = "logs/"; // test folder
	private static $_prefixfichier = "datalogs.";
	private static $_extensionfichier = ".txt";	
	private static $_retourchariot = "\n";
	private static $_separator = "|";
	private static $_currentFileKey = false;
	// fichiers autorises a etres crees automatiquement si absents
	// verifiez le CHMOD du dossier $_repertoiredeslogs
	private static $_files = ["errors" => [],"visites" => []];

	public function __construct(){
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
	
	/**
	 * writeToLogs
	 *
	 * @param  mixed $key
	 * @param  mixed $texte string with Tabulation spaces
	 * @return void
	 */	
	static public function writeToLogs($key,$texte,$caller=["fichier?","fonction?","ligne?"]){
		if(self::set_currentFileAndKey($key)){
			$fullpathfilename = self::$_currentFileKey['fullpathfilename'];
			
			if($key === 'errors'){
				foreach($caller as $key){
					$callerstring = isset($callerstring) ? $callerstring . "," . $key : $key;
				}
				$callerstring = "	(".$callerstring.")";
			}

			// si le fichier existe
			if(file_exists($fullpathfilename)){
				// on ouvre le fichier en ecriture
				$currentFileOpened = fopen($fullpathfilename, 'a+');
				// je met du texte
				$message = self::get_promptLog() . $texte . ($callerstring??"")  .self::$_retourchariot;
				$message = utf8_encode($message);
				fwrite($currentFileOpened, $message);
				// je ferme le fichier !
				fclose($currentFileOpened);
				return self::$_currentFileKey;
			}
		}
		self::unset_currentFileAndKey();
		return false;
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
			// creating file
			$currentFileOpened = fopen($fullpathfilename, 'a+');
			if(file_exists($fullpathfilename)){
				self::writeToLogs($key, "Création du fichier.",[__FILE__,__FUNCTION__,__LINE__]);
				fclose($currentFileOpened);
			}
			else {
				Fun::print_air($fullpathfilename . " n'a pas été crée ! ERREUR !!! [".__FUNCTION__.":".__LINE__."]");
			}
		}
	}

	static private function get_promptLog(){
		return date('Y-m-d H:i:s').self::$_separator;
	}
}
