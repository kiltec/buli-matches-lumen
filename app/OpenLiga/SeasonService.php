<?php
namespace App\OpenLiga;

use App\OpenLiga\Clients\Client;
use App\OpenLiga\Entities\Season;

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

        return new Season([
            'name' => $seasonName,
            'rounds' => [],
        ]);
    }
}
