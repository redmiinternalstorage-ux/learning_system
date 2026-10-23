<?php

if (!function_exists('ordinal_day')) {
    /**
     * Returns ordinal suffix for a day number (1st, 2nd, 3rd, etc.)
     */
    function ordinal_day(int $number): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number . 'th';
        }
        return $number . $ends[$number % 10];
    }
}
