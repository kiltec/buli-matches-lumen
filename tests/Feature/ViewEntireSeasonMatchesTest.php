<?php
namespace Tests\Features;

use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchResults;
use App\OpenLiga\Entities\Score;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\UnknownSeason;
use App\OpenLiga\SeasonService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class ViewEntireSeasonMatchesTest extends TestCase
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
    public function user_can_view_list_of_all_matches_of_a_given_season()
    {
        $this->seasonService
            ->shouldReceive('getSeason')
            ->with($this->aYear())
            ->andReturn(
                $this->aSeason()
            )->once();

        $response = $this->get('/all-matches/' . $this->aYear());

        $response->assertResponseOk();
        $response->assertSee('1. Fußball-Bundesliga 2017/2018');
        $response->assertSee('1. Spieltag');
        $response->assertSee('2017-12-15 15:30');
        $response->assertSee('Hamburger SV');
        $response->assertSee('Werder Bremen');
        $response->assertSee('10:2');
        $response->assertSee('2. Spieltag');
        $response->assertSee('2017-12-22 15:30');
        $response->assertSee('St. Mauli');
        $response->assertSee('Lok Leipzig');
        $response->assertSee('-:-');
    }

    /**
     * @test
     */
    public function user_get_matches_for_current_season_when_requested_season_invalid()
    {
        $invalidSeason = 'invalid_season';
        $currentSeason = Carbon::now()->year;

        $this->seasonService
            ->shouldReceive('getSeason')
            ->with($currentSeason)
            ->andReturn(
                $this->aSeason()
            )->once();

        $response = $this->get('/all-matches/' . $invalidSeason);

        $response->assertResponseOk();
    }

    /**
     * @test
     */
    public function user_gets_empty_season_when_season_not_yet_scheduled()
    {
        $unscheduledSeason = Carbon::now()->addYears(10)->year;

        $this->seasonService
            ->shouldReceive('getSeason')
            ->with($unscheduledSeason)
            ->andReturn(
                new UnknownSeason()
            )->once();

        $response = $this->get('/all-matches/' . $unscheduledSeason);

        $response->assertResponseOk();
        $response->assertSee('Unknown Season');
    }

    /**
     * @return Season
     */
    protected function aSeason(): Season
    {
        return new Season([
            'name' => '1. Fußball-Bundesliga 2017/2018',
            'rounds' => collect([
                new SeasonRound([
                    'name' => '1. Spieltag',
                    'matches' => collect([
                        new Match([
                            'dateTime' => '2017-12-15 15:30',
                            'finished' => true,
                            'team1' => new Team(["name" => "Hamburger SV"]),
                            'team2' => new Team(["name" => "Bayern München"]),
                            "results" =>
                                new MatchResults([
                                    'finalScore' => new Score([
                                        "pointsTeam1" => 10,
                                        "pointsTeam2" => 2,
                                    ])
                                ]),
                        ]),
                        new Match([
                            'dateTime' => '2017-12-15 15:30',
                            'finished' => true,
                            'team1' => new Team(["name" => "St. Pauli"]),
                            'team2' => new Team(["name" => "Werder Bremen"]),
                            "results" =>
                                new MatchResults([
                                    'finalScore' => new Score([
                                        "pointsTeam1" => 4,
                                        "pointsTeam2" => 2,
                                    ])
                                ]),
                        ]),
                    ])
                ]),
                new SeasonRound([
                    'name' => '2. Spieltag',
                    'matches' => collect([
                        new Match([
                            'dateTime' => '2017-12-22 15:30',
                            'finished' => false,
                            'team1' => new Team(["name" => "Hamburger SV"]),
                            'team2' => new Team(["name" => "Bayern München"]),
                            "results" =>
                                new MatchResults([
                                    'finalScore' => new Score([
                                        "pointsTeam1" => 0,
                                        "pointsTeam2" => 0,
                                    ]),
                                ]),
                        ]),
                        new Match([
                            'dateTime' => '2017-12-22 15:30',
                            'finished' => false,
                            'team1' => new Team(["name" => "St. Mauli"]),
                            'team2' => new Team(["name" => "Lok Leipzig"]),
                            "results" =>
                                new MatchResults([
                                    'finalScore' => new Score([
                                        "pointsTeam1" => 0,
                                        "pointsTeam2" => 0,
                                    ]),
                                ]),
                        ]),
                    ])
                ]),
            ])
        ]);
    }

    /**
     * A year which is not the current year
     */
    protected function aYear(): int
    {
        return Carbon::now()->year + 1;
    }
}
