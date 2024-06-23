

<html>
    <?php
    if (isset($_POST['submit'])) {
        echo $_POST['Subject'];
    }
    ?>
    <form method="post">
        <select name="Subject">
            <option value="One">One</option>
            <option value="Two">Two</option>
        </select>

        <input type="submit" name="submit" value="Submit Option!">
    </form>
</html>
