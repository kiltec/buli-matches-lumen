<?php
namespace App\OpenLiga;

use App\OpenLiga\Entities\EmptySeason;
use App\OpenLiga\Entities\EmptyTeamRatioList;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\TeamRatioList;
use Illuminate\Support\Collection;

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

        $finishedMatches = $season->rounds->reduce(function(Collection $accumulator, SeasonRound $round) {
            $roundFinishedMatches = $round->matches->filter(function($match) {
                return $match->finished;
            });
            return $accumulator->merge($roundFinishedMatches);
        }, collect([]));

        if($finishedMatches->isEmpty()) {
            return new EmptyTeamRatioList();
        }

        return new TeamRatioList(['name' => '', 'teamRatios' => '']);
    }
}
