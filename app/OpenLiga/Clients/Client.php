<?php
namespace App\OpenLiga\Clients;

interface Client
{

    public function fetchCurrentSeason(): array;
}
