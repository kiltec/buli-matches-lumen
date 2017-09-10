<?php
namespace App\OpenLiga\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SeasonBuilder
{
    public function from(Collection $allMatches): Season
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

    public function buildSeasonRound($matches): SeasonRound
    {
        return new SeasonRound([
            'name' => $this->extractRoundName($matches),
            'matches' => $this->buildMatches($matches),
        ]);
    }

    public function extractRoundName($matches):string
    {
        return array_get($matches[0], 'Group.GroupName');
    }

    public function extractRoundId($matches):int
    {
        return (int)array_get($matches[0], 'Group.GroupOrderID');
    }

    private function buildMatches($matches): Collection
    {
        return collect($matches)->map(function (array $matchData) {
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

    private function buildMatchResults(array $matchData, bool $matchIsFinished)
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

    public function extractSeasonName(Collection $allMatches): string
    {
        $seasonName = $allMatches->first()['LeagueName'];
        return $seasonName;
    }

    /**
     * There doesn't seem to be another way to get an ID of a season.
     * There is the LeagueId, which might be an unique ID for a season.
     * However, it isn't used for many API calls, especially not for getting
     * the game day data of a given season.
     * We cannot simply take the current date at the time of a request because in a given year, there
     * are two seasons and using the current year early in the year, will refer to the following season...
     *
     * Maybe I don't see something obvious but here we go and get ugly by extracting the season ID from
     * the season name...
     */
    public function extractSeasonId(array $currentRoundMatchData): int
    {
        $seasonName = $this->extractSeasonName(collect($currentRoundMatchData));
        preg_match('#(\d{4})/\d{4}#', $seasonName, $matches);

        return $matches[1];
    }
}
