<?php
/**
 * Created by IntelliJ IDEA.
 * User: lars
 * Date: 11.09.17
 * Time: 23:15
 */

namespace Unit\OpenLiga;

use App\OpenLiga\Entities\EmptySeason;
use App\OpenLiga\Entities\EmptyTeamRatioList;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\TeamEmtpyRatio;
use App\OpenLiga\Entities\TeamRatioList;
use App\OpenLiga\SeasonService;
use App\OpenLiga\StatisticsService;
use Mockery;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_empty_list_when_no_matches_available()
    {
        $seasonId = 2014;

        $seasonService = Mockery::mock(SeasonService::class);
        $seasonService
            ->shouldReceive('getSeason')
            ->with($seasonId)
            ->andReturn(
                new EmptySeason()
            )->once();

        $statisticsService = new StatisticsService($seasonService);
        $teamRatioList = $statisticsService->getWinLossRatios($seasonId);

        $this->assertInstanceOf(EmptyTeamRatioList::class, $teamRatioList);
    }

    /**
     * @test
     */
    public function it_returns_empty_list_when_no_matches_played_yet_but_available()
    {
        $seasonId = 2014;

        $round1MatchData = ['finished' => false, 'dateTime' => '', 'team1' => '', 'team2' => '', 'results' => ''];
        $round1Data = ['name' => 'round 1', 'matches' => collect([new Match($round1MatchData)])];

        $round2MatchData = ['finished' => false, 'dateTime' => '', 'team1' => '', 'team2' => '', 'results' => ''];
        $round2Data = ['name' => 'round 1', 'matches' => collect([new Match($round2MatchData)])];
        $rounds = collect([
            new SeasonRound($round1Data),
            new SeasonRound($round2Data),
        ]);
        $seasonData = ['name' => 'something', 'rounds' => $rounds];
        $seasonService = Mockery::mock(SeasonService::class);
        $seasonService
            ->shouldReceive('getSeason')
            ->with($seasonId)
            ->andReturn(
                new Season($seasonData)
            )->once();

        $statisticsService = new StatisticsService($seasonService);
        $teamRatioList = $statisticsService->getWinLossRatios($seasonId);

        $this->assertInstanceOf(EmptyTeamRatioList::class, $teamRatioList);
    }
}
