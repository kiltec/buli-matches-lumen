<?php
namespace Tests\Feature;

use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\MatchList;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\SeasonService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

/**
 * @group tdd
 */
class ViewUpcomingMatchesTest extends TestCase
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
    public function user_views_list_when_no_upcoming_matches_available()
    {
        $this->seasonService
            ->shouldReceive('getUpcomingMatches')
            ->andReturn(
                new EmptyMatchList()
            )->once();

        $response = $this->get('/upcoming-matches');

        $response->assertResponseOk();
        $response->assertSee('Upcoming Matches');
        $response->assertSee('No matches available.');
        $response->assertDontSee('#upcoming-matches');
        $response->assertDontSee('-:-');
    }

    /**
     * @test
     */
    public function user_views_list_when_upcoming_matches_available()
    {
        $this->seasonService
            ->shouldReceive('getUpcomingMatches')
            ->andReturn(
                $this->aMatchList()
            )->once();

        $response = $this->get('/upcoming-matches');

        $response->assertResponseOk();
        $response->assertSee('1. Fußball-Bundesliga 2017/2018 - 3. Spieltag');
        $response->assertSee('2017-12-22 15:30');
        $response->assertSee('Hamburger SV');
        $response->assertSee('-:-');
        $response->assertSee('2017-14-22 14:30');
        $response->assertSee('Schnerder Krämen');
    }

    private function aMatchList(): MatchList
    {
        $matches = collect([
            new Match([
                'dateTime' => '2017-12-22 15:30',
                'finished' => false,
                'team1' => new Team(["name" => "Hamburger SV"]),
                'team2' => new Team(["name" => "Bayern München"]),
                "results" => null,
            ]),
            new Match([
                'dateTime' => '2017-13-22 20:30',
                'finished' => false,
                'team1' => new Team(["name" => "St. Schnauli"]),
                'team2' => new Team(["name" => "VfB Duttgart"]),
                "results" => null,
            ]),
            new Match([
                'dateTime' => '2017-14-22 14:30',
                'finished' => false,
                'team1' => new Team(["name" => "Schnerder Krämen"]),
                'team2' => new Team(["name" => "RotBock Leipzig"]),
                "results" => null,
            ])
        ]);
        return new MatchList([
            'infoText' => '1. Fußball-Bundesliga 2017/2018 - 3. Spieltag',
            'matches' => $matches
        ]);
    }

}
