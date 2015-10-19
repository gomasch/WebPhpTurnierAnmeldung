<?php

$f3 = require('lib/base.php'); // von http://fatfreeframework.com/home

// Fehlersuche
//$f3->set('DEBUG',3);

// Einstellungen
$f3->set('TURNIER_NAME', 'X. Go-Turnier 2015');
$f3->set('TURNIER_BESCHREIBUNG', 'Turnier und Go-Seminare vom XX. bis YY. Monat 2015');
$f3->set('FILENAME_CSVRESULT', 'turnieranmeldungen.csv');

$f3->set('SHOWALL_KEY', 'passwd');
$f3->set('SHOWALL_PASSWD', 'secret'); // hier ein frisch ausgewürfeltes Passwort einsetzen
// dann kann man liste?passwd=secret die Anmelde-Details anschauen

$f3->set('RANKS', array('7d', '6d', '5d', '4d', '3d', '2d', '1d', 
    '1k', '2k', '3k', '4k', '5k', '6k', '7k', '8k', '9k', '10k', 
    '11k', '12k', '13k', '14k', '15k', '16k', '17k', '18k', '19k', '20k', 
    '21k', '22k', '23k', '24k', '25k', '26k', '27k', '28k', '29k', '30k' 
    ));

// Haupt-Seite
$f3->route(
	array('GET /', 'GET /index.html'),
    function($f3) 
    {
    	echo Template::instance()->render('template_index.html');
    }
);

// Neue Anmeldung
$f3->route('GET /anmeldung',
    function($f3) 
    {
        require('logic_anmeldung.php');
        $f3->set('MAIN_CONTENT_RAW', anmeldungFormular($f3)); // ohne escaping
    	echo Template::instance()->render('template_anmeldung.html');
    }
);

//Ergebnis einer neuen Anmeldung
$f3->route(
    'POST /anmeldung',
    function($f3) 
    {
        require('logic_anmeldung.php');
        $f3->set('MAIN_CONTENT_RAW', anmeldungAuswerten($f3)); // ohne escaping
    	echo Template::instance()->render('template_anmeldung.html');
    }
);

// Liste der angemeldeten Spieler anzeigen
$f3->route('GET /liste',
    function($f3) 
    {
        require('logic_anmeldung.php');
        $f3->set('MAIN_CONTENT_RAW', tabelleAusgeben($f3)); // ohne escaping
    	echo Template::instance()->render('template_list.html');
    }
);

// Fehlerseite
// $f3->set('ONERROR',
// 	function($f3){
//     	setDefaultProperties($f3);
//         $f3->set('MAIN_CONTENT', 'Seite nicht gefunden');
//     	echo Template::instance()->render('template_anmeldung.html');
// 	}
//);

// START
$f3->run();

?>