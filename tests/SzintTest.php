<?php

use OH\Model\Szint;

use PHPUnit\Framework\TestCase;

final class SzintTest extends TestCase
{

    public function testParsingSuccess() {
        $kozep = Szint::instanceFrom('közép');
        $emelt = Szint::instanceFrom('emelt');

        $this->assertEquals(Szint::Kozep, $kozep);
        $this->assertEquals(Szint::Emelt, $emelt);
    }

    public function testParsingFail() {
        $invalid = Szint::instanceFrom('felső-közép');
        $this->assertNull($invalid);
    }

}