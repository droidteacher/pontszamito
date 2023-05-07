<?php

use OH\Service\Kalkulator;
use OH\Model\ErettsegiEredmeny;
use OH\Model\Kovetelmeny;
use OH\Model\PontszamitasNemLehetseges;
use OH\Model\PontszamitasEredmeny;
use OH\Model\Nyelvvizsga;

use PHPUnit\Framework\TestCase;


final class KalkulatorTest extends TestCase
{

    private $magyar15 = [
        'nev' => 'magyar nyelv és irodalom',
        'tipus' => 'közép',
        'eredmeny' => '15%',
    ];

    private $magyar87 = [
        'nev' => 'magyar nyelv és irodalom',
        'tipus' => 'közép',
        'eredmeny' => '87%',
    ];

    private $magyarEmelt82 = [
        'nev' => 'magyar nyelv és irodalom',
        'tipus' => 'emelt',
        'eredmeny' => '82%',
    ];

    private $tortenelem80 = [
        'nev' => 'történelem',
        'tipus' => 'közép',
        'eredmeny' => '80%',
    ];

    private $tortenelemEmelt75 = [
        'nev' => 'történelem',
        'tipus' => 'emelt',
        'eredmeny' => '75%',
    ];

    private $matek90 = [
        'nev' => 'matematika',
        'tipus' => 'közép',
        'eredmeny' => '90%',
    ];


    private $matekEmelt90 = [
        'nev' => 'matematika',
        'tipus' => 'emelt',
        'eredmeny' => '90%',
    ];

    private $angol94 = [
        'nev' => 'angol nyelv',
        'tipus' => 'közép',
        'eredmeny' => '94%',
    ];

    private $informatika95 = [
        'nev' => 'informatika',
        'tipus' => 'közép',
        'eredmeny' => '95%',
    ];

    private $fizika65 = [
        'nev' => 'fizika',
        'tipus' => 'közép',
        'eredmeny' => '65%',
    ];

    private $b2En = [
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 'B2',
        'nyelv' => 'angol',
    ];

    private $c1En = [
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 'C1',
        'nyelv' => 'angol',
    ];

    private $c1De = [
        'kategoria' => 'Nyelvvizsga',
        'tipus' => 'C1',
        'nyelv' => 'német',
    ];

    private $exampleData = [
        'valasztott-szak' => [
            'egyetem' => 'ELTE',
            'kar' => 'IK',
            'szak' => 'Programtervező informatikus',
        ],
        'erettsegi-eredmenyek' => [
            [
                'nev' => 'magyar nyelv és irodalom',
                'tipus' => 'közép',
                'eredmeny' => '70%',
            ],
            [
                'nev' => 'történelem',
                'tipus' => 'közép',
                'eredmeny' => '80%',
            ],
            [
                'nev' => 'matematika',
                'tipus' => 'emelt',
                'eredmeny' => '90%',
            ],
            [
                'nev' => 'angol nyelv',
                'tipus' => 'közép',
                'eredmeny' => '94%',
            ],
            [
                'nev' => 'informatika',
                'tipus' => 'közép',
                'eredmeny' => '95%',
            ],
            [
                'nev' => 'fizika',
                'tipus' => 'közép',
                'eredmeny' => '98%',
            ],
        ],
        'tobbletpontok' => [
            [
                'kategoria' => 'Nyelvvizsga',
                'tipus' => 'B2',
                'nyelv' => 'angol',
            ],
            [
                'kategoria' => 'Nyelvvizsga',
                'tipus' => 'C1',
                'nyelv' => 'német',
            ],
        ],
    ];
    

