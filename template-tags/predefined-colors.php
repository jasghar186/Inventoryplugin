<?php
    function preDefinedColors($color) {
        $array = array(
            "000000" => "Black",
            "0000FF" => "Blue",
            "008080" => "Teal",
            "FFFFFF" => "White",
            "C0C0C0" => "Silver",
            "D3D3D3" => "Metallic",
            "808080" => "Graphite",
            "CD7F32" => "Bronze",
            "E5E4E2" => "Platinum",
            "B0B0B0" => "Granite",
            "808080" => "Gray",
            "800020" => "Velvet",
            "FF0000" => "Red",
            "EAE0C8" => "Pearl",
            "B3B191" => "G6o",
            "008000" => "Green",
            "8B4513" => "Rock",
            "71A6D2" => "Ice",
            "36454F" => 'Charcoal',
            "848482" => 'Graphite',
            "555D50" => 'Ebony',
            "B2BEB5" => 'Ash',
            "0C0B1D" => 'Diesel',
            "1F262A" => 'Dark',
        );
        foreach ($array as $key => $value) {
            if (strtolower($value) === $color) {
                return ['key' => $key, 'value' => $value];
            }
        }
        return null;
    }