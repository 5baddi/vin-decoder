<?php

use BADDIGroup\VINDecoder;
use PHPUnit\Framework\TestCase;

/**
 * VINDecoderTest Class
 *
 * PHP Version 7.3
 *
 * @category Class
 * @author   Mohamed BADDI <project@baddi.info>
 * @license  MIT License
 * @link     https://packagist.org/packages/baddigroup/vin-decoder
 */
class VINDecoderTest extends TestCase
{
    /**
     * Test the instance creation
     * 
     * @return bool
     */
    public function testInitInstance()
    {
        // VIN example
        $vin = "JTEHT05J542053195";

        $vinDecoder = new VINDecoder($vin);
        $this->assertInstanceOf(VINDecoder::class, $vinDecoder);
    }
}