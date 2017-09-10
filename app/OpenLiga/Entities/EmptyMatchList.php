<?php
namespace App\OpenLiga\Entities;

class EmptyMatchList extends MatchList
{
    public function __construct()
    {
        $this->infoText = 'No matches available.';
        $this->matches = [];
    }
}
