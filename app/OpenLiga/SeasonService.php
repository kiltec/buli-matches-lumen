<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\MatchList;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonBuilder;
use Illuminate\Support\Collection;

class SeasonService
{
    /**
     * @var Client
     */
    private $client;
    private $maxRounds = 34;
    private $seasonBuilder;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->seasonBuilder = new SeasonBuilder();
    }

    public function setMaxRounds(int $newMax)
    {
        $this->maxRounds = $newMax;
    }

    public function getSeason(int $year): Season
    {
        $allMatches = collect($this->client->fetchAllMatchesBySeason($year));

        return $this->seasonBuilder->from($allMatches);
    }

    public function getUpcomingMatches(): MatchList
    {
        $currentRoundMatchData = $this->client->fetchCurrentRoundMatches();
        $seasonId = $this->seasonBuilder->extractSeasonId($currentRoundMatchData);
        $seasonName = $this->seasonBuilder->extractSeasonName(collect($currentRoundMatchData));
        $roundName = $this->seasonBuilder->extractRoundName($currentRoundMatchData);

        $upcomingMatches = $this->getUpcomingMatchesFromRoundData($currentRoundMatchData);

        if($upcomingMatches->isEmpty()) {
            $currentRoundId = $this->seasonBuilder->extractRoundId($currentRoundMatchData);
            $nextRoundId = $currentRoundId + 1;
            if($nextRoundId <= $this->maxRounds) {
                $nextRoundMatchData = $this->client->fetchMatchesForRound($nextRoundId, $seasonId);
                $seasonName = $this->seasonBuilder->extractSeasonName(collect($nextRoundMatchData));
                $roundName = $this->seasonBuilder->extractRoundName($nextRoundMatchData);
                $upcomingMatches = $this->getUpcomingMatchesFromRoundData($nextRoundMatchData);
            }
        }

        if($upcomingMatches->isEmpty()) {
            return new EmptyMatchList();
        }

        $matchListName = sprintf('%s - %s', $seasonName, $roundName);
        return new MatchList([
            'infoText' => $matchListName,
            'matches' => $upcomingMatches,
        ]);
    }

    private function getUpcomingMatchesFromRoundData($currentRoundMatchData): Collection
    {
        $currentRound = $this->seasonBuilder->buildSeasonRound($currentRoundMatchData);

        /**
         * @var $currentRoundMatches Collection
         */
        $currentRoundMatches = $currentRound->matches;
        return $currentRoundMatches->filter(function (Match $match) {
            return $match->finished === false;
        });
    }
}
