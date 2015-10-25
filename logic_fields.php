<?php

require('logic_classes.php');

// ------------------------------
// STRUCTURE OF FORM+CSV DATA
// ------------------------------

/**
 * Construct schema data
 * @param $goRanks array of ranks
 * @return IDataField[]
 */
function constructAllFields($goRanks)
{
    return array(
        // Basis-Daten
        new TextField("Vorname", "strName", "Vorname"),
        new TextField("Nachname", "strFamilyName", "Nachname"),
        new TextField("Ort", "strTown", "Ort"),
        new EnumField("Rang", $goRanks, true, "strRank", "Rang"), // INDEX OF RANG: 3
        new TextField("E-Mail", "strMail", "Nachname", false, "<br>(zur Kontaktaufnahme, wird nicht mit angezeigt)"),
        new EnumField("DGoB-Mitglied", array("Ja", "Nein"), false, "strDGoB", "DGoB", false, "<br>(wird nicht mit angezeigt)"),

        // Auskommentieren, wenn man die Übernachtung nicht abfragen möchte:
        //new EnumField("&Uuml;bernachtung", array("N" => "Keine", "Ab Freitag", "Ab Samstag"), true, "strSleep", "Übernachtung", false, "<br>(bitte per Mail melden wenn noch jemand mitkommt; wird nicht mit angezeigt)"),

        // Folgende 3 Felder auskommentieren, wenn es keine Seminare gibt und man nicht unterscheiden möchte, wer zum Turnier kommt und wer zum Seminar
        // Ansonsten: Die Beschreibungen anpassen (Hinweise zum Geld.
        //new EnumField("Teilnahme Turnier", array("Ja", "Nein"), false, "strTurnier", "Teilname Turnier", true, "<br>(Samstag und Sonntag, 0-20 Euro, siehe Ausschreibung)"),
        //new EnumField("Teilnahme Nachmittagsseminar", array("Ja", "Nein"), false, "strNachmittagSem", "Teilname Nachmittagsseminar", true, "<br>(Freitag Nachmittag, 0-15 Euro, siehe Ausschreibung)"),
        //new EnumField("Teilnahme Abendseminar", array("Ja", "Nein"), false, "strAbendSem", "Teilname Abendseminar", true, "<br>Freitag Abend, 0-15 Euro, siehe Ausschreibung)"),

        // IP und Zeit werden gespeichert
        new GenericField(function() { return "";}, function(&$parsedData) { $parsedData = htmlspecialchars($_SERVER['REMOTE_ADDR']); return true;}, "Anmelde-IP", false),
        new GenericField(function() { return "";}, function(&$parsedData) { $parsedData = htmlspecialchars(date("Y-m-d H:i:s")); return true;}, "Anmelde-Zeit", false),
    );
}

/**
 * helper for applying usort on CSV data array
 * @param $ranks
 * @return callable
 */
function buildSorterForRank($ranks)
{
    return function($a, $b) use ($ranks) {
        // NOTE: INDEX OF RANG is 3
        $indexOfA = array_search($a[3], $ranks);
        $indexOfB = array_search($b[3], $ranks);

        // handle special cases
        if ($indexOfA === $indexOfB) {
            // exactly the same (maybe not found)
            return 0;
        }
        if (false === $indexOfA) {
            // a not found - treat as biggest
            return -1; // B is smaller
        }
        if (false === $indexOfB) {
            // b not found - treat as biggest
            return 1; // A is smaller
        }

        return $indexOfA - $indexOfB;
    };
}