<?php

/**
 * Contract Reference Generator
 * 
 * Format: First 4 characters = Merchant GID in HEX
 *         Next 4 characters = Calculated base date
 *         Last 6 characters = Teller for the day
 */
class ContractReference {
    
    /**
     * Generate contract reference
     * 
     * @param int $merchantGid Merchant Global ID
     * @param string $baseDate Base date in YYYYMMDD format (optional, defaults to today)
     * @param int $teller Teller number for the day (optional, defaults to random)
     * 
     * @return string 14-character contract reference
     */
    public static function generate($merchantGid, $baseDate = null, $teller = null) {
        // Convert merchant GID to 4-character hex (uppercase)
        $gidHex = strtoupper(str_pad(dechex($merchantGid), 4, '0', STR_PAD_LEFT));
        
        // Use provided base date or today's date
        if ($baseDate === null) {
            $baseDate = date('Ymd');
        }
        
        // Extract last 4 characters of date (MMDD)
        $datePart = substr($baseDate, -4);
        
        // Generate or use provided teller (6 digits)
        if ($teller === null) {
            // Generate a 6-digit number (could be sequential or random)
            // For simplicity, using a combination of timestamp and random
            $teller = str_pad((time() % 1000000) + rand(0, 999), 6, '0', STR_PAD_LEFT);
        } else {
            $teller = str_pad($teller, 6, '0', STR_PAD_LEFT);
        }
        
        return $gidHex . $datePart . $teller;
    }
    
    /**
     * Validate contract reference format
     */
    public static function validate($reference) {
        return preg_match('/^[0-9A-F]{14}$/i', $reference) === 1;
    }
}



