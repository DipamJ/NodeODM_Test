<?php
    require_once("SetDBConnection.php");

    $con = SetDBConnection();

    if (mysqli_connect_errno()) {
        echo "Failed to connect to database server: ".mysqli_connect_error();
    } else {
        $projectID = $_GET["project"];
        $type = $_GET["type"];
        if ($type != "%") {
            $typeCondition =  " and imagery_product.Type = $type ";
        } else {
            $typeCondition = "";
        }

        $sql =  "select imagery_product.* ".
                "from imagery_product, flight ".
                "where flight.Project = $projectID and imagery_product.Flight = flight.ID ".
                "and imagery_product.Status = 'Finished' ".
                $typeCondition.
                "order by Filename";
        //_log('select imagery_product and flight: '.$sql);

        //echo $sql;

        $result = mysqli_query($con, $sql);
        $list = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        echo json_encode($list);
    }

    mysqli_close($con);
    ?>
