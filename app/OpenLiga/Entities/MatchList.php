<?php
namespace App\OpenLiga\Entities;

class MatchList
{
    public $infoText;
    public $matches;

    public function __construct(array $matchListData)
    {
        $this->infoText = $matchListData['infoText'];
        $this->matches = $matchListData['matches'];
    }
}
