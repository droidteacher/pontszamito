# Egyszerűsített Pontszámító Kalkulátor

Felvételi összpontszámot számol érettségi vizsga eredmények alapján.

## Installálás / Függőségek letöltése

    composer dump-autoload

## Összes teszt futtatása

    ./vendor/bin/phpunit --verbose tests

## Kalkulációs algoritmus

A számítást a `Kalkulator.php` osztályban található `calculate()` metódus indítja el. A kalkulátor példányosítható típusos modell objektumokkal a

    public function __construct(Array $eredmenyek, Array $nyelvvizsgak, Kovetelmeny $kovetelmeny)

 konstruktoron keresztül, vagy a 

    Kalkulator::instanceWithData($rawData)

statikus metódussal, amelynek direktben átadható a komplett adatstruktúra.





