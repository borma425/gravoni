<?php

namespace App\Helpers;

class ColorHelper
{
    protected static $colors = [
        'أسود' => '#000000', 'black' => '#000000',
        'أبيض' => '#FFFFFF', 'white' => '#FFFFFF',
        'أحمر' => '#EF4444', 'red' => '#EF4444',
        'أزرق' => '#3B82F6', 'blue' => '#3B82F6',
        'أخضر' => '#22C55E', 'green' => '#22C55E',
        'أصفر' => '#EAB308', 'yellow' => '#EAB308',
        'برتقالي' => '#F97316', 'orange' => '#F97316',
        'وردي' => '#EC4899', 'pink' => '#EC4899',
        'بنفسجي' => '#8B5CF6', 'purple' => '#8B5CF6',
        'رمادي' => '#6B7280', 'gray' => '#6B7280', 'grey' => '#6B7280',
        'بني' => '#92400E', 'brown' => '#92400E',
        'نيفي' => '#1E3A8A', 'navy' => '#1E3A8A',
        'بيج' => '#D4A574', 'beige' => '#D4A574',
        'كحلي' => '#1E3A5F',
        'سماوي' => '#06B6D4', 'cyan' => '#06B6D4',
        'ذهبي' => '#F59E0B', 'gold' => '#F59E0B',
        'فضي' => '#9CA3AF', 'silver' => '#9CA3AF',
        'زيتي' => '#4D7C0F', 'olive' => '#4D7C0F',
        'خمري' => '#7F1D1D', 'maroon' => '#7F1D1D',
        'تركواز' => '#14B8A6', 'turquoise' => '#14B8A6',
        'كاكي' => '#8B7355', 'khaki' => '#8B7355',
        'عنابي' => '#800020', 'burgundy' => '#800020',
        'موف' => '#9966CC', 'mauve' => '#9966CC',
        'سلموني' => '#FA8072', 'salmon' => '#FA8072',
        'كريمي' => '#FFFDD0', 'cream' => '#FFFDD0',
        'لافندر' => '#E6E6FA', 'lavender' => '#E6E6FA',
    ];

    public static function getHex(string $colorName): string
    {
        $colorLower = mb_strtolower(trim($colorName));
        return self::$colors[$colorLower] ?? '#94A3B8';
    }

    public static function getAllColors(): array
    {
        return self::$colors;
    }
}
