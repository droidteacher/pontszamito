<?php

namespace OH\Model;

use OH\Model\ErettsegiTargy;
use OH\Model\Szint;

class Kovetelmeny
{

    private ErettsegiTargy $kotelezoTargy;
    private array $kotelezoenValaszthatoTargyak;

    public function __construct(ErettsegiTargy $kotelezo, array $valaszthato)
    {
        $this->kotelezoTargy = $kotelezo;
        $this->kotelezoenValaszthatoTargyak = $valaszthato;
    }

    public function getKotelezoTargy(): ErettsegiTargy {
        return $this->kotelezoTargy;
    }

    public function getKotelezoenValaszthatoTargyak(): Array {
        return $this->kotelezoenValaszthatoTargyak;
    }

    public static function kovetelmenyAltalanos() {
        return array(
            new ErettsegiTargy('magyar nyelv és irodalom', Szint::Kozep),
            new ErettsegiTargy('történelem', Szint::Kozep),
            new ErettsegiTargy('matematika', Szint::Kozep),
        );
    }

    public static function kovetelmenyAnglisztika() {
        return new Kovetelmeny(
            new ErettsegiTargy('angol', Szint::Emelt),
            array(
                new ErettsegiTargy('francia', Szint::Kozep),
                new ErettsegiTargy('német', Szint::Kozep),
                new ErettsegiTargy('olasz', Szint::Kozep),
                new ErettsegiTargy('orosz', Szint::Kozep),
                new ErettsegiTargy('spanyol', Szint::Kozep),
                new ErettsegiTargy('történelem', Szint::Kozep),
            )
        );
    }

    public static function kovetelmenyProgramtervezo() {
        return new Kovetelmeny(
            new ErettsegiTargy('matematika', Szint::Kozep),
            array(
                new ErettsegiTargy('biológia', Szint::Kozep),
                new ErettsegiTargy('fizika', Szint::Kozep),
                new ErettsegiTargy('informatika', Szint::Kozep),
                new ErettsegiTargy('kémia', Szint::Kozep),
            )
        );
    }

}
