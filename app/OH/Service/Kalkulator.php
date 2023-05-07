<?php

namespace OH\Service;

use OH\Model\Kovetelmeny;
use OH\Model\ErettsegiTargy;
use OH\Model\ErettsegiEredmeny;
use OH\Model\PontszamitasNemLehetseges;
use OH\Model\PontszamitasEredmeny;
use OH\Model\Nyelvvizsga;
use OH\Model\Szint;

use OH\Helper\KeyChecker;

const HIGH_LEVEL_MULTIPLIER = 50;

class Kalkulator {

    use KeyChecker;

    private Array $erettsegiEredmenyek;
    private Array $nyelvvizsgak;
    private Kovetelmeny $kovetelmeny;

    public function __construct(Array $eredmenyek, Array $nyelvvizsgak, Kovetelmeny $kovetelmeny) {
        $this->erettsegiEredmenyek = $eredmenyek;
        $this->nyelvvizsgak = $nyelvvizsgak;
        $this->kovetelmeny = $kovetelmeny;
    }

    public static function instanceWithData(array $array): ?Kalkulator
    {
        if (self::hasKeys($array, array('valasztott-szak', 'erettsegi-eredmenyek', 'tobbletpontok'))) {
            $szakNeve = $array['valasztott-szak']['szak'];
            $eredmenyek = array();
            foreach($array['erettsegi-eredmenyek'] as $eredmeny) {
                $eredmenyek[] = ErettsegiEredmeny::instanceFrom($eredmeny);
            }
            $nyelvvizsgak = array();
            foreach($array['tobbletpontok'] as $nyv) {
                $nyelvvizsgak[] = Nyelvvizsga::instanceFrom($nyv);
            }

            $kovetelmeny = null;
            if ($szakNeve == 'Programtervező informatikus') {
                $kovetelmeny = Kovetelmeny::kovetelmenyProgramtervezo();
            } else if($szakNeve == 'Anglisztika') {
                $kovetelmeny = Kovetelmeny::kovetelmenyAnglisztika();
            }

            if (!is_null($kovetelmeny)) {
                return new Kalkulator($eredmenyek, $nyelvvizsgak, $kovetelmeny);
            }

            return null;
            
        }
    }

    public function calculate(): Object {
        if (!$this->checkBaseSubjects()) {
            // valamelyik alapveto targybol (magyar, matek, tortenelem) nem erettsigizett
            return PontszamitasNemLehetseges::AlapTantargyHianyzik;
        }

        if (!$this->checkBaseSubjectsPoints()) {
            // valamelyik alapveto targybol 20% alatti eredmeny
            return PontszamitasNemLehetseges::HuszSzazalekAlattiEredmeny;
        }

        if (!$this->checkMandatorySubject()) {
            // kotelezo tantargybol nem tett erettsegit
            return PontszamitasNemLehetseges::KotelezoTantargyHianyzik;
        }
        
        if (!$this->checkOptionalSubject()) {
            // egyetlen kotelezoen valaszthato tantargybol sem tett erettsegit
            return PontszamitasNemLehetseges::ValaszthatoTantargyHianyzik;
        }

        $basePoints = $this->calculateBasePoints();
        $additionalPoints = $this->calculateAdditionalPoints();

        return new PontSzamitasEredmeny($basePoints, $additionalPoints);
    }

    private function calculateBasePoints(): int {
        $a = 0;
        $b = 0;
        $mandatoryResult = $this->getMandatorySubjectResult();
        if (!is_null($mandatoryResult)) {
            $a = $mandatoryResult->getPontszam();
        }
        
        $optionalResults = $this->getOptionalSubjectResults();
        
        if (count($optionalResults) > 0) {
            foreach($optionalResults as $eredmeny) {
                if ($b < $eredmeny->getPontszam() ) {
                    $b = $eredmeny->getPontszam();
                }
            }
        }

        return 2 * ($a + $b);
    }

    private function calculateAdditionalPoints(): int {
        $sum = $this->calculateHighLevelPoints() + $this->calculateLanguagePoints();
        if ($sum <= 100) {
            return $sum;
        } else {
            return 100;
        }
    }

    // nyelvvizsgaert adhato tobblet pontok
    private function calculateLanguagePoints(): int {
        $points = 0;

        $resultsByLanguage = array();

        foreach($this->nyelvvizsgak as $nyv) {
            $lang = $nyv->getLanguage();
            $type = $nyv->getType();
            
            if (!array_key_exists($lang, $resultsByLanguage)) {
                $resultsByLanguage[$lang] = $nyv;
            } else {
                if ($type == 'C1') {
                    $resultsByLanguage[$lang] = $nyv;
                }
            }
        }

        foreach($resultsByLanguage as $nyv) {
            $points += $nyv->getExtraPoints();
        }

        return $points;
    }

