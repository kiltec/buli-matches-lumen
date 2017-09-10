<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchResults;
use App\OpenLiga\Entities\Score;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\Entities\UnknownSeason;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
    public function getSeason(int $year): Season
    {
        $allMatches = collect($this->client->fetchAllMatchesBySeason($year));

        return $this->buildSeason($allMatches);
    }

    private function buildSeason(Collection $allMatches): Season
    {
        if ($allMatches->isEmpty()) {
            return new UnknownSeason();
        }

        $seasonName = $this->extractSeasonName($allMatches);

        $rounds = $this->buildRounds($allMatches);

        return new Season([
            'name' => $seasonName,
            'rounds' => $rounds,
        ]);
    }

    private function buildRounds(Collection $allMatches)
    {
        $rounds = $allMatches
            ->groupBy('Group.GroupOrderID')
            ->map(function ($matches) {
                return $this->buildSeasonRound($matches);
            });
        return $rounds;
    }

    private function buildSeasonRound($matches): SeasonRound
    {
        return new SeasonRound([
            'name' => $this->extractRoundName($matches),
            'matches' => $this->buildMatches($matches),
        ]);
    }

    private function extractRoundName($matches)
    {
        return array_get($matches[0], 'Group.GroupName');
    }

    private function buildMatches($matches): Collection
    {
        return collect($matches)->map(function(array $matchData) {
            return $this->buildMatch($matchData);
        });
    }

    private function buildMatch(array $matchData): Match
    {
        $matchIsFinished = $matchData['MatchIsFinished'];

        return new Match([
            'dateTime' => Carbon::parse($matchData['MatchDateTime'])->toDateTimeString(),
            'finished' => $matchIsFinished,
            'team1' => new Team(['name' => $matchData['Team1']['TeamName']]),
            'team2' => new Team(['name' => $matchData['Team2']['TeamName']]),
            'results' => $this->buildMatchResults($matchData, $matchIsFinished),
        ]);
    }

    private function buildMatchResults(array $matchData, bool $matchIsFinished): MatchResults
    {
        if ($matchIsFinished) {
            $matchResults = new MatchResults([
                'finalScore' => $this->buildScores($matchData),
            ]);
        } else {
            $matchResults = null;
        }
        return $matchResults;
    }

    private function buildScores(array $matchData): Score
    {
        return new Score([
            // @todo do that more safely with checking result type
            'pointsTeam1' => array_last($matchData['MatchResults'])['PointsTeam1'],
            'pointsTeam2' => array_last($matchData['MatchResults'])['PointsTeam2'],
        ]);
    }

    private function extractSeasonName(Collection $allMatches):string
    {
        $seasonName = $allMatches->first()['LeagueName'];
        return $seasonName;
    }
}
