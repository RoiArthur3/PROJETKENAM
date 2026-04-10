<?php

namespace App\Helpers;

class OperationHelper
{
    public static function numberToWords($number)
    {
        if ($number === null || $number === '') return '';
        $n = (int)$number;
        if ($n == 0) return 'zéro';
        
        $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        
        if ($n < 20) return $units[$n];
        
        if ($n < 100) {
            $t = (int)($n / 10);
            $u = $n % 10;
            
            // Cas particuliers pour 70-79 et 90-99
            if ($t == 7) {
                if ($u == 0) return 'soixante-dix';
                if ($u == 1) return 'soixante et onze';
                return 'soixante-' . $units[10 + $u];
            }
            if ($t == 9) {
                if ($u == 0) return 'quatre-vingt-dix';
                return 'quatre-vingt-' . $units[10 + $u];
            }
            
            if ($u == 0) {
                if ($t == 8) return 'quatre-vingts';
                return $tens[$t];
            }
            
            if ($u == 1 && $t < 8) return $tens[$t] . ' et un';
            return $tens[$t] . '-' . $units[$u];
        }
        
        if ($n < 1000) {
            $h = (int)($n / 100);
            $r = $n % 100;
            $res = ($h == 1) ? 'cent' : $units[$h] . ' cent';
            if ($h > 1 && $r == 0) $res .= 's';
            if ($r == 0) return $res;
            return $res . ' ' . self::numberToWords($r);
        }
        
        if ($n < 1000000) {
            $m = (int)($n / 1000);
            $r = $n % 1000;
            $res = ($m == 1) ? 'mille' : self::numberToWords($m) . ' mille';
            if ($r == 0) return $res;
            return $res . ' ' . self::numberToWords($r);
        }
        
        if ($n < 1000000000) {
            $mi = (int)($n / 1000000);
            $r = $n % 1000000;
            $res = ($mi == 1) ? 'un million' : self::numberToWords($mi) . ' millions';
            if ($r == 0) return $res;
            return $res . ' ' . self::numberToWords($r);
        }
        
        return (string)$n;
    }
}
