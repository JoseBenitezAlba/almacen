<?php

namespace App\Support;

class Money
{
    // 1250 -> "12,50 €"
    public static function format(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.') . ' €';
    }
}
