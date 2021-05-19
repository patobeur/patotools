<?php
// Global Constants
$distan1 = "barrecode.pat"; 				// prod server 
$distan2 = "dev.barrecode.pat"; 				// server de test patobeur 
$redirecturl = "https://github.com/patobeur/barrecode"; // redirect si bug 
switch ($_SERVER['HTTP_HOST']){
	case $distan1: 										// distant vm server
		$DBhost = "127.0.0.1";
		$DBname = "patobarre";
		$DBuser = "root";
		$DBpass = "";
		$TDprefix = "pat_"; 							// prefixes des tables
		$glpiUrl = ''; // not updated
		$root = '';
		$core = 'core/';
		$private = '../private/';
		$prefixfilelogs = $TDprefix;
		$debug = true;
		$distant = false;
		$impSql = [
			0 => [
				'DBhost' => $DBhost,
				'DBname' => $DBname,
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [ // tables authorisées
					[false,false],
					[$TDprefix.'articles',false],
					[$TDprefix.'contents',false],
					[$TDprefix.'utilisateurs',false],
					[$TDprefix.'formdatas',false],
					[$TDprefix.'formdatas_pivot',false],
					[$TDprefix.'formgroups',false],
					[$TDprefix.'incidents',false],
					[$TDprefix.'lastdate',false],
					[$TDprefix.'locations',false],
					[$TDprefix.'membres',false],
					[$TDprefix.'pages',false],
					[$TDprefix.'retards',false]
				],
			],
			1 => [ // db 2
				'DBhost' => $DBhost,
				'DBname' => "barrecode",
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [
					[false,false],
					['bc_articles',false],
					['bc_incidents',false],
					['bc_key',false],
					['bc_locations',false],
					['bc_membres',false],
					['bc_utilisateurs',false],
					['bc_valide',false]
				]
			],
			2 => [ // db 3
				'DBhost' => $DBhost,
				'DBname' => "glpi",
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [
					[false,false],
					['glpi_computers',false],
					['glpi_users',false],
				],
			],
		];
	break;
	case $distan2: 					// local dev server
		$DBhost = "127.0.0.1";
		$DBname = "patobarre_dev"; // default
		$DBuser = "root";
		$DBpass = "";
		$TDprefix = "dev_";
		$glpiUrl = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		$root = '';
		$core = 'core/';
		$private = '../public/';
		$prefixfilelogs = $TDprefix;
		$debug = true;
		$distant = false;
		$impSql = [
			0 => [
				'DBhost' => $DBhost,
				'DBname' => $DBname,
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [ // tables authorisées
					[false,false],
					[$TDprefix.'articles',false],
					[$TDprefix.'contents',false],
					[$TDprefix.'utilisateurs',false],
					[$TDprefix.'formdatas',false],
					[$TDprefix.'formdatas_pivot',false],
					[$TDprefix.'formgroups',false],
					[$TDprefix.'incidents',false],
					[$TDprefix.'lastdate',false],
					[$TDprefix.'locations',false],
					[$TDprefix.'membres',false],
					[$TDprefix.'pages',false],
					[$TDprefix.'retards',false]
				],
			],
			1 => [
				'DBhost' => $DBhost,
				'DBname' => "barrecode",
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [
					[false,false],
					['bc_articles',false],
					['bc_incidents',false],
					['bc_key',false],
					['bc_locations',false],
					['bc_membres',false],
					['bc_utilisateurs',false],
					['bc_valide',false]
				],
			],
			2 => [
				'DBhost' => $DBhost,
				'DBname' => "glpi",
				'DBuser' => $DBuser,
				'DBpass' => $DBpass,
				'DBcharset' => 'utf8mb4',
				'DBtabs' => [
					[false,false],
					['glpi_computers',false],
					['glpi_computermodels',false]
				],
			]
		];
	break;
	default:
		// header('Location: '.$redirecturl);
		print_r("check Logs " . $_SERVER['HTTP_HOST']);
		die();
	break;
}

// APIREST GLPI
define('GLPIURL',$glpiUrl);
define('GLPITOKEN','xxxxxxxxxxxxxxxxx');

