<?php
namespace Tests\Feature;

use App\OpenLiga\Entities\EmptyTeamRatioList;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\TeamEmtpyRatio;
use App\OpenLiga\Entities\TeamRatio;
use App\OpenLiga\Entities\TeamRatioList;
use App\OpenLiga\StatisticsService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

/**
 * @group tdd
 */
class ViewWinLossRatioForCurrentSeasonTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $statisticsService;

    public function setUp()
    {
        parent::setUp();

        $this->statisticsService = Mockery::mock(StatisticsService::class);
        $this->app->instance(StatisticsService::class, $this->statisticsService);
    }

    /**
     * @test
     */
    public function user_sees_ratio_list_name()
    {
        $season = 2012;

        $this->statisticsService
            ->shouldReceive('getWinLossRatios')
            ->with($season)
            ->andReturn(
                $this->aTeamRatioListWithRatios()
            )->once();

        $response = $this->get('/win-loss-ratios/' . $season);

        $response->assertResponseOk();
        $this->assertSee('1. Busball-Bundesliga 2017/2018 - Win/Loss-Ratios');
    }

    /**
     * @test
     */
    public function user_sees_no_ratio_list_when_no_ratios_available()
    {
        $season = 2012;

        $this->statisticsService
            ->shouldReceive('getWinLossRatios')
            ->with($season)
            ->andReturn(
                new EmptyTeamRatioList()
            )->once();

        $response = $this->get('/win-loss-ratios/' . $season);

        $response->assertResponseOk();
        $this->assertSee('No Win/Loss Data Available');
        $response->assertDontSee('#win-loss-ratios');
    }

    /**
     * @test
     */
    public function user_sees_the_ratios_for_each_team_when_matches_played()
    {
        $season = 2012;

        $this->statisticsService
            ->shouldReceive('getWinLossRatios')
            ->with($season)
            ->andReturn(
                $this->aTeamRatioListWithRatios()
            )->once();

        $response = $this->get('/win-loss-ratios/' . $season);

        $response->assertResponseOk();
        $response->assertSee('#win-loss-ratios');
        $this->aTeamRatioListWithRatios()->teamRatios->each(function ($teamRatio) use ($response) {
            $response->assertSee($teamRatio->team->name);
            $response->assertSee((string)$teamRatio->ratio);
            $response->assertSee((string)$teamRatio->wins);
            $response->assertSee((string)$teamRatio->losses);
        });

    }

    private function aTeamRatioListWithRatios(): TeamRatioList
    {
        $teamRatios = collect([
            new TeamRatio(['team' => new Team(['name' => 'Hamburger SV']), 'ratio' => 1, 'wins' => 10, 'losses' => 9]),
            new TeamRatio(['team' => new Team(['name' => 'Bleiern Dümmchen']), 'ratio' => 0.5, 'wins' => 8, 'losses' => 7]),
            new TeamRatio(['team' => new Team(['name' => 'Pharmakusen']), 'ratio' => 0.25, 'wins' => 6, 'losses' => 5]),
            new TeamRatio(['team' => new Team(['name' => 'Werder Fischkopp']), 'ratio' => 0.1, 'wins' => 4, 'losses' => 3]),
        ]);

        return new TeamRatioList(['name' => '1. Busball-Bundesliga 2017/2018 - Win/Loss-Ratios', 'teamRatios' => $teamRatios]);
    }
}
