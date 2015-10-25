<?php

require('logic_fields.php');

// ------------------------------
// construct content of HTML pages
// ------------------------------

/**
 * Construct HTML for sign up form
 * @param $f3
 * @return string html fragment for page content to show
 */
function anmeldungFormular($f3)
{
    $fields = constructAllFields($f3->get('RANKS'));

    // build form
    $formFields = "";

    foreach ($fields as $field) {
        $formFields .= $field->getFormHtml();
    }

    return "
<form action='" . $f3->get("URL_2") . "' method='post'>
<table cellspacing='0' cellpadding='5' border='0'>" . $formFields . "
    <tr>
        <td></td>
        <td><input type='submit' value='Anmelden'></td>
    </tr>
</table>
</form>";
}

/**
 * Construct HTML for evaluating sign up post data (and write it to the CSV file)
 * @param $f3 object
 * @return string html fragment for page content to show
 */
function anmeldungAuswerten($f3) {
    $fields = constructAllFields($f3->get('RANKS'));

    // eval POST data
    $allParsedData = array();

    foreach ($fields as $field) {
        $parsedData = null;
        if (! $field->parsePostData($_POST, $parsedData)) {
            // NOT OK
            return "<font color='red'>Bitte bei allen Feldern etwas ausw&auml;hlen bzw. eintragen! <a href='javascript:history.back()'>Zur&uuml;ck</a>";
        }

        array_push($allParsedData, $parsedData);
    }

    //get IP adress and time
    $strIP = htmlspecialchars($_SERVER['REMOTE_ADDR']);
    $strDateAndTime = htmlspecialchars(date("Y-m-d H:i:s"));

    // Table data
    $tableData = array();

    // get previous table data
    $csvFile = new CsvFile($f3->get('FILENAME_CSVRESULT'));
    if($csvFile->exists()) {   
        // file exists, read all
        $tableData = $csvFile->readAll();
    }
    else {
        // build table head
        $tableHead  =  array();

        foreach ($fields as $field) {
            array_push($tableHead, $field->getCsvHeader());
        }

        // add head
        array_push($tableData, $tableHead);
    }

    // build new entry
    $newEntry = array();

    foreach ($allParsedData as $value) {
        array_push($newEntry, $value);
    }

    // add new row
    array_push($tableData, $newEntry);

    // sort after rank
    usort($tableData, buildSorterForRank($f3->get('RANKS')));

    // write all data
    $csvFile->writeAll($tableData);

    // SUCCESS
    return "<font color='green'>Du hast dich erfolgreich angemeldet <a href='liste'>(Anzeigen)</a></font>";
}


/**
 * Construct HTML for table of sign up data
 * @param $f3
 * @return string html fragment for page content to show
 */
function tabelleAusgeben($f3)
{
    $file = $f3->get('FILENAME_CSVRESULT');
    
    if (file_exists($file)) 
    {
        $csvFile = new CsvFile($file);
        $fields = constructAllFields($f3->get('RANKS'));

        $showall = $_GET[$f3->get('SHOWALL_KEY')] == $f3->get('SHOWALL_PASSWD'); // password check to show everything

        $strOutput = "";
        // try to remove item?
        if ($showall) {
            $del = $_GET['del'];
            if ($del != "") {
                // remove something
                $delData = explode(";", $del);
                $delIndex = $delData[0];
                $delCount = $delData[1];

                $tableData = $csvFile->readAll();
                if (count($tableData) == $delCount) {
                    // number OK -> remove index
                    array_splice($tableData, $delIndex, 1); 

                    $csvFile->writeAll($tableData);

                    $strOutput .= "Einer entfernt<br>";

                }
                else {
                    $strOutput .= "Anzahl stimmt nicht<br>";
                }
            }
        }

        // read data
        $tableData = $csvFile->readAll();

        $rowCount = count($tableData);
        $playerCount = $rowCount - 1; // without header

        $strOutput .= "<table border='1' cellspacing='0' cellpadding='3' bordercolor='#000000'>";

        $rowIndex = 0;
        //reading csv-file
        foreach ($tableData as $data) {
            $strOutput .=  "<tr>";

            // data field
            foreach ($fields as $field) {
                $parsedData = array_shift($data);

                if ($showall || $field->isPublic()) {
                    // show this
                    $strOutput .= "<td>" . $parsedData . "&nbsp;</td>";
                }
            }

            if ($showall)
            {
                // show remove column
                if ($rowIndex > 0) {
                    $strOutput .=  "<td><a href='" . $f3->get('URL_3') . "?" . 
                        $f3->get('SHOWALL_KEY') . "=" . $_GET[$f3->get('SHOWALL_KEY')] . "&" . 
                        "del=" . $rowIndex . ";" . $rowCount . "'>L&ouml;schen</a></td>";
                }
                else {
                    $strOutput .=  "<td>Bearbeiten</td>";
                }
            }
            $strOutput    .=  "</tr>";

            $rowIndex++;
        }
        $strOutput    .=  "</table>";
        
        $strOutput .= "<br>Es " . ($playerCount == 1 ? "ist" : "sind" ) . " $playerCount Spieler angemeldet.\n";
        
        if ($showall)
        {
            $strOutput .= "<br><br></bt><p>Daten als reine csv-datei: <hr><pre>\n";
            $csv    =   fopen($file,'r');
            $strOutput .= fread($csv, filesize($file));
            fclose ($csv);
            $strOutput .= "</pre><hr>\n";
         }
    } else {
        $strOutput = "noch ist niemand angemeldet.";
    }

    return $strOutput;
}
