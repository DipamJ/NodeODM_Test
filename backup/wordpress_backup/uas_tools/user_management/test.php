<?php
//require_once("CommonFunctions.php");
//
////////////////
//// Select roles options
//$records_roles = mysqli_query($connect, "SELECT role_name FROM roles");
//$roles = array();
//$count = 0;
//while ($course_roles = mysqli_fetch_assoc($records_roles)){
//    // Save role options into roles array
//    $roles []= $course_roles['role_name'];
//    // Count the number of roles
//    $count++;
//}
////////////////
//?>
<!---->
<!--<html lang="en-us">-->
<!--<head>-->
<!--    <title>Modifying Users</title>-->
<!---->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>-->
<!--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />-->
<!--    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.js"></script>-->
<!---->
<!---->
<!---->
<!--    <style>-->
<!--        </style>-->
<!---->
<!--</head>-->
<!---->
<!--<body>-->
<!--<div class="panel panel-default">-->
<!--    <!--        <div class="panel-heading">Sample Data</div>-->-->
<!--    <div class="panel-body">-->
<!--        <div class="table-responsive">-->
<!--            <table id="sample_data" class="table table-bordered table-striped">-->
<!--                <thead>-->
<!--                <tr>-->
<!--                    <th>ID</th>-->
<!--                    <th>First Name</th>-->
<!--                    <th>Last Name</th>-->
<!--                    <th>Email</th>-->
<!--                    <th>Approval</th>-->
<!--                    <th>Roles</th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!---->
<!--                </tbody>-->
<!--            </table>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div style="display:none;" id="select-box">-->
<!--    <!--    <label>-->-->
<!--    <!--        <select>-->-->
<!--    <!--            <option>Role 1</option>-->-->
<!--    <!--        </select>-->-->
<!--    <!--    </label>-->-->
<!--<label>-->
<!--    <select>-->
<!--        --><?php
//        for ($a = 1; $a <= $count ; $a++){
//            ?>
<!--            <option value="1">--><?php //echo($roles[($a-1)]);?><!--</option>-->
<!--            --><?php
//        }
//        ?>
<!--    </select>-->
<!--</label>-->
<!--</div>-->
<!---->
<!--<div style="display:none;" id="select-box-approval">-->
<!--    <label>-->
<!--        <select>-->
<!--            <option value="1">Approved</option>-->
<!--            <option value="0">Dissaproved</option>-->
<!--        </select>-->
<!--    </label>-->
<!--</div>-->
<!---->
<!--</body>-->
<!--</html>-->
<!---->
<!--<script>-->
<!---->
<!--</script>-->
<!---->
<!--<script>-->
<!--    $(function() {-->
<!--        let $tbl = $('#sample_data'), $select = $('#select-box-approval'), $select1 = $('#select-box')-->
<!---->
<!--        let data = [-->
<!--            [-->
<!--                "Tiger Nixon",-->
<!--                "System Architect",-->
<!--                "Edinburgh",-->
<!--                "5421",-->
<!--                "2011/04/25",-->
<!--                "$3,120"-->
<!--            ],-->
<!--            [-->
<!--                "Garrett Winters",-->
<!--                "Director",-->
<!--                "Edinburgh",-->
<!--                "8422",-->
<!--                "2011/07/25",-->
<!--                "$5,300"-->
<!--            ]-->
<!--        ]-->
<!---->
<!--        $tbl.DataTable({-->
<!--            data: data,-->
<!---->
<!--            columns: [-->
<!--                null, null, null, null, {-->
<!---->
<!--                    render: function(data) {-->
<!--                        return $select.html()-->
<!--                        // + `Use this to select "${data}"`-->
<!--                    }-->
<!--                },-->
<!--                {-->
<!--                    render: function(data) {-->
<!--                        return $select1.html()-->
<!--                        // + `Use this to select "${data}"`-->
<!--                    }-->
<!--                },-->
<!--            ]-->
<!--        })-->
<!--    });-->
<!--</script>-->
