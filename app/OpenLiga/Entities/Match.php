<?php
namespace App\OpenLiga\Entities;

class Match
{
    public $dateTime;
    /**
     * @var bool
     */
    public $finished;
    public $team1;
    public $team2;
    public $results;

    public function __construct(array $matchData)
    {
        $this->dateTime = $matchData['dateTime'];
        $this->finished = $matchData['finished'];
        $this->team1 = $matchData['team1'];
        $this->team2 = $matchData['team2'];
        $this->results = $matchData['results'];
    }
}
