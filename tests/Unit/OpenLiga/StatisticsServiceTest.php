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
}
