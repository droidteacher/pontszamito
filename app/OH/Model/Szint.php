<?php

namespace OH\Model;

enum Szint
{
    case Kozep;
    case Emelt;

    public static function instanceFrom(string $str): ?Szint {
        if('közép' == $str) {
            return Szint::Kozep;
        } else if('emelt' == $str) {
            return Szint::Emelt;
        } else {
            return null;
        }
    }
}

