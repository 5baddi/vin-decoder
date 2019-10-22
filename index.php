<?php

    require('VINConstants.php');
    require('VINDecoder.php');

    use BADDI\VINDecoder;

    if(isset($_POST['vin']))
        $decoder = new VINDecoder($_POST['vin']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>VIN Decoder</title>
    </head>
    <body>
        <form method="POST">
            <input type="text" name="vin"/>
            <input type="submit" value="Decode"/>
        </form>
    </body>
</html>
