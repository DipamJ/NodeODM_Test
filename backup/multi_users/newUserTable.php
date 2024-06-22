<html>
<head>
    <title>How to use Tabledit plugin with jQuery Datatable in PHP Ajax</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
</head>
<body>
<div class="container">
    <h3 align="center">How to use Tabledit plugin with jQuery Datatable in PHP Ajax</h3>
    <br />
    <div class="panel panel-default">
        <div class="panel-heading">Sample Data</div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="sample_data" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Approval</th>
<!--                        <th>Roles</th>-->
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<br />
<br />
</body>
</html>

<script type="text/javascript" language="javascript" >
    $(document).ready(function(){

        var dataTable = $('#sample_data').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
                url:"fetchNewUserTable.php",
                type:"POST"
            }
        });

        $('#sample_data').on('draw.dt', function(){
            $('#sample_data').Tabledit({
                url:'actionNewUserTable.php',
                dataType:'json',
                columns:{
                    identifier : [0, 'user_id'],
                    editable:[
                        [1, 'first_name'],
                        [2, 'last_name'],
                        [3, 'email'],
                        [4, 'admin_approved', '{"1":"Approved","2":"Disapproved"}']
                        // [5, 'role_id']
                            ]
                },
                restoreButton:false,
                onSuccess:function(data, textStatus, jqXHR)
                {
                    if(data.action == 'delete')
                    {
                        $('#' + data.id).remove();
                        $('#sample_data').DataTable().ajax.reload();
                    }
                }
            });
        });

    });
</script>