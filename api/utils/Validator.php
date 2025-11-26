<?php

/**
 * Input Validation Helper
 */
class Validator {
    
    /**
     * Validate email
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate required fields
     */
    public static function required($data, $fields) {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        return empty($missing) ? true : $missing;
    }
    
    /**
     * Validate date format (YYYYMMDD)
     */
    public static function dateFormat($date, $format = 'Ymd') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Validate account type
     */
    public static function accountType($type) {
        return in_array((int)$type, [1, 2, 3]); // 1=Current, 2=Savings, 3=Transmission
    }
    
    /**
     * Validate bank ID
     */
    public static function bankId($bankId) {
        return in_array((int)$bankId, [64, 65, 66, 67, 68, 69, 70, 71, 72]);
    }
    
    /**
     * Validate frequency code
     */
    public static function frequencyCode($code) {
        return in_array((int)$code, [1, 3, 4, 5]); // 1=Weekly, 3=Fortnightly, 4=Monthly, 5=Monthly by Rule
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitize($string, $maxLength = null) {
        $string = trim($string);
        if ($maxLength !== null) {
            $string = substr($string, 0, $maxLength);
        }
        return $string;
    }
}



