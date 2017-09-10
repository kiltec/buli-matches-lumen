<?php
namespace App\OpenLiga\Entities;

class EmptyMatchList
{
    public $infoText;
    public $matches;

    public function __construct()
    {
        $this->infoText = 'No matches available.';
        $this->matches = [];
    }
}
