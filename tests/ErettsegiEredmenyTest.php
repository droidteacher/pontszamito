<?php

use OH\Model\ErettsegiEredmeny;
use OH\Model\Szint;

use PHPUnit\Framework\TestCase;

final class ErettsegiEredmenyTest extends TestCase
{

    private $properInput = array(
        'nev' => 'történelem',
        'tipus' => 'közép',
        'eredmeny' => '80%',
    );

    public function testProperInput() {
        $eredmeny = ErettsegiEredmeny::instanceFrom($this->properInput);
        $this->assertNotNull($eredmeny);
    }

}