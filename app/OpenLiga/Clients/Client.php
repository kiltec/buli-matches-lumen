<?php
namespace App\OpenLiga\Clients;

interface Client
{
    public function fetchAllMatchesBySeason(int $season): array;
}
