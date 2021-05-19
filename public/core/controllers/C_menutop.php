<?php 
	$currentVue = $this->getfilecontent( ROOTS['controllers']."menutop/vues/menutop.php");
	//	Fun::print_air($this->_lesPages);
	$htmltopmenu = '<li><a href="'.WEBSITE['siteurl'].'" title="'.WEBSITE['logotitle'].'" class="brand"><img title="'.WEBSITE['logotitle'].'" alt="'.WEBSITE['logoalt'].'" src="'.WEBSITE['logo'].'"></a></li>';
	foreach($this->_lesPages as $cle => $valeurs){ // fr style
		if(!empty($valeurs['menu'])){
			$params = $valeurs['menu'];
			$htmltopmenu .='<li class="navlink '.$params['class'].(($params['url'] === "?".Page::$_pageCurrent) ? " activemenu" : "").'">';
			$htmltopmenu .='<a href="'.$params['url'].'" title="'.$params['urltitle'].'" class="'.$params['class'].'">'.$params['content'].'</a>';
			$htmltopmenu .='</li>'.PHP_EOL;
		}
	}
	$currentVue = str_replace("#MENUTOP#", $htmltopmenu, $currentVue);
