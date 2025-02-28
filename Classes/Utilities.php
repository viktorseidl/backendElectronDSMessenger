<?php

class Utilities
{
    public static function decimalToHexColor($decimal)
    {
        // Ensure the decimal is within the valid range for colors
        if ($decimal < 0 || $decimal > 16777215) {
            return "Invalid color value. Must be between 0 and 16777215.";
        }

        // Convert the decimal to a 6-character hex value
        $hex = str_pad(dechex($decimal), 6, "0", STR_PAD_LEFT);

        // Add the # to create a proper hex color code
        return "#" . strtoupper($hex);
    }
}

?>