<?php

use OH\Model\Szak;

use PHPUnit\Framework\TestCase;

final class SzakTest extends TestCase
{

    private $properInput = array(
        'egyetem' => 'ELTE',
        'kar' => 'IK',
        'szak' => 'Programtervező informatikus',
    );

    public function testProperInput() {
        $szak = Szak::instanceFrom($this->properInput);
        $this->assertNotNull($szak);
    }
}