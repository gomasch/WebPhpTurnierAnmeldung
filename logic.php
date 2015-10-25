<?php

$f3 = require('lib/base.php'); // von http://fatfreeframework.com/home

// Fehlersuche
//$f3->set('DEBUG',3);

// ------------------------------
// Einstellungen
// ------------------------------
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

// Variablen für die in den Templates zu verwendenden URLs zu den Seiten
$f3->set('URL_1', '.'); // Link zur Ausschreibungsseite
$f3->set('URL_2', 'anmeldung'); // Link zum Anmelde-Formular, GET für neu, POST für
$f3->set('URL_3', 'liste'); // Link zur Liste der erfolgreichen Anmeldungen

// Formular- und Daten-Struktur: siehe logic_fields.php

// ------------------------------
// ROUTING der Seiten
// ------------------------------

// Haupt-Seite, Ausschreibung
$f3->route(
	array('GET /', 'GET /index.html'),
    function($f3) 
    {
    	echo Template::instance()->render('design/template_index.html');
    }
);

// Neue Anmeldung
$f3->route('GET /anmeldung',
    function($f3) 
    {
        require('logic_pages.php');
        $f3->set('MAIN_CONTENT_RAW', anmeldungFormular($f3)); // ohne escaping
    	echo Template::instance()->render('design/template_anmeldung.html');
    }
);

// Ergebnis einer neuen Anmeldung auswerten und anwenden
$f3->route(
    'POST /anmeldung',
    function($f3) 
    {
        require('logic_pages.php');
        $f3->set('MAIN_CONTENT_RAW', anmeldungAuswerten($f3)); // ohne escaping
    	echo Template::instance()->render('design/template_anmeldung.html');
    }
);

// Liste der angemeldeten Spieler anzeigen
$f3->route('GET /liste',
    function($f3) 
    {
        require('logic_pages.php');
        $f3->set('MAIN_CONTENT_RAW', tabelleAusgeben($f3)); // ohne escaping
    	echo Template::instance()->render('design/template_list.html');
    }
);

// Fehlerseite
// $f3->set('ONERROR',
// 	function($f3){
//     	setDefaultProperties($f3);
//         $f3->set('MAIN_CONTENT', 'Seite nicht gefunden');
//     	echo Template::instance()->render('design/template_anmeldung.html');
// 	}
//);

// ------------------------------
// START
// ------------------------------
$f3->run();