<?php
namespace App\OpenLiga\Entities;

class TeamEmtpyRatio
{
    /**
     * @var Team
     */
    public $team;
    public $ratio = 'N/A';
    public $wins = 0;
    public $losses = 0;

    public function __construct(array $teamRatioData)
    {
        $this->team = $teamRatioData['team'];
    }
}
