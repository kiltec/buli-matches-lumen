<?php
namespace App\OpenLiga\Clients;

interface Client
{
    public function fetchAllMatchesBySeason(int $season): array;
    public function fetchCurrentRoundMatches(): array;
    public function fetchMatchesForRound(int $roundId, int $season): array;
}