    // matematika erettsegi hianyzik
    public function testBaseSubjectMissing() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar15),
            ErettsegiEredmeny::instanceFrom($this ->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this ->angol94),
            ErettsegiEredmeny::instanceFrom($this ->informatika95)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $this->assertEquals(PontszamitasNemLehetseges::AlapTantargyHianyzik, $sut->calculate());
    }

    // magyar erettsegi a minimalis pontszam alatt
    public function testBaseSubjectsPoints() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar15),
            ErettsegiEredmeny::instanceFrom($this ->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this ->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this ->angol94),
            ErettsegiEredmeny::instanceFrom($this ->informatika95)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $this->assertEquals(PontszamitasNemLehetseges::HuszSzazalekAlattiEredmeny, $sut->calculate());
    }

    // angol erettsegi hianyzik; kotelezo lenne az anglisztikahoz 
    public function testMandatorySubjectMissing() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar87),
            ErettsegiEredmeny::instanceFrom($this ->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this ->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this ->informatika95)
        ), array(), Kovetelmeny::kovetelmenyAnglisztika());

        $this->assertEquals(PontszamitasNemLehetseges::KotelezoTantargyHianyzik, $sut->calculate());
    }

    // kotelezoen valaszthato tantargy hianyzik
    public function testOptionalSubjectMissing() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar87),
            ErettsegiEredmeny::instanceFrom($this ->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this ->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this ->angol94)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $this->assertEquals(PontszamitasNemLehetseges::ValaszthatoTantargyHianyzik, $sut->calculate());
    }

    // pontszamitas lehetseges (egyetlen kotelezoen valaszthato targy van)
    public function testCalculateSingleOptionalSubject() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar87),
            ErettsegiEredmeny::instanceFrom($this->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this->fizika65)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $expectedResult = new PontszamitasEredmeny(310, 50);

        $this->assertEquals($expectedResult, $sut->calculate());
    }

    // tobb kotelezoen valaszthato tantargy is van, a legjobban sikerult szamit
    public function testCalculateMoreOptionalSubject() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyar87),
            ErettsegiEredmeny::instanceFrom($this->tortenelem80),
            ErettsegiEredmeny::instanceFrom($this ->informatika95),
            ErettsegiEredmeny::instanceFrom($this->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this->fizika65)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $expectedResult = new PontszamitasEredmeny(370, 50);

        $this->assertEquals($expectedResult, $sut->calculate());
    }

    // emelt szintu erettsegiert adhato tobblet pont maximuma 100
    public function testCalculateHighLevelPointsOver100() {
        $sut = new Kalkulator(array(
            ErettsegiEredmeny::instanceFrom($this->magyarEmelt82),
            ErettsegiEredmeny::instanceFrom($this->tortenelemEmelt75),
            ErettsegiEredmeny::instanceFrom($this->matekEmelt90),
            ErettsegiEredmeny::instanceFrom($this->fizika65)
        ), array(), Kovetelmeny::kovetelmenyProgramtervezo());

        $expectedResult = new PontszamitasEredmeny(310, 100);

        $this->assertEquals($expectedResult, $sut->calculate());
    }

    // van nyelvvizsgaert adhato tobblet pont is
    public function testCalculateWithAdditionalLanguagePoints() {
        $sut = new Kalkulator(
            array(
                ErettsegiEredmeny::instanceFrom($this->magyar87),
                ErettsegiEredmeny::instanceFrom($this->tortenelem80),
                ErettsegiEredmeny::instanceFrom($this->matek90),
                ErettsegiEredmeny::instanceFrom($this->fizika65)
            ), 
            array(
                Nyelvvizsga::instanceFrom($this->b2En),
                Nyelvvizsga::instanceFrom($this->c1De),
            ), Kovetelmeny::kovetelmenyProgramtervezo());

        $expectedResult = new PontszamitasEredmeny(310, 68);

        $this->assertEquals($expectedResult, $sut->calculate());
    }

    // azonos nyelvbol B2 es C1 is
    public function testCalculateWithSameLanguageB2AndC1() {
        $sut = new Kalkulator(
            array(
                ErettsegiEredmeny::instanceFrom($this->magyar87),
                ErettsegiEredmeny::instanceFrom($this->tortenelem80),
                ErettsegiEredmeny::instanceFrom($this->matek90),
                ErettsegiEredmeny::instanceFrom($this->fizika65)
            ), 
            array(
                Nyelvvizsga::instanceFrom($this->b2En),
                Nyelvvizsga::instanceFrom($this->c1En),
            ), Kovetelmeny::kovetelmenyProgramtervezo());

        $expectedResult = new PontszamitasEredmeny(310, 40);

        $this->assertEquals($expectedResult, $sut->calculate());
    }

    // Kalkulator peldany inicializalas array literallal
    public function testKalkulatorInstanceCreation() {
        $sut = Kalkulator::instanceWithData($this->exampleData);
        $expectedResult = new PontszamitasEredmeny(376, 100);

        $this->assertEquals($expectedResult, $sut->calculate());
        $this->assertEquals("476 (376 alappont + 100 többletpont)", $expectedResult->getHumanReadableResult());
    }

}