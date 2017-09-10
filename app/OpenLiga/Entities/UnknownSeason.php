<?php
namespace App\OpenLiga\Entities;

class UnknownSeason extends Season
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Unknown Season',
            'rounds' => [],
        ]);
    }
}
