<?php

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($amount)
    {
        $amount = round($amount, 2);
        $amount_parts = explode('.', $amount);
        $integer = $amount_parts[0];
        $decimal = isset($amount_parts[1]) ? $amount_parts[1] : '00';

        $last3 = substr($integer, -3);
        $restUnits = substr($integer, 0, -3);

        if ($restUnits != '') {
            $last3 = ',' . $last3;
        }

        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);

        return '₹' . $restUnits . $last3 . '.' . $decimal;
    }
}
