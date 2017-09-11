<?php
namespace App\OpenLiga\Entities;

class TeamRatio
{
    /**
     * @var Team
     */
    public $team;
    public $ratio;
    public $wins;
    public $losses;

    public function __construct(array $teamRatioData)
    {
        $this->team = $teamRatioData['team'];
        $this->ratio = $teamRatioData['ratio'];
        $this->wins = $teamRatioData['wins'];
        $this->losses = $teamRatioData['losses'];
    }
}
