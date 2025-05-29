<?php

namespace App\Helpers;

class PhoneHelper
{
    public function normalize(string $phone): string
    {
        // Залишаємо тільки цифри
        $digits = preg_replace('/\D+/', '', $phone);
        // Якщо починається з 380, повертаємо +380...
        if (strpos($digits, '380') === 0) {
            return '+'.$digits;
        }
        // Якщо починається з 0, замінюємо на +380
        if (strpos($digits, '0') === 0) {
            return '+380'.substr($digits, 1);
        }
        // Якщо вже з +380
        if (strpos($digits, '+380') === 0) {
            return '+'.$digits;
        }
        // Якщо 9 цифр (без коду), додаємо +380
        if (strlen($digits) === 9) {
            return '+380'.$digits;
        }
        // Інакше повертаємо як є
        return '+'.$digits;
    }
} 