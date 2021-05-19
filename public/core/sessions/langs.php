<?php
	define('ISOLANG', [
			0 => 'fr_FR'
			,1 => 'us_US'
			,2 => 'es_ES'
		]
	);
	define(
		'LANG', 0
	);
	$phraseParLangues = [
		'100' => [
			'0' => 'Allez à la page'
			,'1' => 'Go to page'
			,'2' => 'Ir a la pagina'
		]
		,'200' => [ // Forms
			'0' => 'Envoyer'
			,'1' => 'Submit'
			,'2' => 'Enviar'
		]
		,'201' => [ // Forms
			'0' => 'Choississez la '
			,'1' => 'Select the '
			,'2' => 'Seleccione la '
		]
		,'600' => [
			'0' => 'requete sql mal formatée'
			,'1' => 'malformed sql request'
			,'2' => 'malformed sql request'
		]
		,'601' => [
			'0' => 'réponse vide'
			,'1' => 'empty answer'
			,'2' => 'repuesta vacia'
		]
		,'602' => [
			'0' => 'Pas de données'
			,'1' => 'No Datas !'
			,'2' => 'No hay datos !'
		]
		,'900' => [
			'0' => 'BDD ERREUR !'
			,'1' => 'BDD ERROR !'
			,'2' => 'BDD ERROR !'
		]
	];
	$dictionnaire = [];
	foreach($phraseParLangues as $num => $lesphrases){
			$dictionnaire[$num] = ''.$lesphrases[LANG].'';
	}
	define('DICO', $dictionnaire);
