<?php
namespace App\OpenLiga;

use App\OpenLiga\Entities\EmptySeason;
use App\OpenLiga\Entities\EmptyTeamRatioList;
use App\OpenLiga\Entities\TeamRatioList;

class StatisticsService
{
    /**
     * @var SeasonService
     */
    private $seasonService;

    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }

    public function getWinLossRatios($seasonId): TeamRatioList
    {
        $season = $this->seasonService->getSeason($seasonId);

        if($season instanceof EmptySeason) {
            return new EmptyTeamRatioList();
        }

        return new TeamRatioList(['name' => '', '']);
    }
}
