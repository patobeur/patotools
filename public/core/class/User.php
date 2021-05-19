<?php

class User{ 
	use t_Cookies;
	static $_user = [
		'pseudo' => '',
		'login' => '',
		'token' => '',
		'serveurip' => '',
		'userip' => '',
		'permaconnect' => false
	];
	static $_userProfilForm = [
		'userID' => '',
		'userName' => '',
		'userEmail' => '',
		'userPass' => '',
		'userStatus' => '',
		'tokenCode' => '',
		'accred' => '',
		'lastconnect' => '',
		'userip' => '',
		'glpi_user' => '',
		'glpi_app' => ''
	];
	public function __construct(){
		if(Database::get_checkBdd()){
	
			if(!isset($_SESSION['user']['player']['token'])){
				if (trait_exists('t_Cookies')) {
					t_Cookies::_getCookie();
				}
				else {
					DataLogs::writeToLogs("errors","le trait t_Cookies n'existe pas!!!",[__FILE__,__FUNCTION__,__LINE__]);
				}
			}
			$this->tryLogin();
		}
	}

	public function get_UserInfos(){
		return $this->_user;
	} 

	private function tryLogin(){
		if(($_SESSION['user']['current_page'] === 'login') AND !empty($_POST['login']) AND !empty($_POST['password'])){
			self::$_user['login'] = Fun::get_clean($_POST['login']);
			self::$_user['password'] = md5(Fun::get_clean($_POST['password']));
			self::$_user['permaconnect'] = (isset($_POST['permaconnect']) && $_POST['permaconnect'] === "yes");

			Page::$_pageCurrent = $_SESSION['user']['current_page'];
			$_SESSION['user']['try']++;
			
			$this->login(self::$_user['login'],self::$_user['password'],self::$_user['permaconnect'],__CLASS__);
		}
	}

	private static function _majAccountAfterLoginIn($userRow,$callingParent="indéfinie"){
		$userRowId = $userRow['userID'];
		$retour = false;
		$lastconnect = Fun::dateDuJour('classic');
		$tokenCode =  Fun::generateToken();
		$yourip = $_SERVER['REMOTE_ADDR'];

		try{
			$retour = Database::queryBindUpdate(
				"UPDATE ".TABLES['users']." SET lastconnect=?, tokenCode=? , userip=? WHERE userID=?",
				[[$lastconnect,$tokenCode,$yourip,$userRowId]],
				__FUNCTION__
			);
		}
		catch(PDOException $ex){
			die(__FUNCTION__);
		}
		if( (self::$_user['permaconnect'] || isset($_COOKIE['barrecode']['token']))
		){
			if (trait_exists('t_Cookies',false)) {
				t_Cookies::_setCookie([$lastconnect,$tokenCode,$yourip,$userRowId]);
			}
			else {
				DataLogs::writeToLogs("errors","le trait t_Cookies n'existe pas!!!",[__FILE__,__FUNCTION__,__LINE__]);
			}
		}
		return $retour;
	}

