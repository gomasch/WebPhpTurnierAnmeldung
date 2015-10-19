<?php

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

    $file = $f3->get('FILENAME_CSVRESULT');
    if(file_exists($file))
    {   // file exists, read all
        $csv = fopen($file,'r');
        while (($data = fgetcsv($csv, 1000, ",")) !== FALSE) {
            array_push($tableData, $data);
        }
        fclose($csv);
    }

    // add new entry
    array_push($tableData, array(
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
        $strDateAndTime));

    // sort after rank
    usort($tableData, buildSorterForRank($f3->get('RANKS')));

    // write all data
    $wrtALL = fopen($file,'w');
    foreach ($tableData as $singleLine) {
        fputcsv($wrtALL, $singleLine);
    }
    fwrite($wrtALL, $allranks);
    fclose($wrtALL);

    // SUCCESS
    return "<font color='green'>Du hast dich erfolgreich angemeldet <a href='liste'>(Anzeigen)</a></font>";
}

// this function removes all commas
function sanitize($string)
{
    return str_replace(",", "", htmlspecialchars($string));
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
    
    $showall = $_GET[$f3->get('SHOWALL_KEY')] == $f3->get('SHOWALL_PASSWD'); // password check to show everything

    if (file_exists($file)) 
    {
        $playerCount = 0;
        $csv    =   fopen($file,'r');
        $strList    = "<table border='1' cellspacing='0' cellpadding='3' bordercolor='#000000'>";
        //reading csv-file
        while ( ($data = fgetcsv ($csv, 1000, ",")) != FALSE ) 
        {
            $playerCount = $playerCount + 1;
            //here you can delete or insert your fields of the csv-file
            //first field in csv-file is the same as $data[0], 
            $strList    .=  "<tr>";
            $strList    .=  "<td width='100'>".$data[0]."&nbsp;</td>";//Name
            $strList    .=  "<td width='100'>".$data[1]."&nbsp;</td>";//Vorname
            $strList    .=  "<td width='100'>".$data[2]."&nbsp;</td>";//Stadt
            if ($showall)
            {
                $strList    .=  "<td width='100'>".$data[3]."</td>";//E-Mail
                $strList    .=  "<td width='100'>".$data[4]."&nbsp;</td>";//DGOB
            }
            $strList    .=  "<td width='30'>".$data[5]."&nbsp;</td>";//Rang
            if ($showall)
            {
                $strList    .=  "<td align='center' width='50'>".$data[6]."&nbsp;</td>";//keine Übernachtung
                $strList    .=  "<td align='center' width='50'>".$data[7]."&nbsp;</td>";//Übernachtung ab Freitag
                $strList    .=  "<td align='center' width='50'>".$data[8]."&nbsp;</td>";//Übernachtung ab Samstag
            }
            $strList    .=  "<td width='30'>".$data[9]."&nbsp;</td>";//Teilnahme Turnier
            $strList    .=  "<td width='30'>".$data[10]."&nbsp;</td>";//Teilnahme Nachmittagseminar
            $strList    .=  "<td width='30'>".$data[11]."&nbsp;</td>";//Teilnahme Abendseminar
            if ($showall)
            {
                $strList    .=  "<td align='center' width='50'>".$data[12]."&nbsp;</td>";//IP
                $strList    .=  "<td align='center' width='50'>".$data[13]."&nbsp;</td>";//Datum und Zeit
                if($delete==1)
                {
                    $strList    .=  "<td><a href='show.php?$SHOWALL_GETKEY=$SHOWALL_GETPASSWD&del=$data[0];$data[1];$data[2];$data[3]'>L&ouml;schen</a></td>";
                }
                $delete = 1;
            }
            $strList    .=  "</tr>";
        }
        $strList    .=  "</table>";
        fclose ($csv);
        $strList .= "<br>Es sind $playerCount Spieler angemeldet.\n";
        
        if ($showall)
        {
            $strList .= "<p>Daten als reine csv-datei: <hr><pre>\n";
            $csv    =   fopen($file,'r');
            $strList .= fread($csv, filesize($file));
            fclose ($csv);
            $strList .= "</pre><hr>\n";
         }
    } else {
        $strList = "noch ist niemand angemeldet.";
    }
    return $strList;
}

?>