<?php

namespace OH\Model;

use OH\Helper\KeyChecker;

class Szak
{

    use KeyChecker;

    private $instituteName;
    private $faculty;
    private $course;

    public function __construct(string $s1, string $s2, string $s3)
    {
        $this->instituteName = $s1;
        $this->faculty = $s2;
        $this->course = $s3;
    }

    public static function instanceFrom(array $array):  ? Szak
    {

        if (self::hasKeys($array, array('egyetem', 'kar', 'szak'))) {
            $inst = $array['egyetem'];
            $faculty = $array['kar'];
            $course = $array['szak'];

            if (!is_null($inst) && !is_null($faculty) && !is_null($course)) {
                return new Szak($inst, $faculty, $course);
            }
        }

        return null;
    }
}
