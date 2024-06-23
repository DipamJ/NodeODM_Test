<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("SetFilePath.php");

// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

if (0 < $_FILES["file"]["error"]) {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {
    $fileName = $_FILES["file"]["name"];
    $filePath = SetTempFolderLocalPath() . $fileName;
    move_uploaded_file($_FILES["file"]["tmp_name"], $filePath);

    $fileParts = pathinfo($fileName);
    $dataSetFields = explode("_", $fileParts["filename"]);

    $crop = strtolower($_GET["crop"]);
    $crop = str_replace("-", " ", $crop);
    $crop = str_replace("_", " ", $crop);
    $crop = ucwords($crop);

    $type = strtolower($_GET["type"]);
    $type = str_replace("-", " ", $type);
    $type = str_replace("_", " ", $type);
    $type = ucwords($type);

    $year = $_GET["year"];
    $season = ucwords(strtolower($_GET["season"]));
    $location = $_GET["location"];
    $subLocation = ucwords(strtolower($_GET["sublocation"]));

    $startingValueField = $_GET["startingvaluefield"];

    if ($crop == "" || $type == "" || $year == "" || $location == "") {
        exit("Input Error");
    }

    $name = $year . "-" . $location . "-" . $crop . "-" . str_replace(" ", "_", $type);
    if ($season != "") {
        $name .= "-" . $season;
    }

    if ($subLocation != "") {
        $name .= "-" . $subLocation;
    }

    $file = fopen($filePath, "r");
    if ($file) {
        //Read first line to get list of criteria and values
        // $header contains white spaces
        $header = fgets($file);
        //_log("header: ".$header);

        // remove white spaces from $header
        $no_space_header = str_replace(" ", "_", $header);

        // $no_space_header contains no white spaces
        //_log("no_space_header: ".$no_space_header);

        //$fieldList = explode(",", str_replace(array("\r", "\n"), '', $header));
        $fieldList = explode(",", str_replace(array("\r", "\n"), '', $no_space_header));

        $index = 0;
        while ($fieldList[$index] != $startingValueField && $index < sizeof($fieldList)) {
            //echo $fieldList[$index]." - ".$startingValueField;
            $index++;
        }

        $criteriaList = array_slice($fieldList, 0, $index);
        $dataList = array_slice($fieldList, $index, sizeof($fieldList) - $index);

        $dateCount = 0;
        foreach ($dataList as $dataField) {
            if (is_numeric($dataField)) {//check if the field name is numeric
                if (date('Y-m-d', strtotime($dataField))) {//check if the field name is a date
                    $dateCount++;
                }
            }
        }

        $dateCountThreshold = 1;
        $isMultipleDates = 0;
        if ($dateCount >= $dateCountThreshold) {
            $isMultipleDates = 1;
        }

        // Name of file being uploaded
        $filename = $_FILES['file']['name'];

        //_log("filename: " . $filename);


        // added
//        $fileNameParts = pathinfo($_GET['resumableFilename']);
        //$fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];
        //$fileName = FormatFileName($file["filename"]) . "." . $file["extension"];

        //_log("fileName " . $fileName);

        //$sql = "INSERT INTO crop_data (Name, Location, Crop, Type, Year, Season, SubLocation, MultipleDates, FileName) " .
        //    "VALUES ('$name', '$location','$crop', '$type', '$year', '$season', '$subLocation', $isMultipleDates, $filename)";
        //added

        $sql = "INSERT INTO crop_data (Name, Location, Crop, Type, Year, Season, SubLocation, MultipleDates, FileName) " .
            "VALUES ('$name', '$location','$crop', '$type', '$year', '$season', '$subLocation', '$isMultipleDates', '$filename')";
        $con = SetDBConnection();

        if (mysqli_connect_errno()) {
            exit("Failed to connect to database server: " . mysqli_connect_error());
        } else {
            $result = mysqli_query($con, $sql);

            $dataSetID = $con->insert_id;

            while (($line = fgets($file)) !== false) {
                $sql = "INSERT INTO row_data (CropDataSet) " .
                    "VALUES ($dataSetID)";
                $result = mysqli_query($con, $sql);

                $rowDataSetID = $con->insert_id;

                $lineValueList = explode(",", str_replace(array("\r", "\n"), '', $line));

                $criteriaValueList = array();
                $dataValueList = array();

                for ($i = 0; $i < sizeof($lineValueList); $i++) {
                    if ($i < $index) {
                        $criteriaValueList[] = $lineValueList[$i];
                    } else {
                        $dataValueList[] = $lineValueList[$i];
                    }
                }

                for ($j = 0; $j < sizeof($criteriaList); $j++) {
                    $name = $criteriaList[$j];
                    if (is_numeric($criteriaValueList[$j])) {
                        $value = floatval($criteriaValueList[$j]);
                    } else {
                        $value = $criteriaValueList[$j];
                    }

                    $sql = "INSERT INTO criteria_data (RowDataSet, Name, Value) " .
                        "VALUES ($rowDataSetID,'$name', '$value')";
                    $result = mysqli_query($con, $sql);
                }

                for ($k = 0; $k < sizeof($dataList); $k++) {
                    $name = $dataList[$k];
                    $value = floatval($dataValueList[$k]);

                    $sql = "INSERT INTO value_data (RowDataSet, Name, Value) " .
                        "VALUES ($rowDataSetID,'$name', '$value')";
                    $result = mysqli_query($con, $sql);
                }
            }
            echo "Imported " . $dataSetID;
            mysqli_close($con);

        }
        fclose($file);
    } else {
        echo "File couldn't be opened";
    }
}
?>

<?php $dataSetID = 1; ?>

<!DOCTYPE html>
<html lang="en">
<body>

<a href="GetImportedData.php?varname=<?php echo $dataSetID ?>">Page2</a>

</body>
</html>