    // emelt szintu erettsegiert adhato tobblet pontok
    private function calculateHighLevelPoints(): int {
        $matches = array_filter(
            $this->erettsegiEredmenyek,
            function($eredmeny) {
                return $eredmeny->getTantargy()->getSzint() == Szint::Emelt;
            }
        );

        // var_dump($matches);

        return count($matches) * HIGH_LEVEL_MULTIPLIER;
    }

    // Amennyiben a kötelező tárgyból...
    // nem tett érettségit a hallgató, úgy a pontszámítás nem lehetséges
    private function checkMandatorySubject(): bool {
        $tantargyNev = $this->kovetelmeny->getKotelezoTargy()->getTantargyNev();

        // var_dump($tantargyNev);

        $matches = array_filter(
            $this->erettsegiEredmenyek,
                function($eredmeny) use ($tantargyNev) {
                   return $eredmeny->getTantargy()->getTantargyNev() == $tantargyNev;
                }
        );

        return count($matches) == 1;
    }

    // Amennyiben ... egyetlen kötelezően választható tárgyból sem tett érettségit a hallgató, 
    // úgy a pontszámítás nem lehetséges
    private function checkOptionalSubject(): bool {
        foreach($this->kovetelmeny->getKotelezoenValaszthatoTargyak() as $valaszthatoTargy) {
            $matches = array_filter(
                $this->erettsegiEredmenyek,
                function($eredmeny) use ($valaszthatoTargy) {
                    return $eredmeny->getTantargy()->getTantargyNev() == $valaszthatoTargy->getTantargyNev() && 
                        $eredmeny->getTantargy()->getSzint() == $valaszthatoTargy->getSzint();
                }
            );

            if (count($matches) > 0) {
                return true;
            }
        }

        return false;
    }


    // Amennyiben valamely tárgyból 20% alatt teljesített a felvételiző, úgy sikertelen az érettségi eredménye 
    // és a pontszámítás nem lehetséges.
    private function checkBaseSubjectsPoints(): bool {
        $alaptargyEredmenyek = $this->getBaseSubjects();

        foreach($alaptargyEredmenyek as $eredmeny) {
            // var_dump($eredmeny);
            if ($eredmeny->getPontszam() < 20) {
                return false;
            }
        }

        return true;
    }

    // A jelentkezőknek a következő tárgyakból kötelező érettségi vizsgát tennie: 
    // magyar nyelv és irodalom, történelem és matematika egyéb esetben a pontszámítás nem lehetséges.
    private function checkBaseSubjects(): bool {
        $teljesitettAlaptargyak = $this->getBaseSubjects();
        return count($teljesitettAlaptargyak) == count(Kovetelmeny::kovetelmenyAltalanos());
    }

    private function getBaseSubjects(): Array {
        $tantargyNevek = array();

        foreach(Kovetelmeny::kovetelmenyAltalanos() as $mandatorySubject) {
            $tantargyNevek[] = $mandatorySubject->getTantargyNev();
        }

        $matches = array_filter(
            $this->erettsegiEredmenyek,
                function($eredmeny) use ($tantargyNevek) {
                   return in_array($eredmeny->getTantargy()->getTantargyNev(), $tantargyNevek);
                }
        );

        return $matches;
    }


    private function getMandatorySubjectResult(): ?ErettsegiEredmeny {
        $tantargyNev = $this->kovetelmeny->getKotelezoTargy()->getTantargyNev();

        $matches = array_filter(
            $this->erettsegiEredmenyek,
                function($eredmeny) use ($tantargyNev) {
                   return $eredmeny->getTantargy()->getTantargyNev() == $tantargyNev;
                }
        );

        $keys = array_keys($matches);
        if (count($keys) > 0) {
            return $matches[$keys[0]];
        }

        return null;
    }

    private function getOptionalSubjectResults(): Array {
        $results = array();

        foreach($this->kovetelmeny->getKotelezoenValaszthatoTargyak() as $valaszthatoTargy) {
            $matches = array_filter(
                $this->erettsegiEredmenyek,
                function($eredmeny) use ($valaszthatoTargy) {
                    return $eredmeny->getTantargy()->getTantargyNev() == $valaszthatoTargy->getTantargyNev() && 
                        $eredmeny->getTantargy()->getSzint() == $valaszthatoTargy->getSzint();
                }
            );

            $keys = array_keys($matches);
            if (count($keys) > 0) {
                $results[] = $matches[$keys[0]];
            }
        }

        return $results;
    }

}