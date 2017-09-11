<?php
namespace Tests\Feature;

use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\MatchList;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\TeamEmtpyRatio;
use App\OpenLiga\SeasonService;
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
    private $seasonService;

    public function setUp()
    {
        parent::setUp();

        $this->seasonService = Mockery::mock(SeasonService::class);
        $this->app->instance(SeasonService::class, $this->seasonService);
    }

    /**
     * @test
     */
    public function user_see_all_teams_but_empty_ratio_when_no_matches_played()
    {
        $this->seasonService
            ->shouldReceive('getWinLossRatios')
            ->andReturn(
                $this->aTeamRatioListWithNoRatios()
            )->once();

        $response = $this->get('/win-loss-ratios');

        $response->assertResponseOk();
        $response->assertSee('#win-loss-ratios');
        $this->aTeamRatioListWithNoRatios()->each(function($teamRatio) use ($response) {
            $response->assertSee($teamRatio->team->name);
        });
        $this->assertSee('N/A');
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
}
