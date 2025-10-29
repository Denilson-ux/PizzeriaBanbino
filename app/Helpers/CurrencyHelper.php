<?php

if (!function_exists('formatCurrency')) {
    /**
     * Formatear moneda en bolivianos
     * 
     * @param float $amount
     * @param int $decimals
     * @param bool $showSymbol
     * @return string
     */
    function formatCurrency($amount, $decimals = 2, $showSymbol = true) {
        $formatted = number_format($amount, $decimals, '.', ',');
        return $showSymbol ? 'Bs ' . $formatted : $formatted;
    }
}

if (!function_exists('formatCurrencyShort')) {
    /**
     * Formatear moneda en bolivianos sin decimales para montos grandes
     * 
     * @param float $amount
     * @return string
     */
    function formatCurrencyShort($amount) {
        return 'Bs ' . number_format($amount, 0, '.', ',');
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Obtener símbolo de moneda
     * 
     * @return string
     */
    function getCurrencySymbol() {
        return config('app.currency_symbol', 'Bs');
    }
}

if (!function_exists('getCurrencyName')) {
    /**
     * Obtener nombre de moneda
     * 
     * @return string
     */
    function getCurrencyName() {
        return config('app.currency_name', 'Bolivianos');
    }
}

if (!function_exists('getCurrencyCode')) {
    /**
     * Obtener código ISO de moneda
     * 
     * @return string
     */
    function getCurrencyCode() {
        return config('app.currency_code', 'BOB');
    }
}