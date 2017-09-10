<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonBuilder;

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

    public function getUpcomingMatches(): EmptyMatchList
    {
        $currentRoundMatches = collect($this->client->fetchCurrentRoundMatches());

        if($currentRoundMatches->isEmpty()) {
            return new EmptyMatchList();
        }
    }
}