// DB
define(
	'DB', [
		'host' => $DBhost,
		'name' => $DBname,
		'user' => $DBuser,
		'pass' => $DBpass,
		// 'Imphost' => $ImpDBhost,
		// 'Impname' => $ImpDBname,
		// 'Impuser' => $ImpDBuser,
		// 'Imppass' => $ImpDBpass,
		'ImportSql' => $impSql,
		'engine' => 'InnoDB DEFAULT',
		'attributs' => 'utf8',
		'charset' => 'utf8mb4',
		'Impcharset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci',
		'prefix' => $TDprefix
	]
);
// TB
define('TABLES', [
		'articles' => DB['prefix'].'articles'
		,'contents' => DB['prefix'].'contents'
		,'formdatas' => DB['prefix'].'formdatas'
		,'formdatas' => DB['prefix'].'formdatas'
		,'formdatas_pivot' => DB['prefix'].'formdatas_pivot'
		,'formgroups' => DB['prefix'].'formgroups'
		,'incidents' => DB['prefix'].'incidents'
		,'lastdate' => DB['prefix'].'lastdate'
		,'locations' => DB['prefix'].'locations'
		,'membres' => DB['prefix'].'membres'
		,'pages' => DB['prefix'].'pages'
		,'retards' => DB['prefix'].'retards'
		,'users' => DB['prefix'].'utilisateurs'
	]
);

// CORE INFO
// Directories
define('ROOTS', [
	'debug' => $debug,
	'distant' => $distant,
	'root' => $root,
	'core' => $root.$core,
	'logs' => $private.'logs/',
	'prefixfilelogs' => $prefixfilelogs,
	'sessions' => $root.$core.'sessions/',
	'tools' => $root.$core.'tools/',
	'extra' => $root.$core.'extra/',
	'class' => $root.$core.'class/',
	'vues' => $root.$core.'vues/',
	'functions' => $root.$core.'functions/',
	'controllers' => $root.$core.'controllers/',
	'traits' => $root.$core.'traits/',
	'extphp' => '.php',
	'exthtml' => '.html',
	'less' => $root.'theme/less/',
	'css' => $root.'theme/css/',
	'js' => $root.'theme/js/',
	'img' => $root.'theme/img/'
]);

// Site index
define('SITEINDEX','login'); // default page index tant que pas loggué
// ACTIONS
define('WEBACTIONS', [
	'actionsParDefaut' => 'retard'
]);
// Site dataz
define('WEBSITE', [
	'header'=> 'Content-type: text/html; charset=UTF-8'
	,'siteurl' => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])
	,'head_title' => 'Codebarre App'
	,'index' => SITEINDEX // default page index
	,'indexloged' => 'actions' // default page si connecté
	,'actionsParDefaut' => 'retard'
	,'logo' => ROOTS['img'].'logo.png'
	,'logotitle' => 'PAT BARRECODE'
	,'logoalt' => 'logo et lien vers la page accueil'
]);
// useful data / handy tools
define('CONF',[
		'lastUsedId' => true, // rend persistent le dernier id utilisé par mysql si true
		'F' => '(F)', // txt perso pour marquer si une fonction est celle modifiée dans les logs
		'lastclickdate' => date("Y-m-d H:i:s"), // utile dans User Class function tryClickLog() qui stock la date du dernier click
	]
);




// <-- Erase all after this line when installation done ! / Supprimer a partir de cette ligne après installation
// to do :
// creation d'un test if file_exists($path.'/installation_starter.php') 
// creation d'un include($path."/installation_starter.php")
// creation d'une routine pour supprimer installation_starter.php après installation
// AUTO SELF INSTALLATION


define('INSTALLATION', [
	'active' => true, // installation auto activée
	'delete' => true, // en cas de manque de tables la base peut elle etre delete ?
	'redirect' => true,
	'maxtry' => 1, // installation auto activée
	'delay' => 15,
	'nom' => "Adminpatobeur", // admin
	'email' => "patobeur@patobeur.org",
	'password' => md5('patobeur'),
	'accred' => "99999",
	'nom2' => "AdminPat",
	'email2' => "test@patobeur.org",
	'password2' => md5('test'),
	'accred2' => "55555",
]);
// ATTENTION !!
// les "password 1 & 2" sont mis tels quels dans www/xxxxxx/core/class/checkbdd_datas.php
// pour plus de comptes -> ajoutez les www/xxxxxx/core/class/checkbdd_datas.php en fin de fichier










