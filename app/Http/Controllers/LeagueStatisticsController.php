<?php

namespace App\Http\Controllers;

use App\OpenLiga\StatisticsService;

class LeagueStatisticsController extends Controller
{
    /**
     * @var StatisticsService
     */
    private $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function winLossRatios($year)
    {
        $teamRatioList = $this->statisticsService->getWinLossRatios($year);

        return view('statistics.win-loss-ratios', ['teamRatioList' => $teamRatioList]);
    }
}
