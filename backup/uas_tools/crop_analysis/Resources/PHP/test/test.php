<?php

?>

<!DOCTYPE html>
<html>
<body>

<script>
    $(document).ready(function () {
        $('#editable_table').Tabledit({

            // when click on save; action_user_2.php gets called
            url: 'action_user_2.php', // where data will be sent

            data: {approved_status: approved_status},
            columns: {
                identifier: [0, "user_id"],
                editable: [[1, 'first_name'],
                    [2, 'last_name'],
                    [3, 'email']]
            },
            // hide the column that has the identifier
            hideIdentifier: true,

            // activate focus on first input of a row when click in save button
            autoFocus: true,

            // activate save button when click on edit button
            saveButton: true,

            restoreButton: false,
            onSuccess: function (data, textStatus, jqXHR) {
                var htmlString = "<?php echo 'User information has been updated'; ?>";
                alert(htmlString);

                // custom action buttons
                if (data.action === 'delete') {
                    $('#' + data.id).remove();
                }
            }
        });

    });
    $('#editable_table').DataTable();
</script>



</body>
</html>
