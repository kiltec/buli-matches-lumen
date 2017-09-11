<?php

namespace App\Http\Controllers;

use App\OpenLiga\SeasonService;

class LeagueStatisticsController extends Controller
{
    /**
     * @var SeasonService
     */
    private $seasonService;

    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }

    public function winLossRatios()
    {
        $teamRatioList = $this->seasonService->getWinLossRatios();

        return view('statistics.win-loss-ratios', ['teamRatioList' => $teamRatioList]);
    }
}
