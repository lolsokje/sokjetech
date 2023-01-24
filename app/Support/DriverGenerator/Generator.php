<?php

namespace App\Support\DriverGenerator;

use App\Models\Universe;
use DateTimeImmutable;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Str;

class Generator
{
    private const DEFAULT_DATE_FORMAT = 'Y-m-d';
    private const DEFAULT_READABLE_DATE_FORMAT = 'F jS, Y';

    private FakerGenerator $faker;
    private Locale $locale;
    private int $localeCount;

    public function __construct(
        private ?string $language = null,
        readonly private ?string $gender = null,
    ) {
        if (!$this->language) {
            $this->language = Locales::getRandomLanguage();
        }

        $this->localeCount = count(Locales::getLocalesForLanguage($this->language));
        $this->getNewLocale();
    }

    public function generate(DateTimeImmutable $start, DateTimeImmutable $end, ?int $amount = 1): array
    {
        $drivers = [];

        for ($i = 1; $i <= $amount; $i++) {
            $drivers[] = [
                'first_name' => $this->faker->firstName($this->gender),
                'last_name' => $this->faker->lastName(),
                'dob' => $this->getBirthday($start, $end),
                'country' => Str::upper($this->locale->country()),
                'shared' => false,
            ];

            if ($this->localeCount > 1) {
                $this->getNewLocale();
            }
        }

        return $drivers;
    }

    public function persist(Universe $universe, array $drivers): void
    {
        foreach ($drivers as $driver) {
            $universe->drivers()->create($driver);
        }
    }

    private function getBirthday(DateTimeImmutable $start, DateTimeImmutable $end): string
    {
        return $this->faker->dateTimeBetween(
            $start->format(self::DEFAULT_DATE_FORMAT),
            $end->format(self::DEFAULT_DATE_FORMAT),
        )->format(self::DEFAULT_READABLE_DATE_FORMAT);
    }

    private function getNewLocale(): void
    {
        $this->locale = Locales::getRandomLocaleForLanguage($this->language);
        $this->faker = Factory::create($this->locale->locale());
    }
}
