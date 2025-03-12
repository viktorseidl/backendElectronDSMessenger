<?php

class Calendar{

    public function __construct(){

    }
    public function decimalToHexColor($decimal): string {
        // Ensure the decimal is within the valid range for colors
        if ($decimal < 0 || $decimal > 16777215) {
            return "Invalid color value. Must be between 0 and 16777215.";
        }
        // Convert the decimal to a 6-character hex value
        $hex = str_pad(dechex($decimal), 6, "0", STR_PAD_LEFT);
        // Add the # to create a proper hex color code
        return "#" . strtoupper($hex);
    }
      
    public function hexColorToDecimal($hexColor): float|int|string {
        // Remove # if present
        $hexColor = ltrim(strtoupper($hexColor), '#');
        
        // Validate hex color format
        if (!preg_match('/^[0-9A-F]{6}$/', $hexColor)) {
            return false;
        }
        
        // Convert hex to decimal
        return hexdec($hexColor);
    }
    public function getDurationIndicator($startTime=null, $endTime=null, $type=null):int {
        //Only allow with full params
        if($startTime==null&&$endTime==null&&$type==null) return 0;
        // Convert to timestamps
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        
        // Calculate total minutes
        $totalMinutes = ($endTimestamp - $startTimestamp) / 60;
        
        // Handle different types
        switch ($type) {
            case 'd':
            case 'w':
                return (int) ($totalMinutes / 15);
            case 'm':
                return (int) ($totalMinutes / 1440);
            case 'y':
                return 0;
            case 'l':
                return (int) $totalMinutes;
            default:
                return 0; // Invalid type
        }
    }
}


?>