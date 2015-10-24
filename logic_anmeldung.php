<?php

class CsvFile {
    private $file = '';

    public function __construct($fileName) {
        $this->file = $fileName;
    }

    public function exists() {
        return file_exists($this->file);
    }

    public function readAll()
    {
        $tableData = array();

        $csv = fopen($this->file,'r');
        while (($data = fgetcsv($csv, 1000, ",")) !== FALSE) {
            array_push($tableData, $data);
        }
        fclose($csv);

        return $tableData;
    }

    public function writeAll($tableData) {
        $wrtALL = fopen($this->file,'w');
        foreach ($tableData as $singleLine) {
            fputcsv($wrtALL, $singleLine);
        }
        fclose($wrtALL);
    }
}

function anmeldungFormular($f3)
{
    //formular 
    return "
<form action='anmeldung' method='post'>
<table cellspacing='0' cellpadding='5' border='0'>
    <tr>
        <td>Vorname: </td>
        <td border=1><input type='text' name='strPrename' size='25'></td>
    </tr>
    <tr>
        <td>Nachname: </td>
        <td border=1><input type='text' name='strName' size='25'></td>
    </tr>
    <tr>
        <td>Ort: </td>
        <td border=1><input type='text' name='strTown' size='25'></td>
    </tr>
    <tr>
        <td>Rang:</td>
        <td>
            <select name='strRank' size='1'>
                <option SELECTED>---</option>
                " . 
                implode('', array_map(function($rank) { return "<option>" . $rank . "</option>\n"; }, $f3->get('RANKS'))) . 
                "
            </select>
        </td>
    </tr>
    <tr>
        <td>E-Mail: </td>
        <td border=1><input type='text' name='strMail' size='25'><br>(zur Kontaktaufnahme, wird nicht mit angezeigt)</td>
    </tr>
    <tr>
        <td valign='top'>DGoB-Mitglied</td>
        <td>
            <table>
            <select name='strDGOB' size='1'>
                <option SELECTED>---</option>
                <option value='Ja'>Ja</option>
                <option value='Nein'>Nein</option>
            </select><br>(wird nicht mit angezeigt)
            </table> 
        </td>
    </tr>
    <tr>
        <td valign='top'>&Uuml;bernachtung:</td>
        <td>
            <table>
            <select name='strSleep' size='1'>
                <option SELECTED>---</option>
                <option value='N'>keine</option>
                <option value='F'>Ab Freitag</option>
                <option value='S'>Ab Samstag</option>
            </select><br>(bitte per Mail melden wenn noch jemand mitkommt; wird nicht mit angezeigt)
            </table>
        </td>
    </tr>
    <tr>
        <td valign='top'>Teilnahme Turnier</td>
        <td>
            <table>
            <select name='strTurnier' size='1'>
                <option SELECTED>---</option>
                <option value='Ja'>Ja</option>
                <option value='Nein'>Nein</option>
            </select><br>(Samstag und Sonntag, 0-20 Euro, siehe Ausschreibung)
            </table> 
        </td>
    </tr>
    <tr>
        <td valign='top'>Teilnahme Nachmittagsseminar</td>
        <td>
            <table>
            <select name='strNachmittagSem' size='1'>
                <option SELECTED>---</option>
                <option value='Ja'>Ja</option>
                <option value='Nein'>Nein</option>
            </select><br>(Freitag Nachmittag, 0-15 Euro, siehe Ausschreibung)
            </table> 
        </td>
    </tr>
    <tr>
        <td valign='top'>Teilnahme Abendseminar</td>
        <td>
            <table>
            <select name='strAbendSem' size='1'>
                <option SELECTED>---</option>
                <option value='Ja'>Ja</option>
                <option value='Nein'>Nein</option>
            </select><br>(Freitag Abend, 0-15 Euro, siehe Ausschreibung)
            </table> 
        </td>
    </tr>
    <tr>
        <td></td>
        <td><input type='submit' name='senden' value='Anmelden'></td>
    </tr>
</table>
</form>";
}

