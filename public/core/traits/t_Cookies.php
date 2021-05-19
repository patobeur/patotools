<?php
trait t_Cookies {	
	private static $cookiename ="barrecode";
	static function _getCookie(){
		if(!isset($_SESSION['user']['session'])){
			$cookie = isset($_COOKIE[self::$cookiename]) ? $_COOKIE[self::$cookiename] : false;
			if($cookie && isset($cookie['Token']) && isset($cookie['lastconnect'])){
				if(!empty($cookie['Token']) && !empty($cookie['lastconnect'])){
					// hack washin
					$token = Fun::get_clean($cookie['Token']);
					$lastconnect = Fun::get_clean($cookie['lastconnect']);
					$cleancookie = [$token,$lastconnect]; // supposed clean
					// test length
					// to do
					User::loginByCookieToken($cleancookie);
				}
			}
		}
	}
	static function _setCookie($datas=[]){
		setcookie(self::$cookiename."[lastconnect]", $datas[0], time() + 14400, '/', $_SERVER['HTTP_HOST'], false, true);
		setcookie(self::$cookiename."[Token]", $datas[1], time() + 14400, '/', $_SERVER['HTTP_HOST'], false, true);
	}
	public static function _unsetCookie(){
		setcookie(self::$cookiename."[lastconnect]", '', time() - 1, '/', $_SERVER['HTTP_HOST'], false, true);
		setcookie(self::$cookiename."[Token]", '', time() - 1, '/', $_SERVER['HTTP_HOST'], false, true);
		setcookie(self::$cookiename, '', time() - 1, '/', $_SERVER['HTTP_HOST'], false, true);
	}
}
