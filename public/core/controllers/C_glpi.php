<?php 
	$currentVue = $this->getfilecontent( ROOTS['controllers'].'/vues/glpi.php');


	
					// ------------------
					// GLPI CHANTIER -----

					$glpiTouch = !empty($_SESSION['user']['player']['glpi']) ? new RequestAPI() : false;
					//$glpiTouch = false;
					
					// GLPI CHANTIER -----
					// ------------------





	$currentVue = str_replace("#CONTENTS#", 'La Page : '.self::$_pageCurrent, $currentVue);
