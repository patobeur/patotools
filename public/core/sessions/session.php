<?php
    $default_timezone = date_default_timezone_get();
    date_default_timezone_set('Europe/Amsterdam');
    if (!isset($_SESSION['user'])){
        // nouveau
        $_SESSION['user'] = 
        [
            'naissance' => date("H:i:s"),
            'current' => [],
            'debug' => ROOTS['debug'],
            'player' =>
            [
                'ip' => Fun::get_ip_address(),
                'serverip' => gethostbyname(trim(`hostname`)),
                'pseudo' => 'invité(e)',
                'login' => ''
            ],
            'pages' =>
            [
                'list' => [],
                'route' => []
            ],
            'statut' => 'none',
            'try' => 0,
            'crea_date' => Fun::dateDuJour('classic'),
            'last_date' => Fun::dateDuJour('classic'),
            'last_page' => 'none',
            'current_page' => 'accueil',
            'hit' => 0,
            'ipv4' => Fun::get_ip_address()
        ];
        $_SESSION['cms'] = [
            'EXIT' => '<a class=cms href=?exit>Exit</a>',
            'Readersql' => '<a class=cms href=?readersql>Readersql Testing</a>',
            'Importsql' => '<a class=cms href=?importsql>Importsql Testing</a>',
            'Glpi' => '<a class=cms href=?glpi>Glpi Testing</a>',
            'test' => '<a class=cms href=?fggfgqggsgs>Just Testing</a>'
        ];
    }

    empty($_SESSION['user']['pages']['list'][ $_SESSION['user']['current_page'] ])
        ? $_SESSION['user']['pages']['list'][ $_SESSION['user']['current_page'] ] = 1
        : $_SESSION['user']['pages']['list'][ $_SESSION['user']['current_page'] ] = $_SESSION['user']['pages']['list'][$_SESSION['user']['current_page']] + 1;

    $_SESSION['cms']['errors'] = [];
    $_SESSION['cms']['sql'] = [];
    $_SESSION['cms']['bdd'] = [];
    $_SESSION['cms']['autoload'] = [];
    $_SESSION['cms']['traits'] = [];
    // $_SESSION['cms']['requete'] = [];
    $_SESSION['cms']['require'] = [];
    $_SESSION['cms']['include'] = [];
    $_SESSION['cms']['get_contents'] = [];

    // $_SESSION['user']['pages']['poi'][] = $_SESSION['user']['current_page'];
    $_SESSION['user']['hit']++;
    $_SESSION['user']['last_page'] = $_SESSION['user']['current_page'];
?>