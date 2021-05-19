<?php
	/**
	 * @param string $serviceName nom du service demandé
	 */
	class RequestAPI {
		private $_user = false;
		private $_fresh_token = false; // Jeton (token from glpi if auth is ok)
		private $_apiUrl = GLPIURL;
		private $_usertoken; // Jeton personnel
		private $_apptoken; // Jeton d'application (app_token)
		
		private $_user_ip = false;
		private $_server_ip = false;
		private $_server_port = false;
		private $_glpiProfile = false;
		private $_additem = [];

    function __construct() {

			$this->_user_ip = $_SERVER['REMOTE_ADDR'];
			$this->_server_ip = $_SERVER['SERVER_ADDR'];
			$this->_server_port = $_SERVER['SERVER_PORT'];
			
			if (!empty($_SESSION['user']['player']['glpi'])){
				$this->_usertoken = $_SESSION['user']['player']['glpi']['usertoken'];
				$this->_apptoken = $_SESSION['user']['player']['glpi']['apptoken'];
					$_SESSION['GLPI'] = [
						'_usertoken' => $this->_usertoken,
						'_apptoken' => $this->_apptoken,
						'_fresh_token' => $this->_fresh_token,
						'_user_ip' => $this->_user_ip,
						'_server_ip' => $this->_server_ip,
						'_server_port' => $this->_server_port,
						'_apiUrl' => $this->_apiUrl,
						'header' => [],
						// 'items' => $this->get_AllItems(),
						// 'item' => $this->get_ItemsBySerial(),
 						// 'items' => $this->get_AllItemsByType('Computer')
					];

				$this->get_FreshToken(); // je demande un token a glpi

				if ($this->_fresh_token) {
					$_SESSION['GLPI'] = [
						'_fresh_token' => $this->_fresh_token,

					];
					//$header = $this->get_InitHeader('application/json',$this->_usertoken,$this->_apptoken);

					$this->_glpiProfile = $this->get_MyProfiles();
					$this->_additem[] = ['Add_Item' => $this->Add_Item()];
					$this->_additem[] = ['get_AllItemsByType' => $this->get_AllItemsByType('Computer')];

					$_SESSION['GLPI']['glpiProfile'] = $this->_glpiProfile;
					$_SESSION['GLPI']['additem'] = $this->_additem;

					$_SESSION['GLPI']['count'] = isset($_SESSION['GLPI']['count']) 
						? $_SESSION['GLPI']['count']++ 
						: 1;
				}
			}
		}
		// -get_FreshToken----------------------------------------------------------------------------------
		private function get_FreshToken() {
			$header = $this->get_InitHeader('application/json',$this->_usertoken,$this->_apptoken);
			$_SESSION['GLPI']['header'][__FUNCTION__] = $header;
			
			$respons_result = $this->get_CurlInit(
				$this->_apiUrl.'/initSession/',
				$header
			);

			$this->_fresh_token = false;
			if (!empty($respons_result)){
				$obj = json_decode($respons_result,true);
				if (!empty($obj['session_token'])){
					$this->_fresh_token = $obj['session_token'];
				}
			}
		}


		// -------------------
		
		private function Add_Item($name="PC BioSeed2",$serial="R90PHLN") {
			$init_Curl  = curl_init();
			$item = [
				"input"=>[
					"name"=>"$name",
					"serial"=>"$serial"
				]
			];
			$url = $this->_apiUrl.'/Computers/';
			// $url .= '?name=ordi01&serial=11234501';
			$header = [
				('Content-Type: application/json'),
				('Session-Token: '.$this->_fresh_token),
				('App-Token: '.$this->_apptoken)
				,(json_encode($item))
			];
			$_SESSION['GLPI']['header'][__FUNCTION__] = $header;
			curl_setopt($init_Curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($init_Curl, CURLOPT_POSTFIELDS ,json_encode($item));
			curl_setopt($init_Curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($init_Curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($init_Curl, CURLOPT_URL, $url);
			curl_setopt($init_Curl, CURLOPT_VERBOSE, true);
			curl_setopt($init_Curl, CURLOPT_RETURNTRANSFER, true);
			set_time_limit(3); // limite pour le délai de réponse
			// curl_setopt($init_Curl,CURLOPT_TIMEOUT,1000);
			$respons_result = curl_exec($init_Curl);
			//	Fun::print_air(['header' => $header, 'url' => $url, 'fields' => json_encode($item)],__FUNCTION__);
			curl_close($init_Curl);
			if (!empty($respons_result)){
				$obj = json_decode($respons_result,true);
				return $obj;
			}
			else {
				//
			}
		}
	// ----------------
		// -TOOLS----------------------------------------------------------------------------------
		private function get_InitHeader($contenttype,$usertoken,$apptoken) {
			return [
				('Content-Type: '.$contenttype),
				('Authorization: user_token '.$usertoken),
				('App-Token: '.GLPITOKEN)
			];
		}
		private function get_SessionHeader($contenttype,$sessiontoken,$apptoken) {
			return [
				('Content-Type: '.$contenttype),
				('Session-Token: '.$sessiontoken),
				('App-Token: '.$apptoken)
			];
		}
		// ------------------------------------------------------------------------------------
		private function get_CurlInit($url,$header){
			$init_Curl  = curl_init();
			// $_SESSION['GLPI']['header'][__FUNCTION__][] = $header;
			curl_setopt($init_Curl, CURLOPT_HTTPHEADER, $header);
			// curl_setopt($init_Curl, CURLOPT_SSL_VERIFYHOST, false);
			// curl_setopt($init_Curl, CURLOPT_SSL_VERIFYPEER, false);
			// curl_setopt($init_Curl, CURLOPT_VERBOSE, true);
			curl_setopt($init_Curl, CURLOPT_URL, $url);
			curl_setopt($init_Curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($init_Curl, CURLOPT_CONNECTTIMEOUT_MS, 1000);
			// set_time_limit(3); // limite pour le délai de réponse
			// curl_setopt($init_Curl,CURLOPT_TIMEOUT,1000);
			$respons_result = curl_exec($init_Curl);
			curl_close($init_Curl);
			$_SESSION['cms']['GLPI'] = !empty($respons_result) ? $respons_result : '<pan style="color:red">Error</pan>';
			return $respons_result;
		}


















		/**
		 * Return all the profiles associated to logged user.
		 * Method: GET
		 */
		private function get_MyProfiles() {
			$header = $this->get_SessionHeader('application/json',$this->_fresh_token,$this->_apptoken);
			$_SESSION['GLPI']['header'][__FUNCTION__] = $header;

			$respons_result = $this->get_CurlInit(
				$this->_apiUrl.'/getMyProfiles/',
				$header
			);
			return !empty($respons_result) ? json_decode($respons_result,true) : false;
		}
		/**
		 * Return all the profiles associated to logged user.
		 * Method: GET
		 */
		private function get_AllItemsByType($type) {
			if (!empty($type)){
				$init_Curl  = curl_init();			
				$url = $this->_apiUrl.'/'.$type.'?expand_dropdowns=true';
				// $_SESSION['GLPI'][] = ['testf' => $url];
				$header =  $this->get_SessionHeader('application/json',$this->_fresh_token,$this->_apptoken);
				curl_setopt($init_Curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($init_Curl, CURLOPT_URL, $url);
				curl_setopt($init_Curl, CURLOPT_VERBOSE, true);
				curl_setopt($init_Curl, CURLOPT_RETURNTRANSFER, true);
				set_time_limit(3); // limite pour le délai de réponse
				// curl_setopt($init_Curl,CURLOPT_TIMEOUT,1000);
				$respons_result = curl_exec($init_Curl);
				
				curl_close($init_Curl);
				if (!empty($respons_result)){
					$obj = json_decode($respons_result,true);
					return $obj;
				}
				else {
					//
				}
			}
		}
		/**
		 * Return all the profiles associated to logged user.
		 * Method: GET
		 */
		private function get_AllItems() {
				$init_Curl  = curl_init();			
				$url = $this->_apiUrl.'/Computer/?expand_dropdowns=true';
				// $_SESSION['GLPI'][] = ['testf' => $url];
				$header =  $this->get_SessionHeader('application/json',$this->_fresh_token,$this->_apptoken);
				curl_setopt($init_Curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($init_Curl, CURLOPT_URL, $url);
				curl_setopt($init_Curl, CURLOPT_VERBOSE, true);
				curl_setopt($init_Curl, CURLOPT_RETURNTRANSFER, true);
				set_time_limit(3); // limite pour le délai de réponse
				// curl_setopt($init_Curl,CURLOPT_TIMEOUT,1000);
				$respons_result = curl_exec($init_Curl);
				
				curl_close($init_Curl);
				if (!empty($respons_result)){
					$obj = json_decode($respons_result,true);
					return $obj;
				}
				else {
					//
				}
		}
		/**
		 * 
		 * Method: PUT or PATCH
		 */
		private function get_UpdateItemsByType($type) {
			if (!empty($type)){
				$init_Curl  = curl_init();			
				$url = $this->_apiUrl.'/'.$type.'?expand_dropdowns=true';
				$_SESSION['GLPI'][$type] = $url;
				$header =  $this->get_SessionHeader('application/json',$this->_fresh_token,$this->_apptoken);
				$_SESSION['GLPI']['header'][__FUNCTION__] = $header;
				curl_setopt($init_Curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($init_Curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($init_Curl, CURLOPT_URL, $url);
				curl_setopt($init_Curl, CURLOPT_VERBOSE, true);
				curl_setopt($init_Curl, CURLOPT_RETURNTRANSFER, true);
				$respons_result = curl_exec($init_Curl);
				
				curl_close($init_Curl);
				if (!empty($respons_result)){
					$obj = json_decode($respons_result,true);
					return $obj;
				}
			}
		}

		

	}

// * [Glossary](#glossary)
// * [Important](#important)
// * [Init session](#init-session)
// * [Kill session](#kill-session)
// * [Lost password](#lost-password)
// * [Get my profiles](#get-my-profiles)
// * [Get active profile](#get-active-profile)
// * [Change active profile](#change-active-profile)
// * [Get my entities](#get-my-entities)
// * [Get active entities](#get-active-entities)
// * [Change active entities](#change-active-entities)
// * [Get full session](#get-full-session)
// * [Get GLPI config](#get-glpi-config)
// * [Get an item](#get-an-item)
// * [Get all items](#get-all-items)
// * [Get sub items](#get-sub-items)
// * [Get multiple items](#get-multiple-items)
// * [List searchOptions](#list-searchoptions)
// * [Search items](#search-items)
// * [Add item(s)](#add-items)
// * [Update item(s)](#update-items)
// * [Delete item(s)](#delete-items)
// * [Special cases](#special-cases)
// * [Errors](#errors)
// * [Servers configuration](#servers-configuration)



// reponse bonus :

// $url=$apiurl ."/listesearchoptions/Ticket";
// renvoie la liste des champs de recherche du ticket avec les ID.
// ***************
// il faut mettre les critères dans l'url :


// $url=$wsurl."Search/Ticket?Content-Type=%20application/json&app_token=".$wsapptoken."&session_token=".$session&criteria[0][link]=AND&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=notclosed
// &criteria[1][link]=AND&criteria[1][field]=1&criteria[1][searchtype]=contains&criteria[1][value]=".$leTitreDeMonTicket
// https://forum.glpi-project.org/viewtopic.php?id=170066

?>