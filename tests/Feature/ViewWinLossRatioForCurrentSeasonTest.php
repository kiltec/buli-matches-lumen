<?php
namespace Tests\Feature;

use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\TeamEmtpyRatio;
use App\OpenLiga\Entities\TeamRatio;
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
    public function user_sees_all_teams_but_empty_ratio_when_no_matches_played()
    {
        $season = 2012;

        $this->statisticsService
            ->shouldReceive('getWinLossRatios')
            ->with($season)
            ->andReturn(
                $this->aTeamRatioListWithNoRatios()
            )->once();

        $response = $this->get('/win-loss-ratios/' . $season);

        $response->assertResponseOk();
        $response->assertSee('#win-loss-ratios');
        $this->aTeamRatioListWithNoRatios()->each(function($teamRatio) use ($response) {
            $response->assertSee($teamRatio->team->name);
        });
        $this->assertSee('N/A');
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
        $this->aTeamRatioListWithRatios()->each(function ($teamRatio) use ($response) {
            $response->assertSee($teamRatio->team->name);
            $response->assertSee((string)$teamRatio->ratio);
            $response->assertSee((string)$teamRatio->wins);
            $response->assertSee((string)$teamRatio->losses);
        });

    }

    private function aTeamRatioListWithNoRatios(): Collection
    {
        return collect([
            new TeamEmtpyRatio(['team' => new Team(['name' => 'Hamburger SV'])]),
            new TeamEmtpyRatio(['team' => new Team(['name' => 'Bleiern Dümmchen'])]),
            new TeamEmtpyRatio(['team' => new Team(['name' => 'Pharmakusen'])]),
            new TeamEmtpyRatio(['team' => new Team(['name' => 'Werder Fischkopp'])]),
        ]);
    }

    private function aTeamRatioListWithRatios(): Collection {
        return collect([
            new TeamRatio(['team' => new Team(['name' => 'Hamburger SV']), 'ratio' => 1, 'wins' => 10, 'losses' => 9]),
            new TeamRatio(['team' => new Team(['name' => 'Bleiern Dümmchen']), 'ratio' => 0.5, 'wins' => 8, 'losses' => 7]),
            new TeamRatio(['team' => new Team(['name' => 'Pharmakusen']), 'ratio' => 0.25, 'wins' => 6, 'losses' => 5]),
            new TeamRatio(['team' => new Team(['name' => 'Werder Fischkopp']), 'ratio' => 0.1, 'wins' => 4, 'losses' => 3]),
        ]);

    }
}
