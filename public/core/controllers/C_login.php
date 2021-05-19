<?php 

	if ($currentVue = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent.".php")
			AND $vue_form = $this->getfilecontent(ROOTS['controllers'].self::$_pageCurrent."/vues/"."login_formulaire.php"))
	{
		$vue_form = str_replace("#VALUE_LOGIN#", $_SESSION['user']['player']['login'], $vue_form);
		$vue_form = str_replace("#VALUE_PASSWORD#", '', $vue_form);
		
		$currentVue = str_replace("#CONTENT_LOGIN#", $vue_form, $currentVue);
		$currentVue = $vue_form;
	}
	else
	{
		$currentVue = $this->getfilecontent(ROOTS['vues']."404.php");
	}
