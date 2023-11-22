<?php

namespace BADDIServices;

use Exception;

/**
 * VINDecoder Class
 *
 * PHP Version 7.3
 *
 * @category Class
 * @author   Mohamed BADDI <project@baddi.info>
 * @license  MIT License
 * @link     https://packagist.org/packages/baddiservices/vin-decoder
 */
class VINDecoder
{
    public string $vin;

    private $checkDigit;

    private $wmi;
    private $vds;
    private $vis;

    private array $transliteration = [];

    private array $weightedProduct = [];
    
    private $calculatedWeightedProduct = 0;

    /**
     * @throws Exception
     */
    public function __construct(string $vin)
    {
        // Validate the VIN length
        if(! $this->validate($vin)) {
            throw new Exception("Invalid VIN characters");
        }

        // Store the vin into this instance
        $this->vin = strtoupper($vin);

        // Validate the VIN
        if(! $this->checksum()) {
            throw new Exception("Invalid VIN");
        }

        // Parse the VIN details identifiers
        $this->wmi = substr($this->vin, 0, 3);
        $this->vds = substr($this->vin, 3, 6);
        $this->vis = substr($this->vin, 9, 8);
    }

    /**
     * Check if the VIN is valid
     *
     * @throws Exception
     */
    public function isValid() : bool
    {
        return $this->validate($this->vin);
    }

    /**
     * Check digit getter
     */
    public function getCheckDigit() : int
    {
        return $this->checkDigit;
    }

    /**
     * Extract the manufacturer brand name
     */
    public function getManufacturer() : ?string
    {
        // Load manufacturers list
        $manufactures = json_decode(file_get_contents(__DIR__ . "/data/manufacturers.json"), true);

        return ! empty($manufactures[$this->wmi]) ? ucwords($manufactures[$this->wmi]) : null;
    }

    /**
     * Get country by code
     */
    public function getCountry() : ?string
    {
        // Load countries list
        $countries = json_decode(file_get_contents(__DIR__ . "/data/countries.json"), true);

        return ! empty($countries[$this->getCountryCode()]) ? ucwords($countries[$this->getCountryCode()]) : null;
    }

    /**
     * Get year by code
     */
    public function getYear() : ?int
    {
        // Load years list
        $years = json_decode(file_get_contents(__DIR__ . "/data/years.json"), true);

        return $years[$this->vis[0]] ?? null;
    }

    /**
     * Extract serial number
     */
    public function getSerialNumber() : string
    {
        return substr($this->vin, 11, 6);
    }

    /**
     * Extract security code
     */
    public function getSecurityCode() : string
    {
        return substr($this->vin, 8, 1);
    }

    /**
     * Check the vin length and regex
     *
     * @throws Exception
     */
    private function validate(string $vin) : bool
    {
        // Verify if the vin corresponds to a vehicle manufactured before 1981
        if(strlen($vin) == 11) {
            throw new Exception("Information on vehicles manufactured before 1981 is limited");
        }

        if(strlen($vin) != VINConstants::VIN_LENGTH) {
            throw new Exception("VIN number must be 17 characters");
        }

        return (bool)preg_match('/^[a-zA-Z0-9]+$/', $vin);
    }

    /**
     * Find and replace illegal characters
     */
    private function illegalCharacters() : void
    {
        // Replace - and _ and whitespace
        $this->vin = str_replace('-', '', $this->vin);
        $this->vin = str_replace(' ', '', $this->vin);
        $this->vin = str_replace('_', 0, $this->vin);

        // Replace the illegal characters
        foreach(VINConstants::EXCLUDED_LETTERS as $letter){
            if(! str_contains($this->vin, $letter)) {
                continue;
            }

            $this->vin = str_replace($letter, ($letter === 'I' ? 1 : 0), $this->vin);
        }
    }

    /**
     * Check VIN sum and digit by transliteration and weighted product
     */
    private function checksum() : bool
    {
        // Ignore illegal characters
        $this->illegalCharacters();

        // Convert the VIN letters using the transliteration
        foreach(str_split($this->vin) as $letter){
            // Unknown transliteration
            if(ctype_alpha($letter) && isset(VINConstants::TRANSLITERATION[$letter])) {
                $this->transliteration[] = VINConstants::TRANSLITERATION[$letter];

                continue;
            }

            $this->transliteration[] = $letter;
        }

        // Calculate the weighted product by weighted factor
        foreach($this->transliteration as $key => $trans){
            $this->weightedProduct[] = VINConstants::WEIGHTED_FACTORS[$key + 1];
            $this->calculatedWeightedProduct += $trans * VINConstants::WEIGHTED_FACTORS[$key + 1];
        }

        // Check digit
        $check = substr($this->vin, VINConstants::CHECKSUM_POSITION, 1);
        $this->checkDigit = $this->calculatedWeightedProduct % VINConstants::CHECKSUM_FACTOR;

        return ($this->checkDigit === VINConstants::CHECKSUM && $check === VINConstants::CHECKSUM_LETTER)
            || (
                isset(VINConstants::WEIGHTED_FACTORS[$check])
                && ctype_alpha($check)
                && VINConstants::WEIGHTED_FACTORS[$check] == $mod
            )
            || ($this->checkDigit == $check);
    }

    /**
     * Get country code from the VIN number
     */
    private function getCountryCode() : string
    {
        return substr($this->vin, 0, 2);
    }
}