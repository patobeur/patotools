<?php 
	$currentVue = $this->getfilecontent( ROOTS['controllers'].self::$_pageCurrent."/vues/".self::$_pageCurrent.".php");
	$currentVue = str_replace("#CONTENTS#", '', $currentVue);
