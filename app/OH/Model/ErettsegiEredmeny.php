<?php

namespace OH\Model;

use OH\Model\ErettsegiTargy;
use OH\Model\Szint;
use OH\Helper\KeyChecker;

class ErettsegiEredmeny
{

    use KeyChecker;

    private ErettsegiTargy $tantargy;
    private int $pontszam;


    public function __construct(ErettsegiTargy $targy, int $psz) {
        $this->tantargy = $targy;
        $this->pontszam = $psz;
    }

    public static function instanceFrom(Array $array): ?ErettsegiEredmeny
    {

        if(self::hasKeys($array, array('nev', 'tipus', 'eredmeny'))) {
            $name = $array['nev'];
            $level = Szint::instanceFrom($array['tipus']);
            $points = str_replace('%', '', $array['eredmeny']);

            if (!is_null($name) && !is_null($level) && is_numeric($points)) {
                $targy = new ErettsegiTargy($name, $level);
                return new ErettsegiEredmeny($targy, $points);
            }
        }

        return null;
    }

    public function getTantargy(): ErettsegiTargy
    {
        return $this->tantargy;
    }

    public function getPontszam(): int
    {
        return $this->pontszam;
    }


}
