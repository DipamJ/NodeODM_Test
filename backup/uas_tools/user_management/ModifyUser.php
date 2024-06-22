<?php
require_once("CommonFunctions.php");
// Check only Admins can access to it
session_start();
$_VERIFY = $_SESSION['email'];

// SELECT USING EMAIL TO GET THE role_id
$sql = "select role_id from users_roles, users where  users_roles.user_id = users.user_id and email = '".$_VERIFY."'";// ORDER BY role_id ASC
$result = mysqli_query($connect, $sql);
//_log('select role_id: '.$sql);
$row = mysqli_fetch_assoc($result);
//echo $row["role_id"];
if ($row["role_id"] != '1') {
    header('location:index.php?lmsg=true');
    exit;
}

//////////////
// Select roles options
$records_roles = mysqli_query($connect, "SELECT role_name FROM roles");
$roles = array();
$count = 0;
while ($course_roles = mysqli_fetch_assoc($records_roles)){
    // Save role options into roles array
    $roles []= $course_roles['role_name'];
    // Count the number of roles
    $count++;
}
//////////////
?>

<html lang="en-us">

<head>
    <title>Modifying Users</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"></script>

    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" />

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
    <style>
        .MultiCheckBox {
            border: 1px solid #e2e2e2;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .MultiCheckBox .k-icon {
            font-size: 15px;
            float: right;
            font-weight: bolder;
            margin-top: -7px;
            height: 10px;
            width: 14px;
            color: #787878;
        }

        .MultiCheckBoxDetail {
            display: none;
            position: absolute;
            border: 1px solid #e2e2e2;
            overflow-y: hidden;
        }

        .MultiCheckBoxDetailBody {
            overflow-y: scroll;
        }

        .MultiCheckBoxDetail .cont {
            clear: both;
            overflow: hidden;
            padding: 2px;
        }

        .MultiCheckBoxDetail .cont:hover {
            background-color: #cfcfcf;
        }

        .MultiCheckBoxDetailBody>div>div {
            float: left;
        }

        .MultiCheckBoxDetail>div>div:nth-child(1) {}

        .MultiCheckBoxDetailHeader {
            overflow: hidden;
            position: relative;
            height: 28px;
            background-color: #3d3d3d;
        }

        .MultiCheckBoxDetailHeader>input {
            position: absolute;
            top: 4px;
            left: 3px;
        }

        .MultiCheckBoxDetailHeader>div {
            position: absolute;
            top: 5px;
            left: 24px;
            color: #fff;
        }

        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

    </style>

</head>

<body>

    <div class="container" style="padding-bottom: 1rem!important; padding-top: 1rem!important;">
        <h3 align="center">Modifying Users</h3>
        <br />
        <div class="project">
            <div class="panel panel-default">
                <!--        <div class="panel-heading">Sample Data</div>-->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="sample_data" class="table table-bordered table-striped">
                            <thead>
                                <tr style="background: #555555; color: #ffffff;">
                                    <th style="border: none;">ID</th>
                                    <th style="border: none;">First Name</th>
                                    <th style="border: none;">Last Name</th>
                                    <th style="border: none;">Email</th>
                                    <th style="border: none;">Approval</th>
                                    <!--                        <th>Roles</th>-->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--//////////////-->
    <div>
        <select name="test">
            <?php
    for ($a = 1; $a <= $count ; $a++){
        ?>
            <option value="1"><?php echo($roles[($a-1)]);?></option>
            <?php
    }
    ?>
        </select>
    </div>
    <!--//////////////-->

    <br />
    <br />
</body>

