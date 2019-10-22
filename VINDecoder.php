<?php

    namespace BADDI;

    use Exception;

    class VINDecoder
    {
        public $vin;

        private $wmi;
        private $vds;
        private $vis;

        private $transliteration = [];

        private $weightedProduct = [];
        
        private $calculatedWeightedProduct = 0;

        /**
         * Class constructor
         *
         * @param string $vin
         */
        public function __construct(string $vin)
        {
            // Validate the VIN length
            if(!$this->isValid($vin))
                throw new Exception("Invalid VIN characters");

            // Store the vin into this instance
            $this->vin = strtoupper($vin);

            // Validate the VIN
            if(!$this->checksum())
                throw new Exception("Invalid VIN");

            // Parse the VIN details identifiers
            $this->wmi = substr($this->vin, 0, 3);
            $this->vds = substr($this->vin, 3, 6);
            $this->vis = substr($this->vin, 9, 8);
        }

        /**
         * Check the vin length
         *
         * @param string $vin
         * @return boolean
         */
        private function isValid(string $vin) : bool
        {
            // Verify if the vin corresponds to a vehicle manufactured before 1981
            if(strlen($vin) == 11)
                throw new Exception("Information on vehicles manufactured before 1981 is limited");
            elseif(strlen($vin) != VINConstants::VIN_LENGTH)
                throw new Exception("VIN number must be 17 characters");

            return (bool)preg_match('/^[a-zA-Z0-9]+$/', $vin);
        }

        /**
         * Find and replace illegal characters
         *
         * @return void
         */
        private function illegalCharacters() : void
        {
            // Replace - and _ and whitespace
            $this->vin = str_replace('-', '', $this->vin);
            $this->vin = str_replace(' ', '', $this->vin);
            $this->vin = str_replace('_', 0, $this->vin);

            // Replace the illegal characters
            foreach(VINConstants::EXCLUDED_LETTERS as $letter){
                if(strpos($this->vin, $letter) !== false)
                    $this->vin = str_replace($letter, ($letter === 'I' ? 1 : 0), $this->vin);
            }
        }

        /**
         * Check VIN sum and digit by transliteration and weighted product
         *
         * @return boolean
         */
        private function checksum() : bool
        {
            // Ignore illegal characters
            $this->illegalCharacters();

            // Convert the VIN letters using the transliteration
            foreach(str_split($this->vin) as $letter){
                // Unknown transliteration
                if(ctype_alpha($letter) && isset(VINConstants::TRANSLITERATION[$letter]))
                    $this->transliteration[] = VINConstants::TRANSLITERATION[$letter];
                else
                    $this->transliteration[] = $letter;
            }

            // Calculate the weighted product by wieghted factor
            foreach($this->transliteration as $key => $trans){
                $this->weightedProduct[] = VINConstants::WEIGHTEDFACTORS[$key + 1];
                $this->calculatedWeightedProduct += $trans * VINConstants::WEIGHTEDFACTORS[$key + 1];
            }

            // Check digit
            $check = substr($this->vin, VINConstants::CHECKSUM_POSITION, 1);
            $mod = $this->calculatedWeightedProduct % VINConstants::CHECKSUM_FACTOR;

            // Verify the vin is valid
            if($mod == VINConstants::CHECKSUM && $check === VINConstants::CHECKSUM_LETTER)
                return true;
            elseif(isset(VINConstants::WEIGHTEDFACTORS[$check]) && ctype_alpha($check) && VINConstants::WEIGHTEDFACTORS[$check] == $mod)
                return true;
            elseif($mod == $check)
                return true;

            return false;
        }
    }