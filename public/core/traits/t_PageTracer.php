<?php
trait t_PageTracer {
	// save last page visited to bdd
	static function tryClickLog($test){
		if ( !empty($_SESSION['user']['session']['tokenCode']) ) {

			$userexiste = Database::queryBindSelect(
				"SELECT * FROM ".TABLES['lastdate'].
				" WHERE ".TABLES['lastdate'].".membres_id = ?",
				[[$_SESSION['user']['session']['userID']]],
				'(tryClickLog '.__FUNCTION__
			);
			if(!empty($userexiste[0])){				
				$maj = Database::queryBindUpdate(
					"UPDATE ".TABLES['lastdate']." SET ".
					TABLES['lastdate'].".dateheure = ?, ".
					TABLES['lastdate'].".currentpage = ?".
					" WHERE ".TABLES['lastdate'].".membres_id = ?",
					[[
						CONF['lastclickdate'],
						$_SERVER['REMOTE_ADDR'] . "@" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://". $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], //Page::$_pageCurrent.'---',
						$_SESSION['user']['session']['userID']
					]],
					'(tryClickLog '.__FUNCTION__
				);
			}
			else {
				// nouvel enregistrement
				$maj = Database::queryBindInsert(
					"INSERT INTO ".TABLES['lastdate']." (dateheure,currentpage,membres_id) VALUES (?, ?, ?)",
					[[
						CONF['lastclickdate'],
						(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".  $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], //Page::$_pageCurrent.'---',
						$_SESSION['user']['session']['userID']
					]],
					'(tryClickLog '.__FUNCTION__
				);
			}
		}
	}
}