</html>
<!--<script src="https://code.jquery.com/jquery-3.4.1.js"></script>-->
<script>
    $(document).ready(function() {
        $("#test").CreateMultiCheckBox({
            width: '230px',
            defaultText: 'Select Below',
            height: '250px'
        });
    });

    $(document).ready(function() {
        $(document).on("click", ".MultiCheckBox", function() {
            var detail = $(this).next();
            detail.show();
        });

        $(document).on("click", ".MultiCheckBoxDetailHeader input", function(e) {
            e.stopPropagation();
            var hc = $(this).prop("checked");
            $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", hc);
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
        });

        $(document).on("click", ".MultiCheckBoxDetailHeader", function(e) {
            var inp = $(this).find("input");
            var chk = inp.prop("checked");
            inp.prop("checked", !chk);
            $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", !chk);
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();
        });

        $(document).on("click", ".MultiCheckBoxDetail .cont input", function(e) {
            e.stopPropagation();
            $(this).closest(".MultiCheckBoxDetail").next().UpdateSelect();

            var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
            $(".MultiCheckBoxDetailHeader input").prop("checked", val);
        });

        $(document).on("click", ".MultiCheckBoxDetail .cont", function(e) {
            var inp = $(this).find("input");
            var chk = inp.prop("checked");
            inp.prop("checked", !chk);

            var multiCheckBoxDetail = $(this).closest(".MultiCheckBoxDetail");
            var multiCheckBoxDetailBody = $(this).closest(".MultiCheckBoxDetailBody");
            multiCheckBoxDetail.next().UpdateSelect();

            var val = ($(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length)
            $(".MultiCheckBoxDetailHeader input").prop("checked", val);
        });

        $(document).mouseup(function(e) {
            var container = $(".MultiCheckBoxDetail");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });
    });

    var defaultMultiCheckBoxOption = {
        width: '220px',
        defaultText: 'Select Below',
        height: '200px'
    };

    jQuery.fn.extend({
        CreateMultiCheckBox: function(options) {

            var localOption = {};
            localOption.width = (options != null && options.width != null && options.width != undefined) ? options.width : defaultMultiCheckBoxOption.width;
            localOption.defaultText = (options != null && options.defaultText != null && options.defaultText != undefined) ? options.defaultText : defaultMultiCheckBoxOption.defaultText;
            localOption.height = (options != null && options.height != null && options.height != undefined) ? options.height : defaultMultiCheckBoxOption.height;

            this.hide();
            this.attr("multiple", "multiple");
            var divSel = $("<div class='MultiCheckBox'>" + localOption.defaultText + "<span class='k-icon k-i-arrow-60-down'><svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='sort-down' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512' class='svg-inline--fa fa-sort-down fa-w-10 fa-2x'><path fill='currentColor' d='M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z' class=''></path></svg></span></div>").insertBefore(this);
            divSel.css({
                "width": localOption.width
            });

            var detail = $("<div class='MultiCheckBoxDetail'><div class='MultiCheckBoxDetailHeader'><input type='checkbox' class='mulinput' value='-1982' /><div>Select All</div></div><div class='MultiCheckBoxDetailBody'></div></div>").insertAfter(divSel);
            detail.css({
                "width": parseInt(options.width) + 10,
                "max-height": localOption.height
            });
            var multiCheckBoxDetailBody = detail.find(".MultiCheckBoxDetailBody");

            this.find("option").each(function() {
                var val = $(this).attr("value");

                if (val == undefined)
                    val = '';

                multiCheckBoxDetailBody.append("<div class='cont'><div><input type='checkbox' class='mulinput' value='" + val + "' /></div><div>" + $(this).text() + "</div></div>");
            });

            multiCheckBoxDetailBody.css("max-height", (parseInt($(".MultiCheckBoxDetail").css("max-height")) - 28) + "px");
        },
        UpdateSelect: function() {
            var arr = [];

            this.prev().find(".mulinput:checked").each(function() {
                arr.push($(this).val());
            });

            this.val(arr);
        },
    });

</script>

<script>
    var emelnt = $('#test');
    //now append to table td
    $('table tbody td:nth-child(6)').html(emelnt);

</script>

<script>
    $(document).ready(function() {

        var dataTable = $('#sample_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "FetchUserTable.php",
                type: "POST"
            }
        });

        $('#sample_data').on('draw.dt', function() {
            $('#sample_data').Tabledit({
                url: 'ActionUserTable.php',
                dataType: 'json',
                columns: {
                    identifier: [0, 'user_id'],
                    editable: [
                        [1, 'first_name'],
                        [2, 'last_name'],
                        [3, 'email'],
                        [4, 'admin_approved', '{"1":"Approved","2":"Disapproved"}']
                        // [5, 'role_id']
                    ]
                },
                hideIdentifier: true, // added
                restoreButton: false,
                onSuccess: function(data, textStatus, jqXHR) {
                    if (data.action == 'delete') {
                        $('#' + data.id).remove();
                        $('#sample_data').DataTable().ajax.reload();
                    }
                }
            });
        });

    });

</script>
