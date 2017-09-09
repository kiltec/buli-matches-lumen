<?php
namespace App\OpenLiga\Entities;

class MatchResults
{
    public $finalScore;

    public function __construct(array $resultsData)
    {
        $this->finalScore = $resultsData['finalScore'];
    }
}
