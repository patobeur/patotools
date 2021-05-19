<?php
	class ImportDatabase {	
		use t_Formulary;
		// private static $_firstImport = false; //  <<<<<-Db connection
		private static $_conectSource = false; //  <<<<<-Db connection Source
		private static $_conectDestin = false; //  <<<<<-Db connection Destin

		private static $_importation = true;
		private static $_insertation = true;
		private static $_lecture = true;

		private static $_sourceNumBdd = 1;
		private static $_destinationNumBdd =0;

		private static $_choixBdd = ['membres','articles'];
		private static $_choixCdT = 'articles';
		private static $_CdT = [ 
			'choix'=> 'articles',// ChoixDesTables
			'articles'=> [
				'compare' => ['glpi'],
				'from'=> [
					"numbdd" => 2,
					"numtab" => 1
				],
				'to'=> [
					"numbdd" => 0,
					"numtab" => 1
				]
			],
			'membres'=> [
				'compare' => ['barrecode'],
				'from'=> [
					"numbdd" => 1,
					"numtab" => 5
				],
				'to'=> [
					"numbdd" => 0,
					"numtab" => 10
				]
			]
		];
		private static $_where = [
			"membres" => [
				'prenom' => [
					'<>' => [
						"' '"
						,"''"
					]
				],
				'annee' => [
					// '=' => [2022],
					'<>' => [1719,1820,1920,1921,2022]
				],
			],
			"articles" => []
		];

		// private static $_NumTab = 0;
		// private static $_importationCheck = false;

		public function __construct(){
			// nada ??
			// used in C_importsql.php
			self::$_choixCdT = self::$_choixBdd[self::$_sourceNumBdd];
		}
		public static function get_lecture(){
			return self::$_lecture;
		}
		public static function get_importation(){
			return self::$_importation;
		}

		public static function getWhere(){
			return self::$_where[self::$_choixCdT];
		}
		/**
		 * get_SourceDatas
		 *
		 * @param  mixed $numbdd
		 * @param  mixed $numtab
		 * @param  mixed $where
		 * @return void
		 */
		public static function get_SourceDatas($numbdd,$numtab,$where=false){
			// est ce permis de se connecter ?
			if (self::$_importation){
				// est ce vraiment possible de se connecter ?
				if (self::is_DatabaseAccessible($numbdd,'source')){
					// si oui on retourne le resultat de la requete
					return self::readSourceDatas($numtab,$where);
				};
			}
		}

		/**
		 * get_DestinationDatas
		 *
		 * @param  mixed $numbdd
		 * @param  mixed $numtab
		 * @param  mixed $where
		 * @return void
		 */
		public static function get_DestinationDatas($numbdd,$numtab,$where=false){
			// est ce permis de se connecter ?
			if (self::$_importation){
				// est ce vraiment possible de se connecter ?
				if (self::is_DatabaseAccessible($numbdd,'destination')){
					// si oui on retourne le resultat de la requete
					return self::readDestinationDatas($numtab,$where);
				};
			}
			
		}

		/**
		 * readDestinationDatas
		 *
		 * @param  mixed $numtab
		 * @param  mixed $where
		 * @return void
		 */
		private static function readDestinationDatas($numtab,$where=false){
			if(self::$_conectDestin){
				$sql = "SELECT * FROM ".DB['ImportSql'][self::$_destinationNumBdd]['DBtabs'][$numtab][0];//.$where;
				$firstImp = self::$_conectDestin->prepare($sql);
				// $firstImp->bindParam($i+1, $value[$i], PDO::PARAM_INT);
				$firstImp->execute();
				$allRows = $firstImp->fetchall(PDO::FETCH_ASSOC);
				return [true,$allRows];
			}
		}
		
		/**
		 * readSourceDatas
		 *
		 * @param  mixed $numtab
		 * @param  mixed $where
		 * @return void
		 */
		private static function readSourceDatas($numtab,$where=false){

			// dev comments 
			$_SESSION['cms']['dev'][__FUNCTION__] = [
				$numtab
				,$where
				,DB['ImportSql'][self::$_sourceNumBdd]['DBtabs'][$numtab][0].$where
			];
			
			if(self::$_conectSource){
				// where creation
				// $besoins = "id, serial as barrecode, name as nom_article, autoupdatesystems_id as os, groups_id_tech as cpumhz, comment, is_dynamic as valide";
				$besoins = "*";
				$sql = "SELECT ".$besoins." FROM ".DB['ImportSql'][self::$_sourceNumBdd]['DBtabs'][$numtab][0].$where;
				$firstImp = self::$_conectSource->prepare($sql);
				// $firstImp->bindParam($i+1, $value[$i], PDO::PARAM_INT);
				$firstImp->execute();
				$allRows = $firstImp->fetchall(PDO::FETCH_ASSOC);
				if ($allRows){
					return [true,self::cleanresult($allRows,$numtab)];

				}

			}
		}
		private static function cleanresult($allRows,$numtab){
			//	Fun::print_air(DB['ImportSql'][self::$_sourceNumBdd]['DBtabs'][$numtab][0]);
			return $allRows;
		}
		private static function wheremaker($where){
			// $whereString = " WHERE ";
			// to do
			// return $whereString;
		}
		/**
		 * is_DatabaseAccessible
		 *
		 * @param  mixed $numbdd need intval() of bdd
		 * @param  mixed $target need (only 'source' OR 'destination')
		 * @return void
		 */
		private static function is_DatabaseAccessible($numbdd, $target){
			if (DB['ImportSql'][$numbdd]['DBuser'] && DB['ImportSql'][$numbdd]['DBuser'] != "") {
				try{
					if (
						$conectOuterBdd = new PDO(
							"mysql:".
							"host=".DB['ImportSql'][$numbdd]['DBhost'].";".
							"dbname=".DB['ImportSql'][$numbdd]['DBname'].";".
							"charset=".DB['ImportSql'][$numbdd]['DBcharset'],
							DB['ImportSql'][$numbdd]['DBuser'],
							DB['ImportSql'][$numbdd]['DBpass']
						)
					) {
						if ($target==="source"){
							self::$_conectSource = $conectOuterBdd;
							self::$_sourceNumBdd = $numbdd;
						} elseif ($target==="destination"){
							self::$_conectDestin = $conectOuterBdd;
							self::$_destinationNumBdd = $numbdd;
						}
						return true; 
					} else {
						Fun::print_air(DB['ImportSql'][$numbdd]['DBuser'].'@'.DB['ImportSql'][$numbdd]['DBhost']);
						die("Erreur n°564654 ???");
					}
				}
				catch(PDOException $e){
					die(Fun::get_SqlErreurTexte($e,"La connexion Sql",[__FILE__,__FUNCTION__,__LINE__]).' Jetez un oeil dans le fichier definitions');
				}
			}
		}	
		

		/**
		 * get_colstypes
		 *
		 * @param  mixed $numbdd
		 * @param  mixed $numtab
		 * @return void
		 */
		public static function get_colstypes($numbdd,$numtab,$target){

			$sql = " SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '". DB['ImportSql'][$numbdd]['DBtabs'][$numtab][0] . "'";
	
			if ($target==="source"){
				$firstImp = self::$_conectSource->prepare($sql);
			} else {
				$firstImp = self::$_conectDestin->prepare($sql);
			} 

			$firstImp->execute();
			$allRows = $firstImp->fetchall(PDO::FETCH_ASSOC);
			$sourcecol = [];
			if($allRows && is_array($allRows) && count($allRows)>0){
				foreach($allRows as $key => $value){
					$sourcecol[$value['COLUMN_NAME']] = [$value['COLUMN_NAME'],$value['DATA_TYPE']];
				}
				return $sourcecol;
			}
			return false;
		}
	}