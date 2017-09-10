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

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function getSeason(int $year): Season
    {
        $allMatches = collect($this->client->fetchAllMatchesBySeason($year));

        $seasonBuilder = new SeasonBuilder();
        return $seasonBuilder->from($allMatches);
    }

    public function getUpcomingMatches(): MatchList
    {
        $currentRoundMatchData = $this->client->fetchCurrentRoundMatches();

        $seasonBuilder = new SeasonBuilder();
        $currentRound = $seasonBuilder->buildSeasonRound($currentRoundMatchData);
        /**
         * @var $currentRoundMatches Collection
         */
        $currentRoundMatches = $currentRound->matches;
        $currentUpcomingMatches = $currentRoundMatches->filter(function(Match $match){
            return $match->finished === false;
        });

        if($currentUpcomingMatches->isEmpty()) {
            $currentRoundId = $seasonBuilder->extractRoundId($currentRoundMatchData);
            $nextRoundMatchData = $this->client->fetchMatchesForRound($currentRoundId + 1 );
        } else {
            $upcomingMatches = $currentUpcomingMatches;
        }

        return new MatchList([
            'infoText' => '',
            'matches' => null,
        ]);
    }
}
