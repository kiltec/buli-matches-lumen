<?php
namespace Tests\Features;

use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\SeasonDataService;
use Mockery;
use Tests\TestCase;

class ViewMatchListingTest extends TestCase
{

    private $seasonDataService;

    public function setUp()
    {
        parent::setUp();

        $this->seasonDataService = Mockery::mock(SeasonDataService::class);
        $this->app->instance(SeasonDataService::class, $this->seasonDataService);
    }
    /**
     * @test
     */
    public function user_can_view_list_of_all_matches_of_current_season()
    {
        $this->seasonDataService
            ->shouldReceive('getAllMatchesForCurrentSeason')
            ->andReturn(
                new Season([
                    'name' => '1. Fußball-Bundesliga 2017/2018',
                    'rounds' => collect([
                        new SeasonRound([
                            'name' => '1. Spieltag',
                            'matches' => collect([
                                new Match([
                                    'dateTime' => '2017-12-15 15:30',
                                    'finished' => true,
                                    'team1' => ["name" => "Hamburger SV"],
                                    'team2' => ["name" => "Bayern München"],
                                    "results" =>
                                        [
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                            [
                                                "pointsTeam1" => 10,
                                                "pointsTeam2" => 2,
                                            ]
                                        ],
                                ]),
                                new Match([
                                    'dateTime' => '2017-12-15 15:30',
                                    'finished' => true,
                                    'team1' => ["name" => "St. Pauli"],
                                    'team2' => ["name" => "Werder Bremen"],
                                    "results" =>
                                        [
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                            [
                                                "pointsTeam1" => 4,
                                                "pointsTeam2" => 2,
                                            ]
                                        ],
                                ]),
                            ])
                        ]),
                        new SeasonRound([
                            'name' => '2. Spieltag',
                            'matches' => collect([
                                new Match([
                                    'dateTime' => '2017-12-22 15:30',
                                    'finished' => false,
                                    'team1' => ["name" => "Hamburger SV"],
                                    'team2' => ["name" => "Bayern München"],
                                    "results" =>
                                        [
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                        ],
                                ]),
                                new Match([
                                    'dateTime' => '2017-12-22 15:30',
                                    'finished' => false,
                                    'team1' => ["name" => "St. Mauli"],
                                    'team2' => ["name" => "Lok Leipzig"],
                                    "results" =>
                                        [
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                            [
                                                "pointsTeam1" => 0,
                                                "pointsTeam2" => 0,
                                            ],
                                        ],
                                ]),
                            ])
                        ]),
                    ])
                ])
            )->once();

        $response = $this->get('/all-matches/');

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
}
