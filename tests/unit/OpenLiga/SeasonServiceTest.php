<?php
namespace Tests\Unit\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\SeasonService;
use Mockery;
use Tests\TestCase;

class SeasonServiceTest extends TestCase
{
    private $openLigaClient;

    public function setUp()
    {
        parent::setup();

        $this->openLigaClient = Mockery::mock(Client::class);
        $this->openLigaClient
            ->shouldReceive('fetchCurrentSeason')
            ->andReturn(
                $this->seasonData()
            )->once();
    }


    /**
     * @test
     */
    public function current_season_has_correct_name()
    {
        $seasonService = new SeasonService($this->openLigaClient);

        $currentSeason = $seasonService->getCurrentSeason();
        $this->assertEquals('1. Fußball-Bundesliga 2017/2018', $currentSeason->name);
    }

    /**
     * @test
     */
    public function current_season_has_rounds()
    {
        $seasonService = new SeasonService($this->openLigaClient);

        $currentSeason = $seasonService->getCurrentSeason();
        $roundsCollection = collect($currentSeason->rounds);

        $this->assertFalse(
            $roundsCollection->isEmpty(),
            'Current season has no rounds!'
        );

        $this->assertEmpty(
            $roundsCollection->first(function ($item) {
                return $item instanceof SeasonRound === false;
            }),
            'Rounds collection contains items which are not rounds!'
        );

        $this->assertEquals(7, count($roundsCollection), 'Incorrect amount of rounds!');
    }

    /**
     * @test
     */
    public function current_season_rounds_have_names()
    {
        $seasonService = new SeasonService($this->openLigaClient);

        $currentSeason = $seasonService->getCurrentSeason();
        $roundsCollection = collect($currentSeason->rounds);

        $this->assertEmpty(
            $roundsCollection->first(function ($round) {
                return strlen($round->name) === 0;
            }),
            'Rounds collection contains rounds without names!'
        );

        $this->assertEquals(
            '1. Spieltag',
            $roundsCollection->first()->name
        );

        $this->assertEquals(
            '7. Spieltag',
            $roundsCollection->last()->name
        );
    }

