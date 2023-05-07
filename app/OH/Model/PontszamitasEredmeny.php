<?php

namespace OH\Model;

class PontszamitasEredmeny
{

    private int $alappont;
    private int $tobbletpont;

    public function __construct(int $alap, int $tobblet) {
        $this->alappont = $alap;
        $this->tobbletpont = $tobblet;
    }

    public function getAlappont(): int {
        return $this->alappont;
    }

    public function getTobbletpont(): int {
        return $this->tobbletpont;
    }

    public function getOsszpontszam(): int {
        return $this->alappont + $this->tobbletpont;
    }

    public function getHumanReadableResult(): string {
        $sum = $this->getOsszpontszam();
        $a = $this->alappont;
        $t = $this->tobbletpont;
        return "$sum ($a alappont + $t többletpont)";
    }
}