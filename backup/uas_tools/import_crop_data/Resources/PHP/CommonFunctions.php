<?php
//Remove special characters, replace spaces with underscores, replace all proceeding underscores with 1 underscore
function FormatString($rawName)
{
    $formattedName = str_replace(" ", "_", $rawName);
    $formattedName = preg_replace("/[^A-Za-z0-9\_]/", "", $formattedName);
    return preg_replace("/_+/", "_", $formattedName);
}

?>