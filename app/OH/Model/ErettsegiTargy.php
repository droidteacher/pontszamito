<?php

namespace OH\Model;

use OH\Model\Szint;

class ErettsegiTargy {

    private string $tantargyNev;
    private Szint $szint;

    public function __construct(string $name, Szint $szint) {
        $this->tantargyNev = $name;
        $this->szint = $szint;
    }

    public function getTantargyNev(): string {
        return $this->tantargyNev;
    }

    public function getSzint(): Szint {
        return $this->szint;
    }

}