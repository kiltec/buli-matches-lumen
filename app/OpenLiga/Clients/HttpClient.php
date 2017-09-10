<?php
namespace App\OpenLiga\Clients;

use GuzzleHttp\Client as GuzzleClient;

class HttpClient implements Client
{
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = new GuzzleClient([
                'timeout' => 5.0,
        ]);
    }

    public function fetchAllMatchesBySeason(int $season): array
    {
        $response = $this->guzzle->request('GET', 'https://www.openligadb.de/api/getmatchdata/bl1/' . $season);

        return \GuzzleHttp\json_decode((string)$response->getBody(), true);
    }

    public function fetchCurrentRoundMatches(): array
    {
        $response = $this->guzzle->request('GET', 'https://www.openligadb.de/api/getmatchdata/bl1/');

        return \GuzzleHttp\json_decode((string)$response->getBody(), true);
    }

    public function fetchMatchesForRound(int $roundId, int $season): array
    {
        $uri = sprintf('https://www.openligadb.de/api/getmatchdata/bl1/%d/%d', $season, $roundId);
        $response = $this->guzzle->request('GET', $uri);

        return \GuzzleHttp\json_decode((string)$response->getBody(), true);
    }
}
