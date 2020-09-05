<?php

    require('VINConstants.php');
    require('VINDecoder.php');

    use BADDIGroup\VINDecoder;

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
            <input type="text" name="vin" value="<?= isset($_POST['vin']) ? $_POST['vin'] : null ?>"/>
            <input type="submit" value="Decode"/>
        </form>
        <hr/>
        <?php if(isset($decoder)): ?>
        <pre><?= $decoder->getManufacturer(); ?></pre>
        <?php endif; ?>
    </body>
</html>
