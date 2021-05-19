<?php 
// Use Class Page
	$currentVue = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/"."profilnav.php");
	$font_logged = '<i class="fas fa-user-circle"></i>';
	$currentVue = str_replace("#HREF#", '?profil', $currentVue);
	$currentVue = str_replace("#TITLE#", 'Profil de '.$_SESSION['user']['player']['pseudo'], $currentVue);
	$currentVue = str_replace("#AWESOME#", $font_logged, $currentVue);
