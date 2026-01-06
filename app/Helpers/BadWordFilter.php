<?php

namespace App\Helpers;

class BadWordFilter
{
    /**
     * Danh sách từ cấm (có thể mở rộng)
     */
    protected static array $badWords = [
        // ===== Tiếng Việt =====
        'địt','đụ','đéo','đéo mẹ','đcm','dcm','dm',
        'cặc','buồi','lồn','lol','loz','chim','cu',
        'ngu','ngu học','óc chó','óc lợn','não chó','đần','thiểu năng',
        'vl','vcl','cc','cl','cmm','đmm','dmvl','vãi lồn','vãi lol',
        'đĩ','đĩ mẹ','đĩ chó','đĩ điếm','đĩ thõa',
        'l0n','lo*n','c*a*c','d!t','đ!t','đ*o','d.m','đ.m',
    
        // ===== English =====
        'fuck','fucking','motherfucker',
        'shit','bullshit','asshole','bitch','bastard',
        'dick','cock','pussy',
        'son of a bitch','piece of shit',
        'dumbass','retard','moron','jerk',
        'fck','f*ck','sh!t','bi7ch','a$$hole','pu$$y','d1ck',
    ];
    
    /**
     * Lọc từ tục -> ***
     */
    public static function filter(string $text): string
    {
        foreach (self::$badWords as $word) {
            // regex không phân biệt hoa thường, có dấu
            $pattern = '/\b' . preg_quote($word, '/') . '\b/ui';

            $text = preg_replace_callback($pattern, function ($matches) {
                return str_repeat('*', mb_strlen($matches[0]));
            }, $text);
        }

        return $text;
    }
}
