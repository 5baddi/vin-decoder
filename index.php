<?php

    require "vendor/autoload.php";

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
        <pre>Manufacturer: <?= $decoder->getManufacturer(); ?></pre>
        <pre>Check digit: <?= $decoder->getCheckDigit(); ?></pre>
        <?php endif; ?>
    </body>
</html>
