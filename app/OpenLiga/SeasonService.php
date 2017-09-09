<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\Season;
use App\OpenLiga\Entities\SeasonRound;

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
            ->map(function($item) {
                return new SeasonRound([
                    'name' => '',
                    'matches' => '',
                ]);
            });

        return new Season([
            'name' => $seasonName,
            'rounds' => $rounds,
        ]);
    }
}
