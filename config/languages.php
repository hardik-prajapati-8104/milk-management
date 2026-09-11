<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | Every locale the app can be switched to. 'native' is what's shown in
    | the language switcher itself (always shown in its own script, not
    | translated), 'flag' is a purely decorative emoji, and 'font_class' is
    | applied to <html> so the right web font loads for the current script
    | (Gujarati/Devanagari render poorly in most default UI fonts).
    |
    */

    'supported' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'font_class' => 'lang-latin',
        ],
        'gu' => [
            'name' => 'Gujarati',
            'native' => 'ગુજરાતી',
            'flag' => '🇮🇳',
            'font_class' => 'lang-gujarati',
        ],
        'hi' => [
            'name' => 'Hindi',
            'native' => 'हिन्दी',
            'flag' => '🇮🇳',
            'font_class' => 'lang-devanagari',
        ],
    ],

    'default' => 'en',
];
