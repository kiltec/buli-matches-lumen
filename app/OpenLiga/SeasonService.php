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
            ->map(function($matchData) {
                return new SeasonRound([
                    'name' => array_get($matchData[0], 'Group.GroupName'),
                    'matches' => '',
                ]);
            });

        return new Season([
            'name' => $seasonName,
            'rounds' => $rounds,
        ]);
    }
}