	private static function _setPseudo(){
		self::$_user['pseudo'] = $_SESSION['user']['player']['pseudo'];
	}
	private static function set_UserSession($userRow){
		$_SESSION['user'] = [
			'debug' => ROOTS['debug'],
			'distant' => ROOTS['distant'],
			'hit' => $_SESSION['user']['hit'],
			'session' => [
				'userID' => $userRow['userID'],
				'userName' => $userRow['userName'],
				'userEmail' => $userRow['userEmail'],
				// 'userPass' => $userRow['userPass'],
				'userStatus' => $userRow['userStatus'],
				'tokenCode' => Fun::generateToken(),
				'accred' => $userRow['accred'],
				'lastconnect' => $userRow['lastconnect'],
				// 'userip' => $userRow['userip'],
				// 'serverip' => $_SERVER['SERVER_ADDR'],
				// 'glpi_user' => $userRow['glpi_user'],
				// 'glpi_app' => $userRow['glpi_app'],
			],
			'player' => [
				'ip' => $_SERVER['REMOTE_ADDR'],
				'serverip' => $_SERVER['SERVER_ADDR'],
				'pseudo' =>  $userRow['userName'],
				'login' => $userRow['userEmail'],
				'token' => Fun::generateToken(),
				'glpi' => [
					'userip' => $_SERVER['REMOTE_ADDR'],
					'serverip' => $_SERVER['SERVER_ADDR'],
					'usertoken' => $userRow['glpi_user'],
					'apptoken' => $userRow['glpi_app']
				],
				'accred' => $userRow['accred'],
				'login' => $userRow['userEmail']
			],
			'last_page' => $_SESSION['user']['current_page'],
			'current_page' => 'accueil'
		];
	}
	private static function setConnectionOn($userRow,$callingParent="indéfinie"){
		// Fun::print_air('connecting people !');
		// Fun::print_air("Bonjour ".$userRow['userName']);
		User::set_UserSession($userRow);
		User::_setPseudo();
		DataLogs::writeToLogs("visites",'Connexion de: userID:'.$userRow['userID'] . ' userIP:'.$_SERVER['REMOTE_ADDR'] . ' email:'.$userRow['userEmail'],[__FILE__,__LINE__]);
		User::_majAccountAfterLoginIn($userRow,$callingParent.'('.__FUNCTION__);
		Fun::refresh(0,WEBSITE['siteurl'], true, 404 ,false);
	}
	public function login($email,$upass,$permaconnect,$callingParent="indéfinie"){
		try{
			$userRow = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['users']." WHERE ".TABLES['users'].".userEmail = ? AND ".TABLES['users'].".userPass = ?",
				[[$email,$upass]],
				$callingParent.'( '.__FUNCTION__
			);

			// ICI REFAIRE LE TRAITEMENT C'EST ASSEZ SAL !!!
			// ici on sait que le user existe !!!!
			// s'il n'est pas unique cela bloque la connection
			// s'il n'est pas active cela bloque la connection

			$userRow = $userRow!=false ? $userRow[0] : $userRow;
			if($userRow){
				if($userRow['userStatus'] === '1'){
					if($userRow['userPass'] === $upass){	
						$this->setConnectionOn($userRow,__FUNCTION__);
						
					}
					else{
						// header("Location: ?login&errror");
						DataLogs::writeToLogs("visites",'Login Erreur. userIP:'.$_SERVER['REMOTE_ADDR'] . " email:".$email,[__FILE__,__FUNCTION__,__LINE__]);
						// exit;
					}
				}
				else{
					// header("Location: ?login&inactive");
					DataLogs::writeToLogs("visites",'Compte innactif. userIP:'.$_SERVER['REMOTE_ADDR'] . " email:".$email,[__FILE__,__FUNCTION__,__LINE__]);
				} 
			}
			else{
				// header("Location: ?login&error");
				// exit;
			}  
		}
		catch(PDOException $ex){
			DataLogs::writeToLogs("errors",$ex->getMessage(),[__FILE__,__LINE__]);
			Fun::stop('login erreur',__FUNCTION__);
			Fun::stop("onerror",__FUNCTION__);
			die();
		}
	}

	// COOKIES
	// ajoutée pour le trait cookie
	public static function loginByCookieToken($cookie){
		if (trait_exists('t_Cookies',false)) {
			try{
				self::$_user['permaconnect'] = true;
				$userRow = Database::queryBindSelect(
					"SELECT * FROM ".TABLES['users']." WHERE ".TABLES['users'].".tokenCode != '' AND ".TABLES['users'].".tokenCode = ? AND ".TABLES['users'].".userip = ? AND ".TABLES['users'].".lastconnect = ?",
					[[Fun::get_clean($cookie[0]),Fun::get_ip_address(),Fun::get_clean($cookie[1])]],
					__FUNCTION__
				);
			}
			catch(PDOException $ex){
				Fun::stop("Connexion Cookie impossible !",__FUNCTION__);
			}
			if(is_array(($userRow)) and count($userRow)===1){
				$userRow = $userRow!=false ? $userRow[0] : $userRow;
				User::setConnectionOnByCookie($userRow,__FUNCTION__);
			}
		}
		else {
			DataLogs::writeToLogs("errors","le trait t_Cookies n'existe pas!!!",[__FILE__,__FUNCTION__,__LINE__]);
		}
	}
	// ajoutée pour le trait cookie
	private static function setConnectionOnByCookie($userRow,$callingParent="indéfinie"){
		if (trait_exists('t_Cookies',false)) {
			User::set_UserSession($userRow);
			User::_setPseudo();
			DataLogs::writeToLogs("visites",'Connexion By Cookie de: userID:'.$userRow['userID'] . ' userIP:'.$_SERVER['REMOTE_ADDR'] . ' email:'.$userRow['userEmail'],[__FILE__,__LINE__]);
			User::_majAccountAfterLoginIn($userRow,$callingParent.'('.__FUNCTION__);
		}
		else {
			DataLogs::writeToLogs("errors","le trait t_Cookies n'existe pas!!!",[__FILE__,__FUNCTION__,__LINE__]);
		}
	}
}
