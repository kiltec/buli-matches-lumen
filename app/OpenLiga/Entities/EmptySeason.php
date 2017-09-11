<?php
namespace App\OpenLiga\Entities;

class EmptySeason extends Season
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'No matches available',
            'rounds' => [],
        ]);
    }
}
