<?php
namespace App\OpenLiga\Entities;

class Score
{
    public $pointsTeam1;
    public $pointsTeam2;

    public function __construct(array $scoreData)
    {
        $this->pointsTeam1 = $scoreData['pointsTeam1'];
        $this->pointsTeam2 = $scoreData['pointsTeam2'];
    }
}
