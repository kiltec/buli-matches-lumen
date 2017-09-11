<?php
namespace App\OpenLiga\Entities;

use Illuminate\Support\Collection;

class TeamRatioList
{
    public $name;
    /**
     * @var Collection
     */
    public $teamRatios;

    public function __construct(array $teamRatioListData)
    {
        $this->name = $teamRatioListData['name'];
        $this->teamRatios = $teamRatioListData['teamRatios'];
    }
}