function anmeldungAuswerten($f3) {
    $strName    =   sanitize($_POST['strName']);
    $strPrename =   sanitize($_POST['strPrename']);
    $strMail    =   sanitize($_POST['strMail']);
    $strTown    =   sanitize($_POST['strTown']);
    $strRank    =   sanitize($_POST['strRank']);
    $strSleep   =   sanitize($_POST['strSleep']);
    $strDGOB    =   sanitize($_POST['strDGOB']);
    $strTurnier =   sanitize($_POST['strTurnier']);
    $strNachmittagSem  = sanitize($_POST['strNachmittagSem']);
    $strAbendSem  = sanitize($_POST['strAbendSem']);

    if (    
        $strName    ==  "" OR 
        $strPrename ==  "" OR 
        $strMail    ==  "" OR 
        $strTown    ==  "" OR 
        $strRank    ==  "---" OR 
        $strSleep   ==  "---" OR 
        $strDGOB    ==  "---" OR 
        $strTurnier ==  "---" OR 
        $strNachmittagSem   ==  "---" OR 
        $strAbendSem    ==  "---"
        )
    {   //  variable not set
        return "<font color='red'>Bitte bei allen Feldern etwas ausw&auml;hlen bzw. eintragen! <a href='javascript:history.back()'>Zur&uuml;ck</a>";
    }

    // translate sleep status to $strSleepNo, $strSleepF, $strSleepS
    if ($strSleep == 'F') //sleep friday?
    {
        $strSleepF  =   'x';
        $strSleepS  =   '';
        $strSleepNo =   '';
    } elseif ($strSleep == 'S') // sleep saturday
    {
        $strSleepF  =   '';
        $strSleepS  =   'x';
        $strSleepNo =   '';
    } else //no sleep!!!
    {
        $strSleepF  =   '';
        $strSleepS  =   '';
        $strSleepNo =   'x';
    }
    //end sleep status

    //get IP adress
    $strIP = htmlspecialchars($_SERVER['REMOTE_ADDR']);
    $strDateAndTime = date("Y-m-d H:i:s");

    // table he
    $tablehead  =  array(
        "Nachname", 
        "Vorname", 
        "Ort", 
        "E-Mail", 
        "DGOB",
        "Rang", 
        "keine Übernachtung",
        "ab Freitag",
        "ab Samstag",
        "Teilnahme Turnier",
        "Teilnahme Nachmittagseminar",
        "Teilnahme Abendseminar",
        "Anmelde-IP",
        "Anmelde-Zeit");

    $tableData = array();

    $csvFile = new CsvFile($f3->get('FILENAME_CSVRESULT'));
    if($csvFile->exists()) {   
        // file exists, read all
        $tableData = $csvFile->readAll();
    }
    else {
        array_push($tableData, $tablehead);
    }

    // add new entry
    $newEntry = array(
        $strName,
        $strPrename,
        $strTown,
        $strMail,
        $strDGOB,
        $strRank,
        $strSleepNo,
        $strSleepF,
        $strSleepS,
        $strTurnier,
        $strNachmittagSem,
        $strAbendSem,
        $strIP,
        $strDateAndTime);
    array_push($tableData, $newEntry);

    // sort after rank
    usort($tableData, buildSorterForRank($f3->get('RANKS')));

    // write all data
    $csvFile->writeAll($tableData);

    // SUCCESS
    return "<font color='green'>Du hast dich erfolgreich angemeldet <a href='liste'>(Anzeigen)</a></font>";
}

// this function removes all commas, HTML stuff and limit length
function sanitize($string)
{
    $result = str_replace(",", "", htmlspecialchars($string));

    if (strlen($result) > 50)
    {   // too big, limit length
        $result = substr($result, 0, 50);
    }
   
    return $result;
}

function buildSorterForRank($ranks)
{
    return function($a, $b) use ($ranks) {
        return array_search($a[5], $ranks) - array_search($b[5], $ranks);
    };
}

function tabelleAusgeben($f3)
{
    $file = $f3->get('FILENAME_CSVRESULT');
    
    if (file_exists($file)) 
    {
        $csvFile = new CsvFile($file);

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
        $playerCount = count($tableData);

        $strOutput .= "<table border='1' cellspacing='0' cellpadding='3' bordercolor='#000000'>";

        $rowIndex = 0;
        //reading csv-file
        foreach ($tableData as $data) {
            //first field in csv-file is the same as $data[0], 
            $strOutput    .=  "<tr>";
            $strOutput    .=  "<td width='100'>".$data[0]."&nbsp;</td>";//Name
            $strOutput    .=  "<td width='100'>".$data[1]."&nbsp;</td>";//Vorname
            $strOutput    .=  "<td width='100'>".$data[2]."&nbsp;</td>";//Stadt
            if ($showall)
            {
                $strOutput    .=  "<td width='100'>".$data[3]."</td>";//E-Mail
                $strOutput    .=  "<td width='100'>".$data[4]."&nbsp;</td>";//DGOB
            }
            $strOutput    .=  "<td width='30'>".$data[5]."&nbsp;</td>";//Rang
            if ($showall)
            {
                $strOutput    .=  "<td align='center' width='50'>".$data[6]."&nbsp;</td>";//keine Übernachtung
                $strOutput    .=  "<td align='center' width='50'>".$data[7]."&nbsp;</td>";//Übernachtung ab Freitag
                $strOutput    .=  "<td align='center' width='50'>".$data[8]."&nbsp;</td>";//Übernachtung ab Samstag
            }
            $strOutput    .=  "<td width='30'>".$data[9]."&nbsp;</td>";//Teilnahme Turnier
            $strOutput    .=  "<td width='30'>".$data[10]."&nbsp;</td>";//Teilnahme Nachmittagseminar
            $strOutput    .=  "<td width='30'>".$data[11]."&nbsp;</td>";//Teilnahme Abendseminar
            if ($showall)
            {
                $strOutput    .=  "<td align='center' width='50'>".$data[12]."&nbsp;</td>";//IP
                $strOutput    .=  "<td align='center' width='50'>".$data[13]."&nbsp;</td>";//Datum und Zeit
                if ($rowIndex > 0) {
                    $strOutput .=  "<td><a href='" . $f3->get('URL_3') . "?" . 
                        $f3->get('SHOWALL_KEY') . "=" . $_GET[$f3->get('SHOWALL_KEY')] . "&" . 
                        "del=" . $rowIndex . ";" . $playerCount."'>L&ouml;schen</a></td>";
                }
                else {
                    $strOutput .=  "<td>Bearbeiten</td>";
                }
            }
            $strOutput    .=  "</tr>";

            $rowIndex++;
        }
        $strOutput    .=  "</table>";
        
        $strOutput .= "<br>Es sind $playerCount Spieler angemeldet.\n";
        
        if ($showall)
        {
            $strOutput .= "<p>Daten als reine csv-datei: <hr><pre>\n";
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
