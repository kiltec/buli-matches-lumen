<?php
namespace Tests\Integration\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Clients\HttpClient;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchResults;
use App\OpenLiga\Entities\Score;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\UnknownSeason;
use App\OpenLiga\SeasonService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

/**
 * @group external
 */
class SeasonServiceTest extends TestCase
{
    const SOME_YEAR = 2015;

    /**
     * @test
     */
    public function current_season_has_correct_name()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);
        $this->assertEquals('1. Fußball-Bundesliga 2015/2016', $currentSeason->name);
    }

    /**
     * @test
     */
    public function current_season_has_rounds()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);
        $rounds = $currentSeason->rounds;

        $this->assertInstanceOf(Collection::class, $rounds, 'Rounds are no Collection!');

        $this->assertFalse(
            $rounds->isEmpty(),
            'Current season has no rounds!'
        );

        $this->assertEquals(34, count($rounds), 'Incorrect amount of rounds!');

        $this->assertEmpty(
            $rounds->first(function ($item) {
                return $item instanceof SeasonRound === false;
            }),
            'Rounds collection contains items which are not rounds!'
        );
    }

    /**
     * @test
     */
    public function current_season_rounds_have_names()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);
        $rounds = $currentSeason->rounds;

        $this->assertEmpty(
            $rounds->first(function ($round) {
                return strlen($round->name) === 0;
            }),
            'Rounds collection contains rounds without names!'
        );

        $this->assertEquals(
            '1. Spieltag',
            $rounds->first()->name
        );

        $this->assertEquals(
            '34. Spieltag',
            $rounds->last()->name
        );
    }

    /**
     * @test
     */
    public function current_season_rounds_have_matches()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);
        $rounds = $currentSeason->rounds;

        $matchesFirstRound = $rounds->first()->matches;
        $matchesLastRound = $rounds->last()->matches;

        $this->assertInstanceOf(Collection::class, $matchesFirstRound, 'Matches of first round are no Collection!');
        $this->assertInstanceOf(Collection::class, $matchesLastRound, 'Matches of last round are no Collection!');

        $this->assertFalse(
            $matchesFirstRound->isEmpty(),
            'First round has no matches!'
        );

        $this->assertFalse(
            $matchesLastRound->isEmpty(),
            'Last round has no matches!'
        );

        $this->assertEquals(9, count($matchesFirstRound), 'Incorrect amount of matches for first round!');
        $this->assertEquals(9, count($matchesLastRound), 'Incorrect amount of matches for last round!');

        $this->assertEmpty(
            $matchesFirstRound->first(function ($item) {
                return $item instanceof Match === false;
            }),
            'Matches collection contains items which are not matches!'
        );
    }

    /**
     * @test
     */
    public function current_season_matches_have_correct_data()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);

        /**
         * @var $matchesFirstRound Collection
         */
        $matchesFirstRound = $currentSeason->rounds->first()->matches;

        $matchesFirstRound->each(function($match) {
            $this->assertTrue(strlen($match->dateTime) > 0, 'No dateTime set for match!');
            $this->assertInternalType( 'bool', $match->finished, 'Property finished is no bool!');
            $this->assertInstanceOf( Team::class, $match->team1, 'team1 is no Team!');
            $this->assertInstanceOf( Team::class, $match->team2, 'team2 is no Team!');
            if ($match->finished) {
                $this->assertInstanceOf( MatchResults::class, $match->results, 'results is no MatchResult!');
                $this->assertInstanceOf( Score::class, $match->results->finalScore, 'finalScore is no Score!');
                $this->assertInternalType( 'int', $match->results->finalScore->pointsTeam1, 'pointsTeam1 is no integer!');
                $this->assertInternalType( 'int', $match->results->finalScore->pointsTeam2, 'pointsTeam2 is no integer!');
            } else {
                $this->assertEquals(null, $match->results, 'results should be null!');
            }
        });

        $firstMatch = $matchesFirstRound->first();

        $this->assertEquals('2015-08-14 20:30:00', $firstMatch->dateTime);
        $this->assertEquals(true, $firstMatch->finished);
        $this->assertEquals('Bayern München', $firstMatch->team1->name);
        $this->assertEquals('Hamburger SV', $firstMatch->team2->name);
        $this->assertEquals('5', $firstMatch->results->finalScore->pointsTeam1);
        $this->assertEquals('0', $firstMatch->results->finalScore->pointsTeam2);

        $lastMatch = $currentSeason->rounds->last()->matches->last();

        $this->assertEquals('2016-05-14 15:30:00', $lastMatch->dateTime);
        $this->assertEquals(true, $lastMatch->finished);
        $this->assertEquals('1. FSV Mainz 05', $lastMatch->team1->name);
        $this->assertEquals('Hertha BSC', $lastMatch->team2->name);
        $this->assertEquals('5', $firstMatch->results->finalScore->pointsTeam1);
        $this->assertEquals('0', $firstMatch->results->finalScore->pointsTeam2);

    }

    /**
     * @test
     */
    public function unknown_season_when_season_unscheduled()
    {
        $unscheduledYear = 2031;

        $openLigaClient = Mockery::mock(Client::class);
        $openLigaClient
            ->shouldReceive('fetchAllMatchesBySeason')
            ->with($unscheduledYear)
            ->andReturn(
                []
            )->once();

        $seasonService = new SeasonService($openLigaClient);

        $season = $seasonService->getSeason($unscheduledYear);

        $this->assertInstanceOf(UnknownSeason::class, $season);
    }

    protected function aClient()
    {
        return new HttpClient();
    }
}
