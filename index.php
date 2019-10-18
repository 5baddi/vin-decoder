<?php

    require('VINConstants.php');
    require('VINDecoder.php');

    use BADDI\VINDecoder;

    $decoder = new VINDecoder("WDYPE8CCXD5801598");

    echo $decoder->vin;