    /**
     * This is not a full season but only up to round 7.
     * Should be enough to be meaningful.
     */
    private function seasonData(): array
    {
        return json_decode('[
  {
    "MatchID": 45437,
    "MatchDateTime": "2017-08-18T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-18T18:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:31:32.66",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75506,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75507,
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
        "GoalID": 60231,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 9,
        "GoalGetterID": 16184,
        "GoalGetterName": "Niklas Süle",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60239,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 19,
        "GoalGetterID": 16263,
        "GoalGetterName": "Tolisso",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60244,
        "ScoreTeam1": 3,
        "ScoreTeam2": 0,
        "MatchMinute": 52,
        "GoalGetterID": 1478,
        "GoalGetterName": "Lewandowski",
        "IsPenalty": true,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60245,
        "ScoreTeam1": 3,
        "ScoreTeam2": 1,
        "MatchMinute": 66,
        "GoalGetterID": 15849,
        "GoalGetterName": " Admir Mehmedi",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 34,
      "LocationCity": "München",
      "LocationStadium": "Allianz Arena"
    },
    "NumberOfViewers": 75000
  },
  {
    "MatchID": 45438,
    "MatchDateTime": "2017-08-19T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "Team2": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:31:46.513",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75537,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75550,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60274,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 84,
        "GoalGetterID": 16104,
        "GoalGetterName": "Andrej Kramaric",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 24,
      "LocationCity": "Sinsheim",
      "LocationStadium": "Rhein Neckar Arena"
    },
    "NumberOfViewers": 30150
  },
  {
    "MatchID": 45439,
    "MatchDateTime": "2017-08-19T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "Team2": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:31:58.76",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75538,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75551,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60270,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 46,
        "GoalGetterID": 14005,
        "GoalGetterName": "Mathew Leckie",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60271,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 62,
        "GoalGetterID": 14005,
        "GoalGetterName": "Mathew Leckie",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 22,
      "LocationCity": "Berlin",
      "LocationStadium": "Olympiastadion"
    },
    "NumberOfViewers": 44751
  },
  {
    "MatchID": 45443,
    "MatchDateTime": "2017-08-19T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "LastUpdateDateTime": "2017-08-20T17:32:08.747",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75539,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75552,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60263,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 8,
        "GoalGetterID": 1457,
        "GoalGetterName": "N. Müller",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 1030,
      "LocationCity": "Hamburg",
      "LocationStadium": "Volksparkstadion"
    },
    "NumberOfViewers": 49449
  },
  {
    "MatchID": 45444,
    "MatchDateTime": "2017-08-19T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:32:26.547",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75540,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75553,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60272,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 73,
        "GoalGetterID": 2509,
        "GoalGetterName": "Harnik",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 1081,
      "LocationCity": "Mainz",
      "LocationStadium": "Opel-Arena"
    },
    "NumberOfViewers": 28279
  },
  {
    "MatchID": 45445,
    "MatchDateTime": "2017-08-19T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "Team2": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:32:37.927",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75541,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 2,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75549,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 3,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60264,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 23,
        "GoalGetterID": 16077,
        "GoalGetterName": "Christian Pulisic",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60265,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 27,
        "GoalGetterID": 2021,
        "GoalGetterName": "Barta",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60273,
        "ScoreTeam1": 0,
        "ScoreTeam2": 3,
        "MatchMinute": 60,
        "GoalGetterID": 16033,
        "GoalGetterName": "Pierre-Emerick Aubameyang",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 20,
      "LocationCity": "Wolfsburg",
      "LocationStadium": "Volkswagen Arena "
    },
    "NumberOfViewers": 30000
  },
  {
    "MatchID": 45442,
    "MatchDateTime": "2017-08-19T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-19T16:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "Team2": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-20T17:32:52.623",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75564,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75583,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60299,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 44,
        "GoalGetterID": 16175,
        "GoalGetterName": "Nabil Bentaleb",
        "IsPenalty": true,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60311,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 73,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 33,
      "LocationCity": "Gelsenkirchen",
      "LocationStadium": "Veltins Arena"
    },
    "NumberOfViewers": 61435
  },
  {
    "MatchID": 45440,
    "MatchDateTime": "2017-08-20T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-20T13:30:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "Team2": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-24T11:18:25.063",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75595,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75608,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [],
    "Location": {
      "LocationID": 966,
      "LocationCity": "Freiburg",
      "LocationStadium": "Schwarzwald-Stadion"
    },
    "NumberOfViewers": 24000
  },
  {
    "MatchID": 45441,
    "MatchDateTime": "2017-08-20T18:00:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-20T16:00:00Z",
    "Group": {
      "GroupName": "1. Spieltag",
      "GroupOrderID": 1,
      "GroupID": 28947
    },
    "Team1": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "Team2": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "LastUpdateDateTime": "2017-08-24T11:19:08.55",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75610,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75612,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60344,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 49,
        "GoalGetterID": 14744,
        "GoalGetterName": "Elvedi",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 32,
      "LocationCity": "Mönchengladbach",
      "LocationStadium": "Borussia Park"
    },
    "NumberOfViewers": 54018
  },
  {
    "MatchID": 45448,
    "MatchDateTime": "2017-08-25T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-25T18:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "Team2": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:17:50.117",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75666,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 2,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75667,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 3,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60403,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 28,
        "GoalGetterID": 2242,
        "GoalGetterName": "An. Hahn",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60404,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 34,
        "GoalGetterID": 16032,
        "GoalGetterName": "Bobby Wood",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60412,
        "ScoreTeam1": 1,
        "ScoreTeam2": 2,
        "MatchMinute": 96,
        "GoalGetterID": 7384,
        "GoalGetterName": "Sörensen",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": true,
        "Comment": null
      },
      {
        "GoalID": 60414,
        "ScoreTeam1": 1,
        "ScoreTeam2": 3,
        "MatchMinute": 99,
        "GoalGetterID": 15374,
        "GoalGetterName": "Holtby, Lewis",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": true,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 427,
      "LocationCity": "Köln",
      "LocationStadium": "Rhein-Energie-Stadion"
    },
    "NumberOfViewers": 50000
  },
  {
    "MatchID": 45449,
    "MatchDateTime": "2017-08-26T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "LastUpdateDateTime": "2017-09-05T22:47:34.06",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75686,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75697,
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
        "GoalID": 60443,
        "ScoreTeam1": 0,
        "ScoreTeam2": 0,
        "MatchMinute": 72,
        "GoalGetterID": 14563,
        "GoalGetterName": "Robert Lewandowski",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60451,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 72,
        "GoalGetterID": 14563,
        "GoalGetterName": "Robert Lewandowski",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60452,
        "ScoreTeam1": 0,
        "ScoreTeam2": 2,
        "MatchMinute": 75,
        "GoalGetterID": 14563,
        "GoalGetterName": "Robert Lewandowski",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 492,
      "LocationCity": "Bremen",
      "LocationStadium": "Weser-Stadion"
    },
    "NumberOfViewers": 42100
  },
  {
    "MatchID": 45450,
    "MatchDateTime": "2017-08-26T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:19:42.77",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75687,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75698,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60427,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 22,
        "GoalGetterID": 16031,
        "GoalGetterName": "Daniel Didavi",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 183,
      "LocationCity": "Frankfurt",
      "LocationStadium": "Commerzbank-Arena"
    },
    "NumberOfViewers": 46000
  },
  {
    "MatchID": 45451,
    "MatchDateTime": "2017-08-26T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:20:53.67",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75688,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75699,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 2,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60429,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 32,
        "GoalGetterID": 14955,
        "GoalGetterName": "Wendell",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60430,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 47,
        "GoalGetterID": 16104,
        "GoalGetterName": "Andrej Kramaric",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60432,
        "ScoreTeam1": 2,
        "ScoreTeam2": 1,
        "MatchMinute": 49,
        "GoalGetterID": 16259,
        "GoalGetterName": "Karim Bellarabi\r\n",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60441,
        "ScoreTeam1": 2,
        "ScoreTeam2": 2,
        "MatchMinute": 90,
        "GoalGetterID": 16042,
        "GoalGetterName": "Mark Uth",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": true,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 28,
      "LocationCity": "Leverkusen",
      "LocationStadium": "BayArena"
    },
    "NumberOfViewers": 27106
  },
  {
    "MatchID": 45452,
    "MatchDateTime": "2017-08-26T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "Team2": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:21:27.07",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75689,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 2,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75700,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 2,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60425,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 1,
        "GoalGetterID": 15722,
        "GoalGetterName": "Alfred Finnbogason",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60426,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 7,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60428,
        "ScoreTeam1": 1,
        "ScoreTeam2": 2,
        "MatchMinute": 30,
        "GoalGetterID": 11837,
        "GoalGetterName": "Oscar Wendt",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60444,
        "ScoreTeam1": 2,
        "ScoreTeam2": 2,
        "MatchMinute": 89,
        "GoalGetterID": 0,
        "GoalGetterName": "",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 1079,
      "LocationCity": "Augsburg",
      "LocationStadium": "WWK-Arena"
    },
    "NumberOfViewers": 29243
  },
  {
    "MatchID": 45454,
    "MatchDateTime": "2017-08-26T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:22:05.59",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75690,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75701,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60440,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 53,
        "GoalGetterID": 1016,
        "GoalGetterName": "Badstuber",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 426,
      "LocationCity": "Stuttgart",
      "LocationStadium": "Mercedes-Benz-Arena"
    },
    "NumberOfViewers": 53150
  },
  {
    "MatchID": 45446,
    "MatchDateTime": "2017-08-26T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-26T16:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "Team2": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "LastUpdateDateTime": "2017-08-27T17:17:12.88",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75707,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75709,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60453,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 15,
        "GoalGetterID": 11397,
        "GoalGetterName": "Aubameyang",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60454,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 57,
        "GoalGetterID": 1007,
        "GoalGetterName": "Nuri Sahin",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 184,
      "LocationCity": "Dortmund",
      "LocationStadium": "Signal-Iduna-Park"
    },
    "NumberOfViewers": 80860
  },
  {
    "MatchID": 45447,
    "MatchDateTime": "2017-08-27T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-27T13:30:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "Team2": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "LastUpdateDateTime": "2017-08-27T19:32:20.787",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75748,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75751,
        "ResultName": "Endergebnis",
        "PointsTeam1": 4,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60502,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 23,
        "GoalGetterID": 2204,
        "GoalGetterName": "Niederlechner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60513,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 48,
        "GoalGetterID": 16080,
        "GoalGetterName": "Timo Werner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60514,
        "ScoreTeam1": 2,
        "ScoreTeam2": 1,
        "MatchMinute": 55,
        "GoalGetterID": 10920,
        "GoalGetterName": "Orban",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60515,
        "ScoreTeam1": 3,
        "ScoreTeam2": 1,
        "MatchMinute": 69,
        "GoalGetterID": 16080,
        "GoalGetterName": "Timo Werner",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60517,
        "ScoreTeam1": 4,
        "ScoreTeam2": 1,
        "MatchMinute": 77,
        "GoalGetterID": 2583,
        "GoalGetterName": "Bruma",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 478,
      "LocationCity": "Leipzig",
      "LocationStadium": "Red Bull Arena"
    },
    "NumberOfViewers": 39265
  },
  {
    "MatchID": 45453,
    "MatchDateTime": "2017-08-27T18:00:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-08-27T16:00:00Z",
    "Group": {
      "GroupName": "2. Spieltag",
      "GroupOrderID": 2,
      "GroupID": 28948
    },
    "Team1": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "LastUpdateDateTime": "2017-08-27T19:54:04.567",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75764,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75767,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60521,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 67,
        "GoalGetterID": 14635,
        "GoalGetterName": "Jonathas",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      }
    ],
    "Location": {
      "LocationID": 944,
      "LocationCity": "Hannover",
      "LocationStadium": "HDI-Arena"
    },
    "NumberOfViewers": 43000
  },
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
    "MatchID": 45457,
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
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "Team2": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:26:41.897",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75898,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75937,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45458,
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
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "Team2": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:26:30.24",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75899,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75903,
        "ResultName": "Endergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60657,
        "ScoreTeam1": 0,
        "ScoreTeam2": 1,
        "MatchMinute": 13,
        "GoalGetterID": 11758,
        "GoalGetterName": "Boateng, K.-P.",
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
    "MatchID": 45460,
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
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "Team2": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "LastUpdateDateTime": "2017-09-09T17:28:14.533",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75900,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75904,
        "ResultName": "Endergebnis",
        "PointsTeam1": 3,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60658,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 22,
        "GoalGetterID": 15722,
        "GoalGetterName": "Alfred Finnbogason",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60660,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 32,
        "GoalGetterID": 15722,
        "GoalGetterName": "Alfred Finnbogason",
        "IsPenalty": false,
        "IsOwnGoal": true,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60704,
        "ScoreTeam1": 3,
        "ScoreTeam2": 0,
        "MatchMinute": 94,
        "GoalGetterID": 15722,
        "GoalGetterName": "Alfred Finnbogason",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": true,
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
  },
  {
    "MatchID": 45463,
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
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "Team2": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T17:21:28.007",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75902,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 0,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75934,
        "ResultName": "Endergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 1,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60700,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 52,
        "GoalGetterID": 32,
        "GoalGetterName": "Didavi",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60703,
        "ScoreTeam1": 1,
        "ScoreTeam2": 1,
        "MatchMinute": 75,
        "GoalGetterID": 2509,
        "GoalGetterName": "Harnik",
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
    "MatchID": 45455,
    "MatchDateTime": "2017-09-09T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-09T16:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "Team2": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "LastUpdateDateTime": "2017-09-09T20:25:05.813",
    "MatchIsFinished": true,
    "MatchResults": [
      {
        "ResultID": 75947,
        "ResultName": "Halbzeitergebnis",
        "PointsTeam1": 1,
        "PointsTeam2": 0,
        "ResultOrderID": 1,
        "ResultTypeID": 1,
        "ResultDescription": "Ergebnis zur Halbzeit"
      },
      {
        "ResultID": 75948,
        "ResultName": "Endergebnis",
        "PointsTeam1": 2,
        "PointsTeam2": 0,
        "ResultOrderID": 2,
        "ResultTypeID": 2,
        "ResultDescription": "Ergebnis nach Spielende"
      }
    ],
    "Goals": [
      {
        "GoalID": 60715,
        "ScoreTeam1": 1,
        "ScoreTeam2": 0,
        "MatchMinute": 27,
        "GoalGetterID": 15596,
        "GoalGetterName": "Uth",
        "IsPenalty": false,
        "IsOwnGoal": false,
        "IsOvertime": false,
        "Comment": null
      },
      {
        "GoalID": 60716,
        "ScoreTeam1": 2,
        "ScoreTeam2": 0,
        "MatchMinute": 51,
        "GoalGetterID": 15596,
        "GoalGetterName": "Uth",
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
    "MatchID": 45456,
    "MatchDateTime": "2017-09-10T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-10T13:30:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "Team2": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:49:48.523",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45459,
    "MatchDateTime": "2017-09-10T18:00:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-10T16:00:00Z",
    "Group": {
      "GroupName": "3. Spieltag",
      "GroupOrderID": 3,
      "GroupID": 28949
    },
    "Team1": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "Team2": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:50:10.597",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45471,
    "MatchDateTime": "2017-09-15T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-15T18:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:50:42.287",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45464,
    "MatchDateTime": "2017-09-16T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-16T13:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "Team2": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:44:30.327",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45468,
    "MatchDateTime": "2017-09-16T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-16T13:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "LastUpdateDateTime": "2017-06-29T12:45:13.243",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45469,
    "MatchDateTime": "2017-09-16T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-16T13:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "LastUpdateDateTime": "2017-06-29T12:45:22.25",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45472,
    "MatchDateTime": "2017-09-16T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-16T13:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:46:00.917",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45466,
    "MatchDateTime": "2017-09-16T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-16T16:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "Team2": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:50:58.7",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45467,
    "MatchDateTime": "2017-09-17T13:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-17T11:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "Team2": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:51:14.197",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45470,
    "MatchDateTime": "2017-09-17T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-17T13:30:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:51:30.053",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45465,
    "MatchDateTime": "2017-09-17T18:00:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-17T16:00:00Z",
    "Group": {
      "GroupName": "4. Spieltag",
      "GroupOrderID": 4,
      "GroupID": 28950
    },
    "Team1": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "Team2": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "LastUpdateDateTime": "2017-07-12T14:52:28.973",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45476,
    "MatchDateTime": "2017-09-19T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-19T16:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "Team2": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:53:15.273",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45477,
    "MatchDateTime": "2017-09-19T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-19T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "Team2": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "LastUpdateDateTime": "2017-07-13T22:05:21.01",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45478,
    "MatchDateTime": "2017-09-19T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-19T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "Team2": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-13T22:05:24.937",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45481,
    "MatchDateTime": "2017-09-19T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-19T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "Team2": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-13T22:05:30.187",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45473,
    "MatchDateTime": "2017-09-20T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-20T16:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "Team2": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:53:48.75",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45474,
    "MatchDateTime": "2017-09-20T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-20T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:54:32.657",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45475,
    "MatchDateTime": "2017-09-20T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-20T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "Team2": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:54:45.683",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45479,
    "MatchDateTime": "2017-09-20T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-20T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:54:19.183",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45480,
    "MatchDateTime": "2017-09-20T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-20T18:30:00Z",
    "Group": {
      "GroupName": "5. Spieltag",
      "GroupOrderID": 5,
      "GroupID": 28951
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:54:04.463",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45482,
    "MatchDateTime": "2017-09-22T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-22T18:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "Team2": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:55:13.843",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45484,
    "MatchDateTime": "2017-09-23T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 1635,
      "TeamName": "RB Leipzig",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/20px-RB_Leipzig_2014_logo.svg.png"
    },
    "Team2": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:50:53.767",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45485,
    "MatchDateTime": "2017-09-23T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "Team2": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "LastUpdateDateTime": "2017-06-29T12:51:03.73",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45486,
    "MatchDateTime": "2017-09-23T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:51:14.817",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45488,
    "MatchDateTime": "2017-09-23T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "Team2": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:51:40.997",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45490,
    "MatchDateTime": "2017-09-23T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "LastUpdateDateTime": "2017-06-29T12:51:58.84",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45483,
    "MatchDateTime": "2017-09-23T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-23T16:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "Team2": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:55:29.633",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45489,
    "MatchDateTime": "2017-09-24T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-24T13:30:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 65,
      "TeamName": "1. FC Köln",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/1_FC_Koeln.gif"
    },
    "LastUpdateDateTime": "2017-07-12T14:55:48.673",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45487,
    "MatchDateTime": "2017-09-24T18:00:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-24T16:00:00Z",
    "Group": {
      "GroupName": "6. Spieltag",
      "GroupOrderID": 6,
      "GroupID": 28952
    },
    "Team1": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-07-12T14:56:04.837",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45495,
    "MatchDateTime": "2017-09-29T20:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-29T18:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 9,
      "TeamName": "FC Schalke 04",
      "ShortName": "Schalke",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Schalke_04.gif"
    },
    "Team2": {
      "TeamId": 6,
      "TeamName": "Bayer 04 Leverkusen",
      "ShortName": "Leverkusen",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f7/Bayer_Leverkusen_Logo.svg/20px-Bayer_Leverkusen_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-30T12:32:28.85",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45494,
    "MatchDateTime": "2017-09-30T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-30T13:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 87,
      "TeamName": "Borussia Mönchengladbach",
      "ShortName": "Mönchengladbach",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Borussia_M%C3%B6nchengladbach_logo.svg/12px-Borussia_M%C3%B6nchengladbach_logo.svg.png"
    },
    "Team2": {
      "TeamId": 55,
      "TeamName": "Hannover 96",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Hannover_96_Logo.svg/20px-Hannover_96_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:54:10.54",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45496,
    "MatchDateTime": "2017-09-30T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-30T13:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 91,
      "TeamName": "Eintracht Frankfurt",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Eintracht_Frankfurt_Logo.svg/20px-Eintracht_Frankfurt_Logo.svg.png"
    },
    "Team2": {
      "TeamId": 16,
      "TeamName": "VfB Stuttgart",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/18px-VfB_Stuttgart_1893_Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:54:29.25",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45497,
    "MatchDateTime": "2017-09-30T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-30T13:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 95,
      "TeamName": "FC Augsburg",
      "ShortName": "",
      "TeamIconUrl": "https://www.openligadb.de/images/teamicons/FC_Augsburg.gif"
    },
    "Team2": {
      "TeamId": 7,
      "TeamName": "Borussia Dortmund",
      "ShortName": "BVB",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/20px-Borussia_Dortmund_logo.svg.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:54:39.223",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45499,
    "MatchDateTime": "2017-09-30T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-30T13:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 131,
      "TeamName": "VfL Wolfsburg",
      "ShortName": "VfL Wolfsburg",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Logo-VfL-Wolfsburg.svg/20px-Logo-VfL-Wolfsburg.svg.png"
    },
    "Team2": {
      "TeamId": 81,
      "TeamName": "1. FSV Mainz 05",
      "ShortName": "Mainz 05",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/FSV_Mainz_05_Logo.png/20px-FSV_Mainz_05_Logo.png"
    },
    "LastUpdateDateTime": "2017-06-29T12:55:17.917",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45498,
    "MatchDateTime": "2017-09-30T18:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-09-30T16:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 100,
      "TeamName": "Hamburger SV",
      "ShortName": "HSV",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/HSV-Logo.svg/20px-HSV-Logo.svg.png"
    },
    "Team2": {
      "TeamId": 134,
      "TeamName": "Werder Bremen",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/SV-Werder-Bremen-Logo.svg/13px-SV-Werder-Bremen-Logo.svg.png"
    },
    "LastUpdateDateTime": "2017-08-30T12:33:14.353",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45493,
    "MatchDateTime": "2017-10-01T13:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-10-01T11:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 112,
      "TeamName": "SC Freiburg",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/de/thumb/f/f1/SC-Freiburg_Logo-neu.svg/14px-SC-Freiburg_Logo-neu.svg.png"
    },
    "Team2": {
      "TeamId": 123,
      "TeamName": "TSG 1899 Hoffenheim",
      "ShortName": "Hoffenheim",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/TSG_Logo-Standard_4c.png/17px-TSG_Logo-Standard_4c.png"
    },
    "LastUpdateDateTime": "2017-08-30T12:33:41.057",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  },
  {
    "MatchID": 45492,
    "MatchDateTime": "2017-10-01T15:30:00",
    "TimeZoneID": "W. Europe Standard Time",
    "LeagueId": 4153,
    "LeagueName": "1. Fußball-Bundesliga 2017/2018",
    "MatchDateTimeUTC": "2017-10-01T13:30:00Z",
    "Group": {
      "GroupName": "7. Spieltag",
      "GroupOrderID": 7,
      "GroupID": 28953
    },
    "Team1": {
      "TeamId": 54,
      "TeamName": "Hertha BSC",
      "ShortName": "",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Hertha_BSC_Logo_2012.svg/20px-Hertha_BSC_Logo_2012.svg.png"
    },
    "Team2": {
      "TeamId": 40,
      "TeamName": "Bayern München",
      "ShortName": "FC Bayern",
      "TeamIconUrl": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Logo_FC_Bayern_M%C3%BCnchen.svg/20px-Logo_FC_Bayern_M%C3%BCnchen.svg.png"
    },
    "LastUpdateDateTime": "2017-08-30T12:33:49.807",
    "MatchIsFinished": false,
    "MatchResults": [],
    "Goals": [],
    "Location": null,
    "NumberOfViewers": null
  }
]
', true);
    }
}
