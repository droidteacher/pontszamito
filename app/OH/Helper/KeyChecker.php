<?php

namespace OH\Helper;

trait KeyChecker {

    public static function hasKeys(Array $array, Array $expectedKeys) {
        foreach($expectedKeys as $key) {
            if(!array_key_exists($key, $array)) {
                return false;
            }
        }

        return true;
    }

}