<?php
namespace Tests\Integration\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Clients\HttpClient;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchList;
use App\OpenLiga\Entities\MatchResults;
use App\OpenLiga\Entities\Score;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\UnknownSeason;
use App\OpenLiga\SeasonService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class SeasonServiceTest extends TestCase
{
    const SOME_YEAR = 2015;

    /**
     * @test
     * @group external
     */
    public function current_season_has_correct_name()
    {
        $seasonService = new SeasonService($this->aClient());

        $currentSeason = $seasonService->getSeason(self::SOME_YEAR);
        $this->assertEquals('1. Fußball-Bundesliga 2015/2016', $currentSeason->name);
    }

    /**
     * @test
     * @group external
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
     * @group external
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
     * @group external
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
     * @group external
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

    /**
     * @test
     */
    public function it_returns_empty_match_list_when_no_upcoming_current_matches_and_no_next_round_available()
    {
        $openLigaClient = Mockery::mock(Client::class);
        $openLigaClient
            ->shouldReceive('fetchCurrentRoundMatches')
            ->andReturn(
                $this->aRoundWithNoUpcomingMatches()
            )->once();

        $openLigaClient
            ->shouldReceive('fetchMatchesForRound')
            ->never();

        $seasonService = new SeasonService($openLigaClient);
        $seasonService->setMaxRounds(3);
        $matchList = $seasonService->getUpcomingMatches();

        $this->assertInstanceOf(EmptyMatchList::class, $matchList);
    }

    /**
     * @test
     */
    public function it_returns_correct_match_list_when_upcoming_matches_in_current_round_available()
    {
        $openLigaClient = Mockery::mock(Client::class);
        $openLigaClient
            ->shouldReceive('fetchCurrentRoundMatches')
            ->andReturn(
                $this->aRoundWithUpcomingMatches()
            )->once();

        $openLigaClient
            ->shouldReceive('fetchMatchesForRound')
            ->never();

        $seasonService = new SeasonService($openLigaClient);
        $matchList = $seasonService->getUpcomingMatches();

        $this->assertInstanceOf(MatchList::class, $matchList);
        $this->assertEquals('1. Fußball-Bundesliga 2017/2018 - 3. Spieltag', $matchList->infoText);
        $this->assertTrue($matchList->matches->isNotEmpty());
        $this->assertInstanceOf(Match::class, $matchList->matches->first());
    }

    /**
     * @test
     */
    public function it_requests_matches_from_next_round_when_no_upcoming_current_round_matches()
    {
        $openLigaClient = Mockery::mock(Client::class);
        $openLigaClient
            ->shouldReceive('fetchCurrentRoundMatches')
            ->andReturn(
                $this->aRoundWithNoUpcomingMatches()
            )->once();

        $openLigaClient
            ->shouldReceive('fetchMatchesForRound')
            ->with(4)
            ->andReturn($this->aRoundWithUpcomingMatchesFromNextRound())
            ->once();

        $seasonService = new SeasonService($openLigaClient);
        $matchList = $seasonService->getUpcomingMatches();

        $this->assertInstanceOf(MatchList::class, $matchList);
    }

    protected function aClient()
    {
        return new HttpClient();
    }

    private function aRoundWithNoUpcomingMatches()
    {
        return json_decode('[
  {
    "MatchID": 45461,
    "MatchDateTime": "2017-09-08T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-08T18:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-08T22:22:07.21",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75874,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75875,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 2,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60632,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 67,
        "GoalGetterID": 16054,
        "GoalGetterName": "Naby Keita",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60633,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 75,
        "GoalGetterID": 16080,
        "GoalGetterName": "Timo Werner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45462,
    "MatchDateTime": "2017-09-09T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-09T13:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:21:46.62",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75901,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75905,
        "ResultName": "Endergebnis",
        "PointsTeam1": 3,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60659,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 22,
        "GoalGetterID": 7038,
        "GoalGetterName": "Kohr",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60676,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 45,
        "GoalGetterID": 15370,
        "GoalGetterName": "Muto",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60701,
        "ScoreTeam1": 2,
        "ScoreTeam2": 1,
        "MatchMinute": 57,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60702,
        "ScoreTeam1": 3,
        "ScoreTeam2": 1,
        "MatchMinute": 71,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  }
]', true);
    }

    private function aRoundWithUpcomingMatches()
    {
        return json_decode('[
  {
    "MatchID": 45461,
    "MatchDateTime": "2017-09-08T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-08T18:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-08T22:22:07.21",
    "MatchIsFinished": false,
    "MatchResults": [
      {
        "ResultID": 75874,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75875,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 2,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60632,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 67,
        "GoalGetterID": 16054,
        "GoalGetterName": "Naby Keita",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60633,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 75,
        "GoalGetterID": 16080,
        "GoalGetterName": "Timo Werner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45462,
    "MatchDateTime": "2017-09-09T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-09T13:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:21:46.62",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75901,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75905,
        "ResultName": "Endergebnis",
        "PointsTeam1": 3,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60659,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 22,
        "GoalGetterID": 7038,
        "GoalGetterName": "Kohr",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60676,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 45,
        "GoalGetterID": 15370,
        "GoalGetterName": "Muto",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60701,
        "ScoreTeam1": 2,
        "ScoreTeam2": 1,
        "MatchMinute": 57,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60702,
        "ScoreTeam1": 3,
        "ScoreTeam2": 1,
        "MatchMinute": 71,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  }
]', true);
    }

    private function aRoundWithUpcomingMatchesFromNextRound()
    {
        return json_decode('[
  {
    "MatchID": 45461,
    "MatchDateTime": "2017-09-08T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "Next Round Liga 2017/2018",
    "MatchDateTimeUTC": "2017-09-08T18:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-08T22:22:07.21",
    "MatchIsFinished": false,
    "MatchResults": [
      {
        "ResultID": 75874,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75875,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 2,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60632,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 67,
        "GoalGetterID": 16054,
        "GoalGetterName": "Naby Keita",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60633,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 75,
        "GoalGetterID": 16080,
        "GoalGetterName": "Timo Werner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45462,
    "MatchDateTime": "2017-09-09T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-09T13:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:21:46.62",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75901,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75905,
        "ResultName": "Endergebnis",
        "PointsTeam1": 3,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60659,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 22,
        "GoalGetterID": 7038,
        "GoalGetterName": "Kohr",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60676,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 45,
        "GoalGetterID": 15370,
        "GoalGetterName": "Muto",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60701,
        "ScoreTeam1": 2,
        "ScoreTeam2": 1,
        "MatchMinute": 57,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60702,
        "ScoreTeam1": 3,
        "ScoreTeam2": 1,
        "MatchMinute": 71,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": null,
    "NumberOfViewers": null
  }
]', true);
    }
}
