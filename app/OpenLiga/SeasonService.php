<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchResults;
use App\OpenLiga\Entities\Score;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\Team;
use Illuminate\Support\Carbon;

class SeasonService
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function getCurrentSeason(): Season
    {
        $allMatches = collect($this->client->fetchCurrentSeason());

        $seasonName = $allMatches->first()['LeagueName'];

        $rounds = $allMatches
            ->groupBy('Group.GroupOrderID')
            ->map(function($matches) {
                return new SeasonRound([
                    'name' => array_get($matches[0], 'Group.GroupName'),
                    'matches' => collect($matches)->map(function($matchData){
                        // @todo Extract methods
                        $matchIsFinished = $matchData['MatchIsFinished'];
                        if($matchIsFinished) {
                            $matchResults = new MatchResults([
                                'finalScore' => new Score([
                                    // @todo do that properly with checking result type
                                    'pointsTeam1' => array_last($matchData['MatchResults'])['PointsTeam1'],
                                    'pointsTeam2' => array_last($matchData['MatchResults'])['PointsTeam2'],
                                ]),
                            ]);
                        } else {
                            $matchResults = null;
                        }

                        return new Match([
                            'dateTime' => Carbon::parse($matchData['MatchDateTime'])->toDateTimeString(),
                            'finished' => $matchIsFinished,
                            'team1' => new Team(['name' => $matchData['Team1']['TeamName']]),
                            'team2' => new Team(['name' => $matchData['Team2']['TeamName']]),
                            'results' => $matchResults,
                        ]);
                    }),
                ]);
            });

        return new Season([
            'name' => $seasonName,
            'rounds' => $rounds,
        ]);
    }
}
