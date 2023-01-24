<?php

namespace App\Support\DriverGenerator;

use InvalidArgumentException;

class Locales
{
    private const LOCALES = [
        'ar' => ['EG', 'JO', 'SA'],
        'bg' => ['BG'],
        'cs' => ['CZ'],
        'da' => ['DK'],
        'de' => ['AT', 'CH', 'DE'],
        'el' => ['CY', 'GR'],
        'en' => ['AU', 'CA', 'GB', 'NZ', 'US'],
        'es' => ['AR', 'ES', 'PE', 'VE'],
        'et' => ['EE'],
        'fa' => ['IR'],
        'fi' => ['FI'],
        'fr' => ['BE', 'CA', 'CH', 'FR'],
        'he' => ['IL'],
        'hr' => ['HR'],
        'hu' => ['HU'],
        'hy' => ['AM'],
        'id' => ['ID'],
        'is' => ['IS'],
        'it' => ['CH', 'IT'],
        'ja' => ['JP'],
        'ka' => ['GE'],
        'kk' => ['KZ'],
        'ko' => ['KR'],
        'lt' => ['LT'],
        'lv' => ['LV'],
        'mn' => ['MN'],
        'ms' => ['MY'],
        'nb' => ['NO'],
        'ne' => ['NP'],
        'nl' => ['BE', 'NL'],
        'pl' => ['PL'],
        'pt' => ['BR', 'PT'],
        'ro' => ['MD', 'RO'],
        'ru' => ['RU'],
        'sk' => ['SK'],
        'sl' => ['SI'],
        'sr' => ['Latn_RS'],
        'sv' => ['SE'],
        'th' => ['TH'],
        'tr' => ['TR'],
        'uk' => ['UA'],
        'vi' => ['VN'],
        'zh' => ['CN', 'TW'],
    ];

    private const LANGUAGES = [
        'ar' => 'Arabic',
        'bg' => 'Bulgarian',
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'en' => 'English',
        'es' => 'Spanish',
        'et' => 'Estonian',
        'fa' => 'Persian',
        'fi' => 'Finnish',
        'fr' => 'French',
        'hr' => 'Croatian',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'id' => 'Indonesian',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'ka' => 'Georgian',
        'kk' => 'Kazakh',
        'ko' => 'Korean',
        'lt' => 'Lithuanian',
        'lv' => 'Latvian',
        'mn' => 'Mongolian',
        'ms' => 'Malay',
        'nb' => 'Norwegian',
        'ne' => 'Nepali',
        'nl' => 'Dutch',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sr' => 'Serbian',
        'sv' => 'Swedish',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'vi' => 'Vietnamese',
        'zh' => 'Chinese',
    ];

    public static function getLanguages(): array
    {
        return self::LANGUAGES;
    }

    public static function getRandomLanguage(): string
    {
        return array_rand(self::getLanguages());
    }

    public static function getLocalesForLanguage(string $language): array
    {
        if (! in_array($language, array_keys(self::LOCALES))) {
            throw new InvalidArgumentException("Language [$language] is invalid");
        }

        return self::LOCALES[$language];
    }

    public static function getRandomLocaleForLanguage(string $language): Locale
    {
        $locales = self::getLocalesForLanguage($language);
        $locale = $locales[array_rand($locales)];

        return new Locale($language, $locale);
    }
}
