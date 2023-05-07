<?php

namespace OH\Model;

use OH\Helper\KeyChecker;

const LANGUAGE_EXTRA_POINTS = array(
    'B2' => 28,
    'C1' => 40
);

class Nyelvvizsga
{

    use KeyChecker;

    private string $type;
    private string $language;

    public function __construct(string $type, string $language)
    {
        $this->type = $type;
        $this->language = $language;
    }

    public static function instanceFrom(array $array):  ? Nyelvvizsga
    {

        if (self::hasKeys($array, array('kategoria', 'tipus', 'nyelv'))) {
            $category = $array['kategoria'];
            $type = $array['tipus'];
            $language = $array['nyelv'];

            if (!is_null($category) &&
                !is_null($type) &&
                !is_null($language)
                && $category == 'Nyelvvizsga'
                && in_array($type, array_keys(LANGUAGE_EXTRA_POINTS))
            ) {
                return new Nyelvvizsga($type, $language);
            }
        }

        return null;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getLanguage(): string {
        return $this->language;
    }

    public function getExtraPoints(): int {
        return LANGUAGE_EXTRA_POINTS[$this->type];
    }

}
