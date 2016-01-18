<?php

/**
 * read or write the CSV file
 */
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

/**
 * Interface for a sign up field:
 * - how to display the form
 * - how to parse the POST data
 * - some properties
 */
interface IDataField
{
    // Generate FORM
    /**
     * @return string html for the form
     */
    public function getFormHtml();

    // Evaluate FORM
    /**
     * @param $postData array should be $S_POST
     * @param $parsedData mixed receives the parsed data, can be any type
     * @return bool true of posted data was OK and could be parsed
     */
    public function parsePostData($postData, &$parsedData);

    /**
     * @return bool if this field should be visible for the public
     */
    public function isPublic();

    /**
     * @return string name of csv column
     */
    public function getCsvHeader();
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

/**
 * Simple Text field
 */
class TextField implements IDataField{
    var $Title = "";
    var $FormId = "";
    var $CsvHeader = "";
    var $IsPublic = false;
    var $OptionalComment = "";
    var $CanBeEmpty = false;

    /**
     * TextFeld constructor.
     * @param $title string title in form
     * @param $formId string id in form
     * @param $csvHeader string header name in CSV
     * @param $isPublic bool is it hidden?
     * @param $optionalComment string to show in the form after the input field
     */
    public function __construct($title, $formId, $csvHeader, $isPublic = true, $optionalComment = "", $canBeEmpty = false)
    {
        $this->Title = $title;
        $this->FormId = $formId;
        $this->CsvHeader = $csvHeader;
        $this->IsPublic = $isPublic;
        $this->OptionalComment = $optionalComment;
        $this->CanBeEmpty = $canBeEmpty;
    }

    public function getFormHtml()
    {
        return "<tr>
        <td>$this->Title</td>
        <td><input type='text' name='$this->FormId' size='25'/>" . $this->OptionalComment . "</td>
    </tr>";
    }

    public function parsePostData($postData, &$parsedData)
    {
        $id = $this->FormId;
        $strValue = sanitize($postData[$id]);

        if ($strValue == "")
        {   // empty
            $parsedData = "";
            return $this->CanBeEmpty;
        }
        else
        {   // not empty - fine
            $parsedData = $strValue;
            return true;
        }
    }

    public function isPublic()
    {
        return $this->IsPublic;
    }

    public function getCsvHeader()
    {
        return $this->CsvHeader;
    }
}

/**
 * Enumeration Field
 */
class EnumField implements IDataField{
    var $Title = "";
    var $DropDownData = array(); // simple arrays or ...
    var $DataIsMap = true; // simple arrays or ...
    var $FormId = "";
    var $CsvHeader = "";
    var $IsPublic = false;
    var $OptionalComment = "";

    /**
     * TextFeld constructor.
     * @param $title string title in form
     * @param $dropDownData array drop down values
     * @param $dataIsMap bool true if the array is a key-value map
     * @param $formId string id in form
     * @param $csvHeader string header name in CSV
     * @param $isPublic bool is it hidden?
     * @param $optionalComment string to show in the form after the drop down
     */
    public function __construct($title, $dropDownData, $dataIsMap, $formId, $csvHeader, $isPublic = true, $optionalComment = "")
    {
        $this->Title = $title;
        $this->DropDownData = $dropDownData;
        $this->DataIsMap = $dataIsMap;
        $this->FormId = $formId;
        $this->CsvHeader = $csvHeader;
        $this->IsPublic = $isPublic;
        $this->OptionalComment = $optionalComment;
    }

    public function getFormHtml()
    {
        $options = "<option SELECTED>---</option>";
        if ($this->DataIsMap) {
            // map
            foreach ($this->DropDownData as $key => $value) {
                $options .= "<option value='$key'>" . $value . "</option>";
            }
        }
        else {
            // not a map
            foreach ($this->DropDownData as $item) {
                $options .= "<option>" . $item . "</option>";
            }
        }

        return "<tr>
        <td>$this->Title</td>
        <td>
            <select name='$this->FormId'>$options</select> " . $this->OptionalComment . "</td></tr>";
    }

    public function parsePostData($postData, &$parsedData)
    {
        $id = $this->FormId;
        $strValue = sanitize($postData[$id]);

        if ($this->DataIsMap) {
            // map
            foreach ($this->DropDownData as $key => $value) {
                if ($key == $strValue) {
                    // found
                    $parsedData = $value;
                    return true;
                }
            }
        }
        else {
            // not a map
            foreach ($this->DropDownData as $item) {
                if ($item == $strValue) {
                    // found
                    $parsedData = $item;
                    return true;
                }
            }
        }

        // not found
        $parsedData = "";
        return false;
    }

    public function isPublic()
    {
        return $this->IsPublic;
    }

    public function getCsvHeader()
    {
        return $this->CsvHeader;
    }
}

class GenericField implements IDataField {

    var $GetHtml;
    var $Parse;

    var $CsvHeader = "";
    var $IsPublic = false;

    /**
     * GenericField constructor.
     * @param $GetHtml callable function() to return the HTML
     * @param $Parse callable function(&$parsedData) to return parse success and set the parsed date
     * @param $CsvHeader string header in CSV
     * @param $IsPublic bool if this should be shown for all in the table
     */
    public function __construct($GetHtml, $Parse, $CsvHeader, $IsPublic)
    {
        $this->GetHtml = $GetHtml;
        $this->Parse = $Parse;
        $this->CsvHeader = $CsvHeader;
        $this->IsPublic = $IsPublic;
    }

    public function getFormHtml()
    {
        $function = $this->GetHtml;
        return $function();
    }

    public function parsePostData($postData, &$parsedData)
    {
        $function = $this->Parse;
        return $function($parsedData);
    }

    public function isPublic()
    {
        return $this->IsPublic;
    }

    public function getCsvHeader()
    {
        return $this->CsvHeader;
    }
}