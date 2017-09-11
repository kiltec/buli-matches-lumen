<?php
namespace App\OpenLiga\Entities;

class EmptyTeamRatioList
{
    public $name = 'No Win/Loss Data Available';
    public $teamRatios;

    public function __construct()
    {
        $this->teamRatios = collect([]);
    }
}
