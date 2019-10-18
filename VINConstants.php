<?php

    namespace BADDI;

    class VINConstants
    {
        const VIN_LENGTH = 17;
        const EXCLUDED_LETTERS = ['I', 'O', 'Q'];
        const CHECKSUM_LETTER = 'X';
        const CHECKSUM = 10;
        const CHECKSUM_FACTOR = 11;
        const CHECKSUM_POSITION = 8;
        const TRANSLITERATION = [
            'A' => 1, 'J' => 1,
            'B' => 2, 'K' => 2, 'S' => 2,
            'C' => 3, 'L' => 3, 'T' => 3,
            'D' => 4, 'M' => 4, 'U' => 4,
            'E' => 5, 'N' => 5, 'V' => 5,
            'F' => 6, 'W' => 6,
            'G' => 7, 'P' => 7, 'X' => 7,
            'H' => 8, 'Y' => 8, 
            'R' => 9, 'Z' => 9,
        ];
        const WEIGHTEDFACTORS = [
            1 => 8, 10 => 9,
            2 => 7, 11 => 8,
            3 => 6, 12 => 7,
            4 => 5, 13 => 6,
            5 => 4, 14 => 5,
            6 => 3, 15 => 4,
            7 => 2, 16 => 3,
            8 => 10, 17 => 2,
            9 => 0,
        ];
    }