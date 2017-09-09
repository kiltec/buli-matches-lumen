<?php
namespace App\OpenLiga\Entities;

class SeasonRound
{
    public $name;
    public $matches;

    public function __construct(array $roundData)
    {
        $this->name = $roundData['name'];
        $this->matches = $roundData['matches'];
    }
}
