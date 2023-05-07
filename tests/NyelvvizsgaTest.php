<?php

use OH\Model\Nyelvvizsga;

use PHPUnit\Framework\TestCase;

final class NyelvvizsgaTest extends TestCase
{

    private $properInput = array(
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 'B2',
        'nyelv' => 'angol',
    );

    private $invalidCategory = array(
        'kategoria' => 'OKJ vizsga',
        'tipus' => 'B2',
        'nyelv' => 'angol',
    );

    private $missingCategory = array(
        'tipus' => 'B2',
        'nyelv' => 'angol',
    );

    private $invalidType = array(
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 127,
        'nyelv' => 'angol',
    );

    private $missingLanguage = array(
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 'C1'
    );


    public function testParsingProperData() {
        $nyv = Nyelvvizsga::instanceFrom($this->properInput);
        $this->assertNotNull($nyv);
    }

    public function testInvalidCategory() {
        $nyv = Nyelvvizsga::instanceFrom($this->invalidCategory);
        $this->assertNull($nyv);
    }

    public function testMissingCategory() {
        $nyv = Nyelvvizsga::instanceFrom($this->missingCategory);
        $this->assertNull($nyv);
    }

    public function testInvalidType() {
        $nyv = Nyelvvizsga::instanceFrom($this->invalidType);
        $this->assertNull($nyv);
    }

    public function testMissingLanguage() {
        $nyv = Nyelvvizsga::instanceFrom($this->missingLanguage);
        $this->assertNull($nyv);
    }
}