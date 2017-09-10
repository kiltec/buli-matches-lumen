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

}
